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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['zz_publicada'])){
	$Log['tx'][]='no fue enviada la variable zz_publicada';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$query="SELECT  
 id, 
 nombre, 
 descripcion,
  funcionalidad,
   id_p_ref_capasgeo, 
   ic_p_est_02_marcoacademico,
    periodicidad, 
    fechadesde, 
    fechahasta, 
    usu_autor, 
    zz_borrada, 
    zz_publicada,
     col_texto1_nom, 
     col_texto2_nom, col_texto3_nom, col_texto4_nom, col_texto5_nom, col_numero1_nom, col_numero2_nom, col_numero3_nom, col_numero4_nom, col_numero5_nom, col_texto1_unidad, col_texto2_unidad, col_texto3_unidad, col_texto4_unidad, col_texto5_unidad, col_numero1_unidad, col_numero2_unidad, col_numero3_unidad, col_numero4_unidad, col_numero5_unidad, representar_campo, representar_val_max, representar_val_min
	
	
        FROM    geogec.ref_indicadores_indicadores
        WHERE 
                zz_borrada = '0'
        AND
                zz_publicada = '".$_POST['zz_publicada']."'
        AND
                usu_autor = '".$idUsuario."'
 ";

if(isset($_POST['id'])){
    $query=$query."
        AND
                id = '".$_POST['id']."'
     ";
}

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron indicadores existentes para este usuario.";
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicadores existentes";
    while ($fila=pg_fetch_assoc($Consulta)){	
	$Log['data']['indicador']=$fila;
    }
}


if(!isset($_POST['ano'])){
	$Log['tx'][]='no fue enviada la variable ano';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['mes'])){
	$Log['tx'][]='no fue enviada la variable mes';
	$Log['res']='err';
	terminar($Log);	
}


//si los valore coinciden con todos los registros de la capa de este indicador, est� completa





$query="

	SELECT 
		id, 
		id_p_ref_indicadores_indicadores, 
		ano, 
		mes, 
		usu_autor, 
		fechadecreacion, 
		zz_superado, 
		zz_borrado,
		
		CASE WHEN ref_indicadores_indicadores.col_texto1_nom != '' AND ref_indicadores_valores.col_texto1_dato = ''  THEN 0 ELSE 1 END AS stat_col_texto1_nom,
		CASE WHEN ref_indicadores_indicadores.col_texto2_nom != '' AND ref_indicadores_valores.col_texto2_dato = ''  THEN 0 ELSE 1 END AS stat_col_texto2_nom,
		CASE WHEN ref_indicadores_indicadores.col_texto3_nom != '' AND ref_indicadores_valores.col_texto3_dato = ''  THEN 0 ELSE 1 END AS stat_col_texto3_nom,
		CASE WHEN ref_indicadores_indicadores.col_texto4_nom != '' AND ref_indicadores_valores.col_texto4_dato = ''  THEN 0 ELSE 1 END AS stat_col_texto4_nom,
		CASE WHEN ref_indicadores_indicadores.col_texto5_nom != '' AND ref_indicadores_valores.col_texto5_dato = ''  THEN 0 ELSE 1 END AS stat_col_texto5_nom,
		CASE WHEN ref_indicadores_indicadores.col_numero1_dato != '' AND ref_indicadores_valores.col_numero1_dato = ''  THEN 0 ELSE 1 END AS stat_col_numero1_nom,
		CASE WHEN ref_indicadores_indicadores.col_numero2_dato != '' AND ref_indicadores_valores.col_numero2_dato = ''  THEN 0 ELSE 1 END AS stat_col_numero2_nom,
		CASE WHEN ref_indicadores_indicadores.col_numero3_dato != '' AND ref_indicadores_valores.col_numero3_dato = ''  THEN 0 ELSE 1 END AS stat_col_numero3_nom,
		CASE WHEN ref_indicadores_indicadores.col_numero4_dato != '' AND ref_indicadores_valores.col_numero4_dato = ''  THEN 0 ELSE 1 END AS stat_col_numero4_nom,
		CASE WHEN ref_indicadores_indicadores.col_numero5_dato != '' AND ref_indicadores_valores.col_numero5_dato = ''  THEN 0 ELSE 1 END AS stat_col_numero5_nom,
		
		
	FROM 
	
		geogec.ref_indicadores_valores
		
	LEFT JOIN
	
		 geogec.ref_indicadores_indicadores ON ref_indicadores_valores.id_p_ref_indicadores_indicadores = ref_indicadores_indicadores.id
		
	WHERE
		zz_superado = '0'
		AND
		zz_borrado = '0'
		AND
		id_p_ref_indicadores_indicadores = '".$_POST['id']."'

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
    $Log['tx'][]= "No se encontraron registros para esta capa.";
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['geom'][$fila['id']]['valores']=array();
    }
}
		
		


		




$query="SELECT  
                id,
                ST_AsText(geom) as geotx,
                texto1, 
                texto2, 
                texto3, 
                texto4, 
                texto5, 
                numero1, 
                numero2, 
                numero3, 
                numero4, 
                numero5, 
                geom_point, 
                geom_line, 
                id_ref_capasgeo

        FROM    
            geogec.ref_capasgeo_registros
        WHERE 
	  		id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
    $Log['tx'][]= "No se encontraron registros para esta capa.";
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
    while ($fila=pg_fetch_assoc($Consulta)){	
		$Log['data']['geom'][$fila['id']]=$fila;
		$Log['data']['geom'][$fila['id']]['valores']=array();
    }
}


$query="
	SELECT 
		id, 
		id_p_ref_indicadores_indicadores, 
		ano, 
		mes, 
		usu_autor, 
		fechadecreacion, 
		zz_superado, 
		zz_borrado, 
		col_texto1_dato, 
		col_texto2_dato, 
		col_texto3_dato, 
		col_texto4_dato, 
		col_texto5_dato, 
		col_numero1_dato, 
		col_numero2_dato, 
		col_numero3_dato, 
		col_numero4_dato, 
		col_numero5_dato, 
		id_p_ref_capas_registros, 
		fechadesde, 
		fechahasta
		
	FROM 
		geogec.ref_indicadores_valores
	WHERE
		ano = '".$_POST['ano']."'
		AND
		mes = '".$_POST['mes']."'
		AND
		zz_superado = '0'
		AND
		zz_borrado = '0'
		AND
		id_p_ref_indicadores_indicadores = '".$_POST['id']."'
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
	if(!isset($fila['id_p_ref_capas_registros'])){continue;}
	    	
	$Log['data']['geom'][$fila['id_p_ref_capas_registros']]['valores'][]=$fila;
	
}





$Log['res']="exito";
terminar($Log);
