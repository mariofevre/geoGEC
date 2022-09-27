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
$Usu= validarUsuario();

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
/*
if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}
*/

/*
if(!isset($_POST['zz_publicada'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}
*/
$_POST['zz_publicada']='1';


$minacc=0;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
/*
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
*/

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificaci�n de este marco acad�mico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}


$idUsuario = $_SESSION["geogec"]["usuario"]['id'];



$query="
	SELECT 
		id, tipo, orden, condicion, consigna
	FROM 
		geogec.ref_indicadores_modelos_tag_tipo
	ORDER BY 
		orden ASC
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
	$Log['data']['tagsTiposOrden'][]=$fila['id'];
	$Log['data']['tagsTipos'][$fila['id']]=$fila;
}
	
	


$query="SELECT 
		id, tipo, nombre, des_formulario, orden_formulario, defec_formulario, ayuda
	FROM 
		geogec.ref_indicadores_modelos_tags
	ORDER BY
		orden_formulario
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$tags_link_plantilla=array();

while ($fila=pg_fetch_assoc($Consulta)){	
	$tags_link_plantilla[$fila['id']]['activo']=$fila['defec_formulario'];
	$Log['data']['tagsOrden'][$fila['tipo']][]=$fila['id'];
	$Log['data']['tags'][$fila['id']]=$fila;
}


$query="SELECT  
			ref_indicadores_modelos.*,
			sis_usu_registro.nombre as autornom,
			sis_usu_registro.apellido as autorape
        FROM    
        	geogec.ref_indicadores_modelos
        LEFT JOIN
			geogec.sis_usu_registro ON sis_usu_registro.id = ref_indicadores_modelos.usu_autor
        WHERE 
            ref_indicadores_modelos.zz_borrada = '0'
        AND
            (zz_publicada = '".$_POST['zz_publicada']."'        
			OR
            ref_indicadores_modelos.usu_autor = '".$idUsuario."')
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

if(pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron modelos de indicadores existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
}else{
    $Log['tx'][]= "Consulta de modelos de indicadores existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['modelos'][$fila['id']]=$fila;
		$Log['data']['modelosOrden'][]=$fila['id'];
		$Log['data']['modelos'][$fila['id']]['tag_link']=$tags_link_plantilla;
    }
}



$query="
	SELECT 
		id, id_p_indicadores_modelos, id_p_indicadores_modelos_tags, comentarios, zz_auto_fechau_crea, zz_auto_usu_crea, activo
	FROM 
		geogec.ref_indicadores_modelos_tags_links
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$tags_link_plantilla=array();

while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['modelos'][$fila['id_p_indicadores_modelos']]['tag_link'][$fila['id_p_indicadores_modelos_tags']]['activo']=$fila['activo'];
	$Log['data']['modelos'][$fila['id_p_indicadores_modelos']]['tag_link'][$fila['id_p_indicadores_modelos_tags']]['comentarios']=$fila['comentarios'];
}



$query="
	SELECT 
		id, descripcion, id_p_ref_indicadores_modelos, 
		app, id_modelo_en_app, id_p_ref_capasgeo, 
		id_arr_modelo_en_app
	FROM geogec.ref_indicadores_modelos_requerimientos;
 ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$tags_link_plantilla=array();

while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['modelos'][$fila['id_p_ref_indicadores_modelos']]['requerimientos'][$fila['app']]=$fila;
}

$Log['res']="exito";
terminar($Log);
