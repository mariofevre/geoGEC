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
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
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


if(!isset($_POST['codMarcoDestino'])){
	$Log['mg'][]=utf8_encode('error en las variables codMarco.');
	$Log['tx'][]='error, no se recibi� la variable id';
	$Log['res']='err';
	terminar($Log);	
}

if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarcoDestino']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarcoDestino']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarcoDestino']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarcoDestino']]['general'];
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



if(!isset($_POST['idCapa'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibi� la variable id';
	$Log['res']='err';
	terminar($Log);	
}




$query="

	INSERT INTO
		geogec.ref_capasgeo(
			autor,nombre,ic_p_est_02_marcoacademico,zz_borrada,
			descripcion,nom_col_text1,nom_col_text2,nom_col_text3,
			nom_col_text4,nom_col_text5,nom_col_num1,nom_col_num2,
			nom_col_num3,nom_col_num4,nom_col_num5,zz_publicada,
			srid,sld,tipogeometria,zz_instrucciones,modo_defecto,
			wms_layer,zz_aux_ind,zz_aux_rele
		)
		
		SELECT 
				'".$_SESSION["geogec"]["usuario"]['id']."',nombre,'".$_POST['codMarcoDestino']."',zz_borrada,
				descripcion,nom_col_text1,nom_col_text2,nom_col_text3,
				nom_col_text4,nom_col_text5,nom_col_num1,nom_col_num2,
				nom_col_num3,nom_col_num4,nom_col_num5,zz_publicada,
				srid,sld,tipogeometria,zz_instrucciones,modo_defecto,
				wms_layer,zz_aux_ind,zz_aux_rele 
		FROM 
			geogec.ref_capasgeo
		WHERE
			id = '".$_POST['idCapa']."'
		
		
		returning id
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);

$Log['tx'][]= "Creada capa id: ".$fila['id'];
$Log['data']['id']=$fila['id'];




$query="
	INSERT INTO
		geogec.ref_capasgeo_registros(
			geom,geom_point,geom_line,
			texto1,texto2,texto3,texto4,texto5,
			numero1,numero2,numero3,numero4,numero5,
			id_ref_capasgeo,zz_auto_crea_usu,zz_auto_crea_fechau
			
		)
			SELECT
				geom,geom_point,geom_line,
				texto1,texto2,texto3,texto4,texto5,
				numero1,numero2,numero3,numero4,numero5,
				'".$Log['data']['id']."','".$_SESSION["geogec"]["usuario"]['id']."','".time()."'
				
				FROM 
				geogec.ref_capasgeo_registros
				WHERE
				id_ref_capasgeo= '".$_POST['idCapa']."'
		
		returning id
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
