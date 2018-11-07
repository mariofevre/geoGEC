<?php
/**
* *
* modifica en la base de datos los atributos de una carpeta
 * 
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gesti�n de documento
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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
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
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificaci�n de este marco acad�mico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}


if(!isset($_POST['id'])){
	$Log['tx'][]='error, falta id';
	$Log['res']='err';
	terminar($Log);	
}


if($_SESSION["geogec"]["usuario"]['id']<1){
        $Log['tx'][]='error, falta id del usuario';
	$Log['res']='err';
	terminar($Log);	
}
$UsuarioId = $_SESSION["geogec"]["usuario"]['id'];

if(!isset($_POST['progresoNumber'])){
	$Log['tx'][]='error, falta porcentaje de progreso';
	$Log['res']='err';
	terminar($Log);
}

function IsNullOrEmptyString($string){
    return (!isset($string) || trim($string)==='');
}

$fechaPropuesta = null;   
if (!IsNullOrEmptyString($_POST['fechaPropuesta'])) {
    if (verifyDate($_POST['fechaPropuesta'], true)){
        $fechaPropuesta = "'".$_POST['fechaPropuesta']."'";
    } else {
        $Log['tx'][]='error, la fecha propuesta es incorrecta: -|'.$_POST['fechaPropuesta'].'|-';
        $Log['res']='err';
        terminar($Log);
    }
}

//Solo procesa cambios en progreso si esta dentro del rango 0 - 100
if((!IsNullOrEmptyString($_POST['progresoNumber']) 
        &&  $_POST['progresoNumber'] >= 0 && $_POST['progresoNumber'] <= 100)) {

        if ($fechaPropuesta == null){
            $fechaPropuesta = 'NULL';
        }
        
        $query="INSERT INTO geogec.sis_planif_estados(
                    terminado,
                    id_p_sis_planif_plan, 
                    ic_p_est_02_marcoacademico,
                    porcentaje_progreso,
                    fecha_propuesta,
                    fecha_cambio, 
                    id_p_sis_usu_registro
                ) VALUES (
                    'false',
                    '".$_POST['id']."',
                    '".$_POST['codMarco']."',
                    '".$_POST['progresoNumber']."',
                    ".$fechaPropuesta.",
                    '".date('Y-m-d H:i:s', time())."',
                    '".$UsuarioId."'
                )
                RETURNING id
        ";

        $ConsultaVer = pg_query($ConecSIG, $query);
        if(pg_errormessage($ConecSIG)!=''){
                $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
                $Log['tx'][]='query: '.$query;
                $Log['mg'][]='error interno';
                $Log['res']='err';
                terminar($Log);	
        }
}

$Log['data']['idit']=$_POST['id'];
$Log['res']='exito';
terminar($Log)
?>