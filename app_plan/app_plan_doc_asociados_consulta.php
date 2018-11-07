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
	$Log['tx'][]=utf8_encode('no fue solicitado un cod válido para marco académico');
	$Log['mg'][]=utf8_encode('no fue solicitado un cod válido para marco académico');
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
	$Log['mg'][]=utf8_encode('no cuenta con permisos para consultar la caja de documentnos de este marco académico. \n minimo requerido: 1 \ nivel disponible: '.$Acc);
	$Log['res']='err';
	terminar($Log);	
}

$ActividadId = '';

if(isset($_POST['idactividad'])){
    if($_POST['idactividad']!=''){
        $ActividadId = $_POST['idactividad'];
    }
}

$hayResultados = 0;
$Log['data']['psdir']=array();
$Log['data']['psdir']['archivos']=array();
$Log['data']['psdir']['archivolinks']=array();

$query="SELECT 
                geogec.sis_planif_docs.id,
                geogec.sis_planif_docs.id_sis_planif_plan,
                geogec.sis_planif_docs.id_ref_01_documentos,
                geogec.sis_planif_docs.comentario,
                geogec.ref_01_documentos.id_p_est_02_marcoacademico,
                geogec.ref_01_documentos.zz_borrada,
                geogec.ref_01_documentos.nombre,
                geogec.ref_01_documentos.descripcion,
                geogec.ref_01_documentos.archivo,
                geogec.ref_01_documentos.id_p_ref_02_pseudocarpetas,
                geogec.ref_01_documentos.orden
        FROM 
                geogec.sis_planif_docs
        JOIN 
                geogec.ref_01_documentos
        ON 
                geogec.sis_planif_docs.id_ref_01_documentos = geogec.ref_01_documentos.id
        WHERE
                geogec.ref_01_documentos.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'";

if ($ActividadId != ''){
    $query = $query."AND geogec.sis_planif_docs.id_sis_planif_plan='".$ActividadId."'";
}
        
$query = $query."
        AND 
                geogec.ref_01_documentos.zz_borrada = 0
        ORDER BY 
		geogec.ref_01_documentos.orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)>0){
    $hayResultados = $hayResultados + 1;
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir']['archivos'][$fila['id']]=$fila;
	$Log['data']['psdir']['ordenarchivos'][]=$fila['id'];
}

$query="SELECT 
                geogec.sis_planif_links.id,
                geogec.sis_planif_links.id_sis_planif_plan,
                geogec.sis_planif_links.id_ref_doc_links,
                geogec.sis_planif_links.comentario,
                geogec.ref_doc_links.ic_p_est_02_marcoacademico,
                geogec.ref_doc_links.id_p_ref_02_pseudocarpetas,
                geogec.ref_doc_links.zz_borrada,
                geogec.ref_doc_links.nombre,
                geogec.ref_doc_links.url,
                geogec.ref_doc_links.orden,
                geogec.ref_doc_links.descripcion
        FROM 
                geogec.sis_planif_links
        JOIN 
                geogec.ref_doc_links
        ON 
                geogec.sis_planif_links.id_ref_doc_links = geogec.ref_doc_links.id
        WHERE
                geogec.ref_doc_links.ic_p_est_02_marcoacademico='".$_POST['codMarco']."'
";

if ($ActividadId != ''){
    $query = $query."AND geogec.sis_planif_links.id_sis_planif_plan='".$ActividadId."'";
}
        
$query = $query."
        AND 
                geogec.ref_doc_links.zz_borrada = 0
        ORDER BY 
		geogec.ref_doc_links.orden
";

$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)>0){
    $hayResultados = $hayResultados + 1;
}
while ($fila=pg_fetch_assoc($ConsultaProy)){	
	$Log['data']['psdir']['archivolinks'][$fila['id']]=$fila;
	$Log['data']['psdir']['ordenarchivolinks'][]=$fila['id'];
}

if($hayResultados == 0){
    $Log['tx'][]='no se encontraron documentos asociados para este marco academico';
}

$Log['res']='exito';
terminar($Log);
?>