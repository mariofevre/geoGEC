<?php 
/**
*
* aplicaci�n para procesar una versi�n candidta como definitiva.
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicaci�n se desarrollo sobre una publicaci�n GNU 2017 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (GPL-3.0)
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU General Public License" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificaci�n de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

// funciones frecuentes
include($GeoGecPath."/includes/encabezado.php");
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php
$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


ini_set('display_errors', 0);
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
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel '.$minAcc.' vs nivel '.$Acc.') para consultar un indicador. En el marco de investigaci�n c�digo '.$_POST['codMarco']);
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
	$_POST['personalizados']=array();
}


$Log['data']['archivar']=$_POST['archivar'];


if($_POST['id_registro']==''||$_POST['id_registro']=='undefined'){
	$query="
		INSERT INTO
			geogec.ref_rele_registros(
			
				id_p_ref_rele_campa,
				zz_auto_crea_usu
			)
		
			VALUES(
				'".$_POST['idcampa']."',
				'".$idUsuario."'
			
			)
		RETURNING	id			
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

	$fila=pg_fetch_assoc($Consulta);
	$_POST['id_registro']=$fila['id'];
	$Log['data']['registro_nuevo']='si';
}else{
	$Log['data']['registro_nuevo']='no';
}
	
	$Log['data']['id_registro']=$_POST['id_registro'];
	
	
$query="
	SELECT 
		id, id_p_ref_rele_campa, zz_auto_crea_usu, zz_superado, zz_borrado, col_texto1_dato, col_texto2_dato, col_texto3_dato, 
		col_texto4_dato, col_texto5_dato, col_texto6_dato, col_texto7_dato, col_texto8_dato, col_texto9_dato, col_texto10_dato, 
		col_numero1_dato, col_numero2_dato, col_numero3_dato, col_numero4_dato, col_numero5_dato, col_numero6_dato, col_numero7_dato, 
		col_numero8_dato, col_numero9_dato, col_numero10_dato, id_p_ref_capas_registros, zz_auto_supera_id, zz_auto_crea_fechau, 
		zz_archivada, zz_archivada_fecha
	FROM 
		geogec.ref_rele_registros
		
	WHERE
	id = '".$_POST['id_registro']."'
	AND
	id_p_ref_rele_campa ='".$_POST['idcampa']."'
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
$Registro=pg_fetch_assoc($Consulta);


if($Registro['zz_archivada_fecha']==''||$Registro['zz_archivada_fecha']==null){
	$Registro['zz_archivada_fecha']='0001-01-01';
}



$query="
SELECT 
	id, nombre, descripcion, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, 
	usu_autor, zz_borrada, zz_publicada
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
		id_p_ref_capas_registros,
		zz_archivada,
		zz_archivada_fecha
		)
	VALUES (
		'".$_POST['idcampa']."', '".$idUsuario."', 		'".time()."', 
		'".utf8_decode($_POST['t1'])."', 	'', 					'', 				'', 				'', 				'', 				'', 				'', 				'', 				'', 
		'".utf8_decode($_POST['n1'])."', 	null, 					null, 				null, 				null, 				null, 				null, 				null, 				null, 				null, 
		'".$_POST['idgeom']."',
		'".$Registro['zz_archivada']."',
		'".$Registro['zz_archivada_fecha']."'
		
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
		id='".$Registro['id']."'
		
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
	$_POST['personalizados'][$k]=utf8_decode($v);   //encode?
}

foreach($_POST['personalizados'] as $k => $v){
	
	$_POST['personalizados'][$k]=utf8_decode($v);   //encode?
	
	
	if(!isset($Campos[$k])){
		    $Log['res']='err';
		    $Log['tx'][]='error al buscar el campo id: '.$k.' entre los campos registrados para la campa�a de relevamiento id: '.$_POST['idcampa'];
		    $Log['mg'][]='error al buscar el campo id: '.$k.' entre los campos registrados para la campa�a de relevamiento id: '.$_POST['idcampa'];
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
		
		
		if(strpos($v, ',')!==false){		 
			if(strpos($v, '.')!==false){		 		
				if(strpos($v, '.') > strpos($v, ',')){		 
					$v=str_replace(',','',$v);
				}else{
					$v=str_replace('.','',$v);
					$v=str_replace(',','.',$v);					
				}
			}else{
				$v=str_replace(',','.',$v);	
			}
		}
		
		$setcampo="
			null,
			".(float)$v.",
			null
		";
	}elseif($Campos[$k]['tipo']=='fecha'){
		if($v===null||$v===''){
			$v= "null";
		}
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
			ref_rele_registros.id='".$Registro['id']."'			
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
