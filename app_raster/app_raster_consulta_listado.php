<?php
/**
 * genera una salida JSON conteniendo el listado de capas raster disponibles para este proyectdo.
* 
* @package    	geoGEC
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018-2023 Universidad de Buenos Aires
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
//$Usu = validarUsuario(); // en ./usu_valudacion.php
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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
$mod='app_raster';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$mod])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$mod];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<1){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco acad�mico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}



$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


//CONSULTA DICCIONARIO

$query="
	SELECT 
		id, nombre, descripcion, url_consulta
	FROM 
		geogec.ref_raster_tipos_diccionario;
		";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['tipos']=array();
while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['tipos'][$fila['id']]=$fila;
	$Log['data']['tipos'][$fila['id']]['bandas']=array();
}




$query="
	SELECT 
		id, id_p_ref_raster_tipos_diccionario, numero, 
		indice, nombre, descripcion, longitud_central, ancho, resolucion
	FROM 
		geogec.ref_raster_tipos_bandas_diccionario
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['tipos']=array();
while ($fila=pg_fetch_assoc($Consulta)){	
	if(!isset($Log['data']['tipos'][$fila['id_p_ref_raster_tipos_diccionario']])){
		$Log['tx'][]='Tipo de banda ('.$fila['id'].') referida a tipo inexistente ('.$fila['id_p_ref_raster_tipos_diccionario'].')';
		continue;
	}
	$idtipo=$fila['id_p_ref_raster_tipos_diccionario'];
	$Log['data']['tipos'][$idtipo]['bandas'][$fila['id']]=$fila;
}



		
		

$query="
	SELECT 
		id, autor, nombre, descripcion, ic_p_est_02_marcoacademico, tipo, 
		zz_borrada, zz_publicada, srid, modo_publica, fecha_ano, 
		fecha_mes, fecha_dia, zz_auto_borra_usu, 
		zz_auto_borra_fechau, geom, hora_utc, id_p_ref_01_documentos, 
		zz_data_procesada,
		id_p_ref_raster_tipos_diccionario
	FROM 
		geogec.ref_raster_coberturas
    WHERE 
    	ref_raster_coberturas.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
  		ref_capasgeo.zz_borrada = '0'
  		
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$Log['data']['tipos']=array();
while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['coberturas'][$fila['id']]=$fila;
	$Log['data']['coberturas'][$fila['id']]['bandas']=array();
	$idtipo=$fila['id_p_ref_raster_tipos_diccionario'];
	$Log['data']['tipos'][$idtipo]['bandas']=array();
	if($idtipo>0){
		if(!isset($Log['data']['tipos'][$idtipo])){continue;}
		$bandas_tipo=$Log['data']['tipos'][$idtipo]['bandas'];
		foreach($bandas_tipo as $id_banda_tipo => $dat){
			$Log['data']['coberturas'][$fila['id']]['bandas'][$id_banda_tipo]=array(
				'numero' => $dat['numero'],
				'indice' => $dat['indice'],
				'nombre' => $dat['nombre'],
				'estado' => 'sin cargar'
			);			
		}
	}
}


$query="
	SELECT 
		id, id_p_ref_raster_coberturas, id_p_ref_raster_tipos_bandas_diccionario, estado, anotaciones
	FROM 
		geogec.ref_raster_bandas
    WHERE 
    	ref_raster_bandas.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
  		ref_raster_bandas.zz_borrada = '0'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


while ($fila=pg_fetch_assoc($Consulta)){	
	$id_cob=$fila['id_p_ref_raster_coberturas'];
	if(!isset($Log['data']['coberturas'][$id_cob])){continue;}
	
	$id_banda_tipo=$fila['id_p_ref_raster_tipos_bandas_diccionario'];
	if(!isset($Log['data']['coberturas'][$id_cob]['bandas'][$id_banda_tipo])){continue;}
	
	foreach($fila as $k => $v){
		$Log['data']['coberturas'][$id_cob]['bandas'][$id_banda_tipo][$k]=$v;
	}
}



$Log['res']="exito";
terminar($Log);
