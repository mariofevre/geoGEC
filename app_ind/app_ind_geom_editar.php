<?php
/**
* 
* @package    	geoGEC
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicaci�n se desarroll� sobre una publicaci�n GNU 2017 TReCC SA
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

ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";
include($GeoGecPath.'/includes/encabezado.php');
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

$Hoy_a = date("Y");
$Hoy_m = date("m");
$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

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

if(!isset($_POST['geometria'])){
	$Log['tx'][]='falta geometria';
	terminar($Log);		
}
if(!isset($_POST['idCapa_registro_duplica'])){
	$Log['tx'][]='falta id duplicacion';
	terminar($Log);		
}

if(!isset($_POST['idCapa_registro_elimina'])){
	$Log['tx'][]='falta id elim';
	terminar($Log);		
}


if(!isset($_POST['idCapa'])){
	$Log['tx'][]='falta capa';
	terminar($Log);		
}

if(!isset($_POST['idIndicador'])){
	$Log['tx'][]='falta indicador';
	terminar($Log);		
}

if(!isset($_POST['renombre'])){
	$Log['tx'][]='falta la variable renombre ';
	terminar($Log);		
}


function IsNullOrEmptyString($string){
    return (!isset($string) || trim($string)==='');
}

function verifyDate($date, $strict = true)
{
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if ($strict) {
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
    }
    return $dateTime !== false;
}

function validarFechaQuery($fechaPorValidar){
    $fechaNueva = null;
    if (IsNullOrEmptyString($fechaPorValidar) || $fechaPorValidar == 'NULL'){
        $fechaNueva = 'NULL';
    } else {
        if (verifyDate($fechaPorValidar, true)){
            $fechaNueva = "'".$fechaPorValidar."'";
        } else {
            $Log['tx'][]='error, la fecha es incorrecta: -|'.$fechaPorValidar.'|-';
            $Log['res']='err';
            terminar($Log);
        }
    }
    
    return $fechaNueva;
}

function valorNulableQuery($valorAValidar){
    $valorParaQuery = null;
    
    if (IsNullOrEmptyString($valorAValidar) || $valorAValidar == 'NULL'){
        $valorParaQuery = 'NULL';
    } else {
        $valorParaQuery = "'".$valorAValidar."'";
    }
    
    return $valorParaQuery;
}

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificaci�n de este marco acad�mico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];




$query="
		SELECT 
			id, autor, nombre, ic_p_est_02_marcoacademico, 
			zz_borrada, 
			descripcion, 
			nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
			nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
			zz_publicada, srid, sld, tipogeometria, 
			zz_instrucciones, modo_defecto, wms_layer, zz_aux_ind
		FROM 
			geogec.ref_capasgeo
		WHERE
            id = '".$_POST['idCapa']."'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$Capa=pg_fetch_assoc($Consulta);

if($Capa['tipogeometria']=='Point'){
	$campogeo='geom_point';
}elseif($Capa['tipogeometria']=='LineString'){
	$campogeo='geom_line';
}else{
	$campogeo='geom';
}


$query="
	SELECT 
		id, nombre, descripcion, funcionalidad, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, periodicidad, fechadesde, fechahasta, usu_autor, zz_borrada, zz_publicada, col_texto1_nom, col_texto2_nom, col_texto3_nom, col_texto4_nom, col_texto5_nom, col_numero1_nom, col_numero2_nom, col_numero3_nom, col_numero4_nom, col_numero5_nom, col_texto1_unidad, col_texto2_unidad, col_texto3_unidad, col_texto4_unidad, col_texto5_unidad, col_numero1_unidad, col_numero2_unidad, col_numero3_unidad, col_numero4_unidad, col_numero5_unidad, representar_campo, representar_val_max, representar_val_min, zz_borrada_usu, zz_borrada_utime, calc_buffer, calc_superp, calc_zonificacion
	FROM 
		geogec.ref_indicadores_indicadores
	
		WHERE
             id = '".$_POST['idIndicador']."'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$Indicador=pg_fetch_assoc($Consulta);
if($Capa['tipogeometria']=='Point'){
	$campogeo='geom_point';
}elseif($Capa['tipogeometria']=='LineString'){
	$campogeo='geom_line';
}else{
	$campogeo='geom';
}
$Log['data']['idInd']=$Indicador['id'];




if($_POST['idCapa_registro_duplica']!=''){
	//duplica geometr�a usada en otro per�odo
	$Log['mg'][]=utf8_encode('funci�n en desarrollo');
	$Log['tx'][]=utf8_encode('funci�n en desarrollo');
	$Log['res']='err';	
	
}else if($_POST['renombre']!=''){
	//renombre la geometr�a seleccionada
			
	$query="
		UPDATE 
			geogec.ref_capasgeo_registros
		SET 
			texto1='".$_POST['renombre']."'
		FROM
			geogec.ref_capasgeo
		WHERE
			ref_capasgeo.id = ref_capasgeo_registros.id_ref_capasgeo
		AND
			ref_capasgeo_registros.id='".$_POST['idCapa_registro_renombre']."'
		AND
			ref_capasgeo_registros.id_ref_capasgeo='".$_POST['idCapa']."'
		AND
			ref_capasgeo.zz_aux_ind='".$_POST['idIndicador']."'
	";			
			
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	$Log['data']['id_geom_edit']=$_POST['idCapa_registro_renombre'];
	
	
}else if($_POST['idCapa_registro_elimina']!=''){
	//elimina geometr�a usada
	
	$query="
		UPDATE 
			geogec.ref_capasgeo_registros
		SET 
			zz_borrada='1'
		
		FROM
		 
			geogec.ref_capasgeo 
			
		WHERE
		
			ref_capasgeo.id = ref_capasgeo_registros.id_ref_capasgeo
		
		AND
		 
			ref_capasgeo_registros.id='".$_POST['idCapa_registro_elimina']."'
		AND
		
			ref_capasgeo_registros.id_ref_capasgeo='".$_POST['idCapa']."'
			
		AND
		
			ref_capasgeo.zz_aux_ind='".$_POST['idIndicador']."'
	";			
			
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
}else{
	//crea geometr�a
	
	$query="
		INSERT INTO geogec.ref_capasgeo_registros(
			".$campogeo.",
			id_ref_capasgeo,
			texto1
		)
		VALUES (
			ST_GeomFromText('".$_POST['geometria']."',3857),
			'".$_POST['idCapa']."',
			'- sin nombre -'
		)
		RETURNING
			id
	";	
	
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	if (pg_num_rows($Consulta) <= 0){
	    $Log['tx'][]= "Se creo un registro geometrico vinculado a la capa ".$_POST['idCapa'];
	    $Log['data']=null;
	} else {
	    $fila = pg_fetch_assoc($Consulta);
	    $RegCapa=$fila['id'];
		$Log['tx'][]= "Creado registro de geometria id:".$RegCapa;
	}

	$Log['data']['periodo']['ano']=$_POST['ano'];
	$Log['data']['periodo']['mes']=$_POST['mes'];
	
	$extracampo='';
	$extravalor='';
    if ($Indicador['periodicidad'] == 'mensual'){
        $extracampo= " mes, ";
		$extravalor="'".$_POST['mes']."', ";
    }


	$query = "
		INSERT INTO   
			geogec.ref_indicadores_valores
			
	        (   
	        	id_p_ref_indicadores_indicadores, 
	            ano,
	            ".$extracampo."
	            usu_autor, 
	            fechadecreacion,    
	            zz_superado,
	            zz_borrado,
	            id_p_ref_capas_registros
	        )
	
	        VALUES
	        (   '".$_POST['idIndicador']."',
	            '".$_POST['ano']."',
				".$extravalor."
	            ".$idUsuario.",
	            ".validarFechaQuery($_POST['fechadecreacion']).",
	            0,
	            0,
	            ".$RegCapa."
	        )
	        RETURNING id;
	 ";

	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}
	
	if (pg_num_rows($Consulta) <= 0){
	    $Log['tx'][]= "Se creo un valor de indicador vinculado al indicador ".$_POST['idIndicador']." y al registro geom�trico ".$RegCapa;
	    $Log['data']=null;
	} else {
	    $fila = pg_fetch_assoc($Consulta);
	    $RegValor=$fila['id'];
		$Log['tx'][]= "Creado valor de indicador id:".$RegValor;
	}
	
	
	
	
}

$Log['res']="exito";

//$Log['tx'][]='Query: '.$query;

terminar($Log);
