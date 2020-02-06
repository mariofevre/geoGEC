<?php
/**
* 
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
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


if(!isset($_POST['idcampa'])){
	$Log['tx'][]='no fue enviada la variable idcampa';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_SESSION["geogec"])){
	$Log['tx'][]='sesión caduca';
	$Log['acc'][]='login';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


$query="
	SELECT  
		id_p_ref_capasgeo,
		*
	
    FROM   
    	geogec.ref_rele_campa
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'

    AND
        zz_borrada = '0'
    AND 
    	id = '".$_POST['idcampa']."'
";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
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
    $Log['tx'][]= "No se encontro el indicador id ".$_POST['idcampa'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Log['data']['campa']=$fila;
}




$query="
		SELECT 
			id, autor, nombre, ic_p_est_02_marcoacademico, 
			zz_borrada, 
			descripcion, 
			nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
			nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
			zz_publicada, srid, sld, tipogeometria, 
			zz_instrucciones, modo_defecto, wms_layer, zz_aux_ind
		FROM 
			geogec.ref_capasgeo
		WHERE
            id = '".$Log['data']['campa']['id_p_ref_capasgeo']."'

     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$Capa=pg_fetch_assoc($Consulta);

if($Capa['tipogeometria']=='Point'){
	$campogeo='ref_capasgeo_registros.geom_point';
}elseif($Capa['tipogeometria']=='LineString'){
	$campogeo='ref_capasgeo_registros.geom_line';
}else{
	$campogeo='ref_capasgeo_registros.geom';
}



$query="SELECT  
            ref_capasgeo_registros.id,
            ST_AsText(".$campogeo.") as geotx,
            ref_rele_registros.col_texto1_dato   as t1,
            ref_rele_registros.col_numero1_dato  as n1
            
        FROM    
            geogec.ref_capasgeo_registros
        LEFT JOIN
    		geogec.ref_rele_registros
    		ON 
    			ref_rele_registros.id_p_ref_capas_registros = ref_capasgeo_registros.id 
    			AND ref_rele_registros.zz_superado='0' 
    			AND ref_rele_registros.zz_borrado='0'
        WHERE 
        	     	
            id_ref_capasgeo = '".$Log['data']['campa']['id_p_ref_capasgeo']."'
            AND
            ref_capasgeo_registros.zz_borrada='0'
            AND
    		ST_X(ST_centroid(".$campogeo.")) is not null
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['data']['geom']=array();

if (pg_num_rows($Consulta) <= 0){
    $Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['campa']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['idcampa'];
    $Log['data']['geom']=array();
} else {
    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['campa']['id_p_ref_capasgeo'];
    while ($fila=pg_fetch_assoc($Consulta)){	
        $Log['data']['geom'][$fila['id']]=$fila;
        $Log['data']['geom'][$fila['id']]['valores']=array();
        $Log['data']['geom'][$fila['id']]['estadocarga']='sin carga';
    }
}



$query="
		SELECT 
			id, autor, nombre, ic_p_est_02_marcoacademico, 
			zz_borrada, 
			descripcion, 
			nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, 
			nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, 
			zz_publicada, srid, sld, tipogeometria, 
			zz_instrucciones, modo_defecto, wms_layer, zz_aux_ind
		FROM 
			geogec.ref_capasgeo
		WHERE
            id = '".$Log['data']['campa']['id_p_ref_capasgeo']."'
     ";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}
$Capa=pg_fetch_assoc($Consulta);

$Log['data']['capa']=$Capa;




// TODO
/*
$query="
	SELECT 
		ref_indicadores_valores.id,
		ref_indicadores_valores.id_p_ref_capas_registros,
	    ref_indicadores_valores.ano,
	    ref_indicadores_valores.mes,
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto1_nom = '' OR ref_indicadores_indicadores.col_texto1_nom is null) 
			AND (ref_indicadores_valores.col_texto1_dato is null OR ref_indicadores_valores.col_texto1_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto1_nom,
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto2_nom = '' OR ref_indicadores_indicadores.col_texto2_nom is null ) 
			AND (ref_indicadores_valores.col_texto2_dato is null OR ref_indicadores_valores.col_texto2_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto2_nom,

		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto3_nom = '' OR ref_indicadores_indicadores.col_texto3_nom is null ) 
			AND (ref_indicadores_valores.col_texto3_dato is null OR ref_indicadores_valores.col_texto3_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto3_nom,
			
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto4_nom = '' OR ref_indicadores_indicadores.col_texto4_nom is null) 
			AND (ref_indicadores_valores.col_texto4_dato is null OR ref_indicadores_valores.col_texto4_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto4_nom,
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_texto5_nom = '' OR ref_indicadores_indicadores.col_texto5_nom is null) 
			AND (ref_indicadores_valores.col_texto5_dato is null OR ref_indicadores_valores.col_texto5_dato = '')  
			THEN 0 ELSE 1 END AS stat_col_texto5_nom,


		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero1_nom = '' OR ref_indicadores_indicadores.col_numero1_nom is null) 
			AND (ref_indicadores_valores.col_numero1_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero1_nom,
		
					
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero2_nom = '' OR ref_indicadores_indicadores.col_numero2_nom is null) 
			AND (ref_indicadores_valores.col_numero2_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero2_nom,
		
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero3_nom = '' OR ref_indicadores_indicadores.col_numero3_nom is null) 
			AND (ref_indicadores_valores.col_numero3_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero3_nom,
		
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero4_nom = '' OR ref_indicadores_indicadores.col_numero4_nom is null) 
			AND (ref_indicadores_valores.col_numero4_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero4_nom,
		
		
		CASE WHEN 
			NOT(ref_indicadores_indicadores.col_numero5_nom = '' OR ref_indicadores_indicadores.col_numero5_nom is null) 
			AND (ref_indicadores_valores.col_numero5_dato is null )  
			THEN 0 ELSE 1 END AS stat_col_numero5_nom
		
	FROM
		geogec.ref_indicadores_indicadores
		
	LEFT JOIN
		geogec.ref_capasgeo_registros 
        ON 
                geogec.ref_indicadores_indicadores.id_p_ref_capasgeo = geogec.ref_capasgeo_registros.id_ref_capasgeo
		AND 
        		geogec.ref_capasgeo_registros.zz_borrada='0'	
			
	LEFT JOIN
		geogec.ref_indicadores_valores
        ON 
                geogec.ref_indicadores_valores.id_p_ref_indicadores_indicadores = geogec.ref_indicadores_indicadores.id
        AND
                geogec.ref_indicadores_valores.id_p_ref_capas_registros = geogec.ref_capasgeo_registros.id
        AND 
        		geogec.ref_capasgeo_registros.zz_borrada='0'
		
	WHERE
		geogec.ref_indicadores_valores.ano = '".$_POST['ano']."'
        AND
		geogec.ref_indicadores_valores.zz_superado = '0'
        AND
		geogec.ref_indicadores_valores.zz_borrado = '0'
        AND
		geogec.ref_indicadores_indicadores.id = '".$_POST['idcampa']."'

        ";


if ($Log['data']['campa']['periodicidad'] == 'mensual'){
    $query=$query."
        AND
		geogec.ref_indicadores_valores.mes = '".$_POST['mes']."'";
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
    $Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['campa']['id_p_ref_capasgeo']." -estadocarga: sin carga";
} else {
    $Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['campa']['id_p_ref_capasgeo'];
    
    while ($fila=pg_fetch_assoc($Consulta)){
    	if(!isset($Log['data']['geom'][$fila['id_p_ref_capas_registros']])){continue;}
    	
        $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='listo';
        $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['controles']=$fila;		

        foreach($fila as $k => $v){
            if (strpos($k, 'stat_col_') === false){continue;}

            if($v=='0'){
                $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
            }
        }
    }
}
	*/
	
$Log['res']="exito";
terminar($Log);
