<?php
/**
*
 * edita como borrado el registro correspondiente a un item o carpeta para anidar archivos.
 * 
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
* 
*
*/
ini_set('display_errors', true);

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
	$Log['tx'][]='no fue enviada la varaible codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if($Usu['acc']['ref'][$_POST['codMarco']]<2){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('no cuenta con permisos para generar archivos y cajas en  tablas tipo ref para el marco académico id:'.$_POST['codMarco']);
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id'])){
	$Log['tx'][]='no fue definido el tipo de contenido';
	$Log['res']='err';
	terminar($Log);
}
if($_POST['tipo']!='item'){
	$Log['tx'][]='variable de control tipo incorrecto';
	$Log['res']='err';
	terminar($Log);
}
if($_POST['accion']!='borrar'){
	$Log['tx'][]='variable de control accion incorrecto';
	$Log['res']='err';
	terminar($Log);
}


$query="
	UPDATE 
		geogec.ref_02_pseudocarpetas
	SET 
		zz_borrada='1'
	WHERE
		id='".$_POST['id']."'
	AND
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."' 	
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

//echo $query;
$Log['data']['idit']=$_POST['id'];
$Log['tx'][]='completado';
$Log['res']='exito';

terminar($Log);

?>