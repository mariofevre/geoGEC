<?php
/**
* consulta la base de datos y genera una salida con las unidades de planificaic�n dentro en un mismo marco o proyecto.
*
* @package    	geoGEC
* @subpackage 	app_plan. Aplicacion para la gesti�n de documento
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
* 
*
*/

ini_set('display_errors', '1');

if(!isset($_SESSION)) { session_start(); }

chdir(getcwd().'/../'); 

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include_once("./usuarios/usu_validacion.php");
global $ConecSIG;
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
	$Log['tx'][]='no fue enviada la variable idMarco o codMarco';
	$Log['mg'][]='no fue enviada la variable idMarco o codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if($_POST['codMarco']==''){
	$Log['tx'][]=utf8_encode('no fue solicitado un cod v�lido para marco acad�mico');
	$Log['mg'][]=utf8_encode('no fue solicitado un cod v�lido para marco acad�mico');
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_plan'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_plan'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
if($Acc<1){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la planificaci�n de este marco acad�mico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}


$query="
	SELECT 
	 	id, 
	 	nombre, 
	 	descripcion, 
	 	id_p_sis_planif_plan,
	 	ic_p_est_02_marcoacademico, 
	 	orden, 
	 	zz_borrada
	FROM 
  		geogec.sis_planif_plan
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
$Log['data']['orden']['psdir']=array();

while($fila=pg_fetch_assoc($ConsultaProy)){	
	//$Ord[$fila['id']]=$fila['orden'];	
	$Log['data']['psdir'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id']]['estados']=Array();
	$Log['data']['psdir'][$fila['id']]['responsables']=Array();
	$Log['data']['orden']['psdir'][]=$fila['id'];	
}

if(
	$_SESSION["geogec"]["usuario"]['id'] < 1
	&&
	$_SESSION["geogec"]["usuario"]['id'] != '-1'
	){
    $Log['tx'][]='error, falta id del usuario';
	$Log['res']='err';
	terminar($Log);	
}
$UsuarioId = $_SESSION["geogec"]["usuario"]['id'];

//TODO: agregar que puede estar borrado o no aprobado
$query="SELECT
		estados.id, 
		estados.terminado, 
		estados.avance_previsto, 
		estados.avance_efectivo, 
		estados.id_p_sis_planif_plan,
		estados.ic_p_est_02_marcoacademico,
                estados.porcentaje_progreso,
                estados.fecha_propuesta,
		estados.fecha_cambio,
		estados.id_p_sis_usu_registro,
                usuarios.nombre,
		usuarios.apellido,
		usuarios.email
	FROM 
		geogec.sis_planif_estados estados
        JOIN    
                geogec.sis_usu_registro usuarios
	ON 
                estados.id_p_sis_usu_registro = usuarios.id
	WHERE
		estados.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	ORDER BY 
		estados.fecha_cambio DESC
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

$Log['tx'][]='query: '.$query;
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir'][$fila['id_p_sis_planif_plan']]['estados'][$fila['id']]=$fila;
	$Log['data']['psdir'][$fila['id_p_sis_planif_plan']]['ordenestados'][]=$fila['id'];
}

$query="
	SELECT 
		id_p_sis_usu_registro, 
		id_p_sis_planif_plan, 
		responsabilidad,
		sis_usu_registro.id,
		sis_usu_registro.nombre,
		sis_usu_registro.apellido
	FROM 
		geogec.sis_planif_reponsables,
		geogec.sis_usu_registro
	WHERE
		sis_planif_reponsables.id_p_sis_usu_registro = sis_usu_registro.id
	AND
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
	AND
		sis_planif_reponsables.zz_borrada='0'
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
$Log['tx'][]='query: '.$query;
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir'][$fila['id_p_sis_planif_plan']]['responsables'][$fila['id_p_sis_usu_registro']]=$fila;
	$Log['data']['psdir'][$fila['id_p_sis_planif_plan']]['ordenresponsables'][]=$fila['id'];
}

$Log['res']='exito';
terminar($Log);
?>