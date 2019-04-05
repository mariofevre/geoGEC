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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['idSesion'])){
	$Log['tx'][]='no fue enviada la variable idSesion';
	$Log['res']='err';
	terminar($Log);	
}


$minacc=0;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
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

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificaci�n de este marco acad�mico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


//datos generales de la sesion
$query="
	SELECT 
		id, id_p_indicadores_indicadores, ic_p_est_02_marcoacademico, 
		nombre, presentacion, costounitario, limiteunitarioporturno,
		zz_borrada, 
		ref_game_sesiones.*
	FROM 
		geogec.ref_game_sesiones
	WHERE 
        zz_borrada = '0'
    AND
		ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	AND
		id = '".$_POST['idSesion']."'
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
    $Log['tx'][]= "No se encontraron sesiones existentes.";
    $Log['tx'][]= "Query: ".$query;
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de sesiones existentes";
	$Log['data']['sesion']=pg_fetch_assoc($Consulta);
	$Log['data']['sesion']['presentacionBR']=nl2br($Log['data']['sesion']['presentacion']);
}


$query="SELECT  
			id, funcionalidad, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, periodicidad, fechadesde, fechahasta, 
			calc_buffer, calc_superp, calc_zonificacion
		FROM    
			geogec.ref_indicadores_indicadores		
        WHERE 
                zz_borrada = '0'
        AND
                id = '".$Log['data']['sesion']['id_p_indicadores_indicadores']."'
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
    $Log['tx'][]= "No se encontro el indicador id ".$Log['data']['sesion']['id_p_indicadores_indicadores'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Log['data']['indicador']=$fila;
}


 



//datos de la geometria del marco de trabajo
$query="
	SELECT 
			ST_AsText(geo) as geo_ai_tx,
			ST_AsText(ST_Centroid(geo)) as geo_centroide_tx,
			ST_x(ST_Centroid(geo)) as geo_centroide_x,
			ST_y(ST_Centroid(geo)) as geo_centroide_y,
			ST_AsText(	        	
	            ST_Transform(
					ST_Buffer(
						ST_Transform(ST_Centroid(geo),22175),				
						".$Log['data']['indicador']['calc_buffer'].", 
						'endcap=round join=round'
					),
					3857
				)
			) as geo_buffer_centroide_tx
		FROM 
			geogec.est_02_marcoacademico
		WHERE 
		 zz_obsoleto = '0'
		 AND
		 	codigo = '".$_POST['codMarco']."'
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	if(pg_num_rows($ConsultaProy)<1){
		$Log['tx'][]='error';
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='no se encontraron tablas disponibles';
		$Log['res']='err';
		terminar($Log);	
	}
	 $Log['data']['geomGame']=pg_fetch_assoc($ConsultaProy);
	


$Log['res']="exito";
terminar($Log);
