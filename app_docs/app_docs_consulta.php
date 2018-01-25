<?php
/**
* consulta la base de datos y genera una salida con loa archivos y las carpetas contenidas dentro en un mismo marco o proyecto.
*
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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
* 
*
*/

ini_set('display_errors', '1');

session_start();

chdir(getcwd().'/../'); 

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include_once("./usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
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
	$Log['tx'][]='no fue enviada la varaible idMarco o codMarco';
	$Log['mg'][]='no fue enviada la varaible idMarco o codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if($_POST['codMarco']==''){
	$Log['tx'][]=utf8_encode('no fue solicitado un cod vaálido para marco académico');
	$Log['mg'][]=utf8_encode('no fue solicitado un cod válido para marco académico');
	$Log['res']='err';
	terminar($Log);	
}	
	

if($Usu['acc']['ref'][$_POST['idMarco']]<2&&$Usu['acc']['ref'][$_POST['codMarco']]<2){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco académico');
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT 
		id, 
		id_p_ref_02_pseudocarpetas, 
		id_p_est_02_marcoacademico,
		ic_p_est_02_marcoacademico,
		orden,
		nombre, 
		descripcion
	FROM 
  		geogec.ref_02_pseudocarpetas
	WHERE
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 			
	AND
		zz_borrada='0'
	ORDER BY 
		orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)<1){
	$Log['tx'][]='no se encontraron pseudocarpetas para este marco academico';
}

$Log['data']['psdir']=array();
$Log['data']['psdir'][0]['archivos']=array();//el id de la caja 0 refiere a archivos localizados en ninguna caja
$Log['data']['orden']['psdir']=array();

while($fila=pg_fetch_assoc($ConsultaProy)){	
	//$Ord[$fila['id']]=$fila['orden'];	
	$Log['data']['psdir'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id']]['archivos']=Array();	
	$Log['data']['orden']['psdir'][]=$fila['id'];	
}		


$query="
	SELECT 
		id, 
		id_p_est_02_marcoacademico, 
		zz_borrada, 
		nombre, 
		descripcion,
		archivo, 
		id_p_ref_02_pseudocarpetas, 
		orden
  	FROM 
  		geogec.ref_01_documentos
	WHERE
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'	
	AND
		zz_borrada='0'
	ORDER BY 
		orden
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir'][$fila['id_p_ref_02_pseudocarpetas']]['archivos'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id_p_ref_02_pseudocarpetas']]['ordenarchivos'][]=$fila['id'];
}
$Log['res']='exito';
terminar($Log);
?>