<?php 
/**
*
* aplicación para procesar una versión candidta como definitiva.
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

// funciones frecuentes
include($GeoGecPath."/includes/encabezado.php");
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php
$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


global $PROCESANDO;
$PROCESANDO='si';

$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';
function terminar($Log){
    $res=json_encode($Log);
    if($res==''){$res=print_r($Log,true);}
    echo $res;
    exit;
}

if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='err';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
$minAcc=2;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minAcc){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel '.$minAcc.' vs nivel '.$Acc.') para consultar un indicador. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}





if(!isset($_POST['idgeom'])){
	$Log['res']='err';
	$Log['tx'][]='falta la variable idgeom';	
	terminar($Log);
}
if(!isset($_POST['t1'])){
	$Log['res']='err';
	$Log['tx'][]='falta la variable t1';	
	terminar($Log);
}
if(!isset($_POST['n1'])){
	$Log['res']='err';
	$Log['tx'][]='falta la variable n1';	
	terminar($Log);
}


if(!isset($_POST['personalizados'])){
	$Log['res']='err';
	$Log['tx'][]='falta la variable personalizados';	
	terminar($Log);
}



$query="
SELECT 
	id, nombre, descripcion, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, 
	fechadesde, fechahasta, usu_autor, zz_borrada, zz_publicada, 
	col_texto1_nom, col_texto2_nom, col_texto3_nom, col_texto4_nom, col_texto5_nom, col_numero1_nom, col_numero2_nom, col_numero3_nom, col_numero4_nom, col_numero5_nom, col_texto1_unidad, col_texto2_unidad, col_texto3_unidad, col_texto4_unidad, col_texto5_unidad, col_numero1_unidad, col_numero2_unidad, col_numero3_unidad, col_numero4_unidad, col_numero5_unidad, representar_campo, representar_val_max, representar_val_min, zz_borrada_usu, zz_borrada_utime, col_texto6_nom, col_texto7_nom, col_texto8_nom, col_texto9_nom, col_texto10_nom
	FROM geogec.ref_rele_campa
	WHERE
	id = '".$_POST['idcampa']."'
	AND
	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	
$f=pg_fetch_assoc($Consulta);


if($f['id_p_ref_capasgeo']>0){
	
	$IdCapa=$f['id_p_ref_capasgeo'];

}else{
	
	$Log['res']='err';
	$Log['tx'][]='no se encontro la capa';	
	terminar($Log);
	
}

if($_POST['n1']==''){$_POST['n1']='0';}

$query="
	INSERT INTO geogec.ref_rele_registros(
		id_p_ref_rele_campa, 	zz_auto_crea_usu, 		zz_auto_crea_fechau, 
		col_texto1_dato, 		col_texto2_dato, 		col_texto3_dato, 	col_texto4_dato, 	col_texto5_dato, 	col_texto6_dato, 	col_texto7_dato, 	col_texto8_dato, 	col_texto9_dato, 	col_texto10_dato, 
		col_numero1_dato, 		col_numero2_dato, 		col_numero3_dato, 	col_numero4_dato, 	col_numero5_dato, 	col_numero6_dato, 	col_numero7_dato, 	col_numero8_dato, 	col_numero9_dato, 	col_numero10_dato, 
		id_p_ref_capas_registros
		)
	VALUES (
		'".$_POST['idcampa']."', '".$idUsuario."', 		'".time()."', 
		'".$_POST['t1']."', 	'', 					'', 				'', 				'', 				'', 				'', 				'', 				'', 				'', 
		'".$_POST['n1']."', 	null, 					null, 				null, 				null, 				null, 				null, 				null, 				null, 				null, 
		'".$_POST['idgeom']."'
		)
	RETURNING id
		
";
//$Log['tx'][]=$query;
$Consulta = pg_query($ConecSIG,utf8_encode($query));
$row=pg_fetch_assoc($Consulta);
$nid=$row['id'];
if($nid<1){
	$Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=$query;
}

$Log['data']['nid']=$nid;
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	

$query="
UPDATE 
	geogec.ref_rele_registros
	SET
		zz_superado='1',
		zz_auto_supera_id='".$nid."'	
	WHERE
		zz_superado='0'
		AND
		zz_borrado='0'
		AND
		id_p_ref_rele_campa='".$_POST['idcampa']."'
		AND
		id_p_ref_capas_registros='".$_POST['idgeom']."'
		AND
		id!='".$nid."'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
$row=pg_fetch_assoc($Consulta);
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	



$query="
	SELECT 
		id, 
		nombre, 
		inputattributes, opciones, unidaddemedida, tipo
	FROM 
		geogec.ref_rele_campos
	WHERE
		id_p_ref_rele_campa = '".$_POST['idcampa']."'
	AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		zz_borrada='0'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	
$Campos=Array();
while($row=pg_fetch_assoc($Consulta)){
	$Campos[$row['id']]=$row;
}



foreach($_POST['personalizados'] as $k => $v){
	$_POST['personalizados'][$k]=$v;   //encode?
}

foreach($_POST['personalizados'] as $k => $v){
	
	$_POST['personalizados'][$k]=$v;   //encode?
	
	
	if(!isset($Campos[$k])){
		    $Log['res']='err';
		    $Log['tx'][]='error al buscar el campo id: '.$k.' entre los campos registrados para la campaña de relevamiento id: '.$_POST['idcampa'];
		    $Log['mg'][]='error al buscar el campo id: '.$k.' entre los campos registrados para la campaña de relevamiento id: '.$_POST['idcampa'];
		    terminar($Log);
	}
	
	if($Campos[$k]['tipo']=='texto'){
		
		$defcampo="
			data_texto,  
			data_numero,
			data_documento
		";
		$setcampo="
			'".$v."',
			null,
			null
		";
		
	}elseif($Campos[$k]['tipo']=='numero'){
		if($v===null||$v===''){
			$v= "null";
		}
		$defcampo="
			data_texto,  
			data_numero,
			data_documento
		";
		$setcampo="
			null,
			".$v.",
			null
		";
	}elseif($Campos[$k]['tipo']=='coleccion_imagenes'){
		$defcampo="
			data_texto,  
			data_numero,
			data_documento
		";
		$setcampo="
			null,
			null,
			'".$v."'
		";
	}
	
	
	$query="
		INSERT INTO 
		geogec.ref_rele_registros_datos(
			
				ic_p_est_02_marcoacademico,
				id_p_ref_rele_campa, 
				id_p_ref_rele_campos, 
				id_p_ref_rele_registros, 
				$defcampo
				
		)VALUES (
			'".$_POST['codMarco']."',
			'".$_POST['idcampa']."', 
			'".$k."',
			'".$nid."',
			$setcampo
		)
		RETURNING id
		
	";
	//$Log['tx'][]=$query;
	$Consulta = pg_query($ConecSIG,utf8_encode($query));
	$row=pg_fetch_assoc($Consulta);
	$nid_rc=$row['id'];
	$Log['data']['registroscampos_nids'][]=$nid_rc;
	//$Log['tx'][]=$query;
	   
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['res']='error';
	    $Log['tx'][]='error al insertar registro-campo en la base de datos';
	    
	    //Something to write to txt log
		$log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.      
        "User: ".$idUsuario.PHP_EOL.
        "-------------------------".PHP_EOL.
		print_r($Log['tx'],true);
		;
		//Save string to log, use FILE_APPEND to append.
		file_put_contents($GeoGecPath.'/app_rele/logs/err_'.date("j.n.Y").'.log', $log);	    
	    
	    $Log['tx'][]=pg_errormessage($ConecSIG);
	    $Log['tx'][]=utf8_encode($query);
	    terminar($Log);
	}

	$log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
    "User: ".$idUsuario.PHP_EOL.
    "-------------------------".PHP_EOL.
	print_r($Log['tx'],true);
	;				
	//Save string to log, use FILE_APPEND to append.
	file_put_contents($GeoGecPath.'/app_rele/logs/err_'.date("j.n.Y").'.log', $log);
    
		
	$query="
	
		UPDATE 
			geogec.ref_rele_registros_datos
		SET
			zz_superado='1',
			zz_auto_supera_id='".$nid_rc."'
			
		FROM 
			geogec.ref_rele_registros
		WHERE
			ref_rele_registros_datos.id_p_ref_rele_registros = ref_rele_registros.id
		AND
			ref_rele_registros.id != $nid
		AND
			ref_rele_registros.id_p_ref_capas_registros = '".$_POST['idgeom']."'
		AND
			ref_rele_registros_datos.zz_superado='0'
		AND
			ref_rele_registros_datos.zz_borrada='0'
		AND
			ref_rele_registros_datos.id_p_ref_rele_campa='".$_POST['idcampa']."'		
		AND
			ref_rele_registros_datos.id_p_ref_rele_campos='".$k."'
		AND
			ref_rele_registros_datos.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'		
	";
	//$Log['tx'][]=$query;
	$Consulta = pg_query($ConecSIG,utf8_encode($query));
	$row=pg_fetch_assoc($Consulta);
	$Log['data']['registroscampos_nids'][]=$nid;
	$Log['tx'][]=utf8_encode($query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['res']='error';
	    $Log['tx'][]='error al insertar registro-campo en la base de datos';
	    $Log['tx'][]=pg_errormessage($ConecSIG);
	    $Log['tx'][]=$query;
	    terminar($Log);
	}	
}



$Log['res']='exito';	
terminar($Log);
