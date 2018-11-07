<?php
/**
* crea o edita una responsabilidad asignada a una planificaci�n
*
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gesti�n de documento
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
	$Log['tx'][]='no fue enviada la varaible idMarco';
        $Log['mg'][]='no fue enviada la variable idMarco o codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['idit'])){
	$Log['tx'][]='no fue enviada la varaible idit que indica la plafinicacion a la cual se agna el responsable';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['iddoc'])){
	$Log['tx'][]='no fue enviada la varaible iddoc que indica el id del documento';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['nuevoestado'])){
	$Log['tx'][]='no fue enviada la varaible nuevoestado (incluido/excluido)';
	$Log['res']='err';
	terminar($Log);	
}
$comentario = '';
if(isset($_POST['comentario'])){
    $comentario = $_POST['comentario'];
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
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar los documentos asociados a la planificaci�n de este marco acad�mico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$query="SELECT 
                id, 
                id_sis_planif_plan,
                id_ref_doc_links,
                comentario
	FROM
		geogec.sis_planif_links
	WHERE
		id_sis_planif_plan='".$_POST['idit']."'
	AND
		id_ref_doc_links='".$_POST['iddoc']."'
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}

if($_POST['nuevoestado']=='excluido'){
    if(pg_num_rows($ConsultaProy)!=0){
        $query="DELETE FROM geogec.sis_planif_links
                WHERE 
                        id_sis_planif_plan='".$_POST['idit']."'
                AND
                        id_ref_doc_links='".$_POST['iddoc']."'
        ";
    } else {
        //Si no encontro la relacion, no hace falta borrarla
        $Log['tx'][]='No hay nada que actualizar';
        $Log['res']='exito';
        terminar($Log);
    }
}elseif($_POST['nuevoestado']=='incluido'){
    if ($comentario == ''){
        $comentario = "NULL";
    } else {
        $comentario = "'".$comentario."'";
    }
    if(pg_num_rows($ConsultaProy)==0){
        $query="INSERT INTO geogec.sis_planif_links
                        (id_sis_planif_plan, 
                        id_ref_doc_links, 
                        comentario)
                VALUES (
                        '".$_POST['idit']."', 
                        '".$_POST['iddoc']."', 
                        ".$comentario."
                );
        ";	
    } else {
        $query="UPDATE
                        geogec.sis_planif_links
                SET 
                        comentario=".$comentario."
                WHERE 
                        id_sis_planif_plan='".$_POST['idit']."'
                AND 
                        id_ref_doc_links='".$_POST['iddoc']."'
        ";		
    }
} else {
    $Log['tx'][]='error: valor inesperado para la variable nuevoestado';
    $Log['res']='err';
    terminar($Log);	
}

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['res']='err';
    terminar($Log);
}

$Log['data']=$_POST;


$Log['res']='exito';
terminar($Log);
?>