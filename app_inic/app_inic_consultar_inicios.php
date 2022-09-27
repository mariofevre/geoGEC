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


$Id_Capa_Provincias='304';
$campo_link_provincia='texto1';
$campo_nombre_provincia='texto2';

$Id_Capa_Departamen='237';
$campo_link_departamen='texto1';
$campo_nombre_departamen='texto2';
$campo_id_prov_departamen='texto3';


$idUsuario = $_SESSION["geogec"]["usuario"]['id'];



$query="
SELECT 
	i.id, 
	i.id_p_sis_usu_registro, 
	i.nombre, 
	i.link_provincia, 
	i.link_departamento, 
	i.id_p_ref_ind_mod_tag, 
	i.zz_auto_crea_fechau,
	rp.".$campo_nombre_provincia." as provincia,
	rd.".$campo_nombre_departamen." as departamento,
	rd.geom
	
	FROM 
		geogec.ref_inic_inicio as i
	LEFT JOIN 
	geogec.ref_capasgeo_registros as rp 
		ON rp.id_ref_capasgeo = '".$Id_Capa_Provincias."' 
		AND rp.".$campo_link_provincia." = i.link_provincia
	LEFT JOIN 
	geogec.ref_capasgeo_registros as rd 
		ON rd.id_ref_capasgeo = '".$Id_Capa_Departamen."' 
		AND rp.".$campo_link_departamen." = i.link_departamento
		
	WHERE 
		i.id_p_sis_usu_registro ='".$idUsuario."'
		
	ORDER by id desc
	";
	
$Consulta = pg_query($ConecSIG, $query);


$Log['tx'][]='query: '.utf8_encode($query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

while($fila=pg_fetch_assoc($Consulta)){
	$Log['data']['iniciosOrden'][]=$fila['id'];
	$Log['data']['inicios'][$fila['id']]=$fila;
}

	
	

	

$Log['res']="exito";
terminar($Log);
