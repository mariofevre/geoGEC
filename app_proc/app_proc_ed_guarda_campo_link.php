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
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_docs'];
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


if(!isset($_POST['idin'])){
	$Log['tx'][]='no fue enviada la variable idin';
	$Log['res']='err';
	terminar($Log);	
}
if($_POST['idin']==''){
	$Log['tx'][]='no fue enviada la variable idin';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['idin']=$_POST['idin'];

if(!isset($_POST['idco'])){
	$Log['tx'][]='no fue enviada la variable idco';
	$Log['res']='err';
	terminar($Log);	
}
if($_POST['idco']==''){
	$Log['tx'][]='no fue enviada la variable idco';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['idco']=$_POST['idco'];

if(!isset($_POST['campo'])){
	$Log['tx'][]='no fue enviada la variable campo';
	$Log['res']='err';
	terminar($Log);	
}
if($_POST['campo']==''){
	$Log['tx'][]='no fue enviada la variable campo';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['campo']=$_POST['campo'];


if(!isset($_POST['valor'])){
	$Log['tx'][]='no fue enviada la variable campo';
	$Log['res']='err';
	terminar($Log);	
}
$Log['data']['valor']=$_POST['valor'];


		
$camposvalidos=array(
		'campo_t_a' => '',
		'campo_t_b' => '',
		'campo_t_c' => '',
		'campo_t_d' => '',
		'campo_t_e' => '',
		'campo_n_a' => '',
		'campo_n_b' => '',
		'campo_n_c' => '',
		'campo_n_d' => '',
		'campo_n_e' => ''
);	
if(!isset($camposvalidos[$_POST['campo']])){
	$Log['tx'][]='no fue enviado un contenido v�lido apra la variable campo';
	$Log['res']='err';
	terminar($Log);		
}	
		
$query="
	UPDATE
	
		geogec.ref_proc_instancias_componentes
		
	SET
		
		".$_POST['campo']." = '".$_POST['valor']."'
    WHERE 
		ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
    AND
		id_p_ref_proc_instancias = '".$_POST['idin']."'
    AND
		id_p_ref_proc_componentes = '".$_POST['idco']."'
  		
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}






$Log['res']="exito";
terminar($Log);
