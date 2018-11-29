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

if(!isset($_POST['id'])){
	$Log['tx'][]='no fue enviada la variable id';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_SESSION["geogec"])){
	$Log['tx'][]='sesi�n caduca';
	$Log['acc'][]='login';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$query="SELECT  
			*,
			funcionalidad
        FROM   
        	geogec.ref_indicadores_indicadores
        WHERE 
                zz_borrada = '0'
        AND
                zz_publicada = '".$_POST['zz_publicada']."'
        AND
                id = '".$_POST['id']."'
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
    $Log['tx'][]= "No se encontro el indicador id ".$_POST['id'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Log['data']['indicador']=$fila;
}


if(!isset($_POST['ano'])){
	$Log['tx'][]='no fue enviada la variable ano';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['periodo']['ano']=$_POST['ano'];
$Log['data']['periodo']['mes']=$_POST['mes'];

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
            id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
	$campogeo='geom_point';
}elseif($Capa['tipogeometria']=='LineString'){
	$campogeo='geom_line';
}else{
	$campogeo='geom';
}



$query="SELECT  
                id,
                ST_AsText(".$campogeo.") as geotx,
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
                id_ref_capasgeo,
                zz_borrada
        FROM    
                geogec.ref_capasgeo_registros
        WHERE 
                id_ref_capasgeo = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
                AND
               ref_capasgeo_registros.zz_borrada='0'
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
    $Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
    $Log['data']['geom']=array();
} else {
    $Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
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
            id = '".$Log['data']['indicador']['id_p_ref_capasgeo']."'
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
		zz_superado = '0'
        AND
		zz_borrado = '0'
        AND
		id_p_ref_indicadores_indicadores = '".$_POST['id']."'
";

if ($Log['data']['indicador']['periodicidad'] == 'mensual'){
    $query=$query."
        AND
		mes = '".$_POST['mes']."'";
}


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['data']['resumen']['sum_numero1']=0;
$Log['data']['resumen']['sum_numero2']=0;
$Log['data']['resumen']['sum_numero3']=0;
$Log['data']['resumen']['sum_numero4']=0;
$Log['data']['resumen']['sum_numero5']=0;
$Log['data']['resumen']['prom_numero1']=0;
$Log['data']['resumen']['prom_numero2']=0;
$Log['data']['resumen']['prom_numero3']=0;
$Log['data']['resumen']['prom_numero4']=0;
$Log['data']['resumen']['prom_numero5']=0;

$cont=0;


while ($fila=pg_fetch_assoc($Consulta)){
    if(!isset($Log['data']['geom'][$fila['id_p_ref_capas_registros']])){continue;}
    $Log['data']['geom'][$fila['id_p_ref_capas_registros']]['valores'][]=$fila;    
    $cont++;
	$Log['data']['resumen']['sum_numero1']+=$fila['col_numero1_dato'];
	$Log['data']['resumen']['sum_numero2']+=$fila['col_numero2_dato'];
	$Log['data']['resumen']['sum_numero3']+=$fila['col_numero3_dato'];
	$Log['data']['resumen']['sum_numero4']+=$fila['col_numero4_dato'];
	$Log['data']['resumen']['sum_numero5']+=$fila['col_numero5_dato'];    
}
if($cont>0){
	$Log['data']['resumen']['prom_numero1']=$Log['data']['resumen']['sum_numero1']/$cont;
	$Log['data']['resumen']['prom_numero2']=$Log['data']['resumen']['sum_numero2']/$cont;
	$Log['data']['resumen']['prom_numero3']=$Log['data']['resumen']['sum_numero3']/$cont;
	$Log['data']['resumen']['prom_numero4']=$Log['data']['resumen']['sum_numero4']/$cont;
	$Log['data']['resumen']['prom_numero5']=$Log['data']['resumen']['sum_numero5']/$cont;
}



$extrawhere = "";
$extracampo = "";
$extravalor = "";
$Log['data']['periodo']['ano']=$_POST['ano'];
$Log['data']['periodo']['mes']='';

if ($Log['data']['indicador']['periodicidad'] == 'mensual'){
	$extrawhere = "
		AND
			mes = '".$_POST['mes']."'
		";
	$extracampo = ' mes, ';
	$extravalor = "'".$_POST['mes']."', ";
	$Log['data']['periodo']['mes']=$_POST['mes'];
}


$query="
SELECT 
	id
	FROM 
		geogec.ref_indicadores_resumen
	WHERE
		id_p_ref_indicadores_indicadores ='".$_POST['id']."'
	AND
		ano = '".$_POST['ano']."'
	$extrawhere
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
   		
   $query="
		INSERT INTO 
			geogec.ref_indicadores_resumen( 
				id_p_ref_indicadores_indicadores, 
				".$extracampo."
				ano,
				sum_numero1,
				sum_numero2,
				sum_numero3,
				sum_numero4,
				sum_numero5,
				prom_numero1,
				prom_numero2,
				prom_numero3,
				prom_numero4,
				prom_numero5
			)
			VALUES (
				'".$_POST['id']."',
				".$extravalor."
				'".$_POST['ano']."',
				'".$Log['data']['resumen']['sum_numero1']."',
				'".$Log['data']['resumen']['sum_numero2']."',
				'".$Log['data']['resumen']['sum_numero3']."',
				'".$Log['data']['resumen']['sum_numero4']."',
				'".$Log['data']['resumen']['sum_numero5']."',
				'".$Log['data']['resumen']['prom_numero1']."',
				'".$Log['data']['resumen']['prom_numero2']."',
				'".$Log['data']['resumen']['prom_numero3']."',
				'".$Log['data']['resumen']['prom_numero4']."',
				'".$Log['data']['resumen']['prom_numero5']."'
			)
			
   ";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}	   
}else{

   $query="
		UPDATE 
			geogec.ref_indicadores_resumen
			
		SET( 
			id_p_ref_indicadores_indicadores, 
			".$extracampo."
			ano,
			sum_numero1,
			sum_numero2,
			sum_numero3,
			sum_numero4,
			sum_numero5,
			prom_numero1,
			prom_numero2,
			prom_numero3,
			prom_numero4,
			prom_numero5
		)
		= (
			'".$_POST['id']."',
			".$extravalor."
			'".$_POST['ano']."',
			'".$Log['data']['resumen']['sum_numero1']."',
			'".$Log['data']['resumen']['sum_numero2']."',
			'".$Log['data']['resumen']['sum_numero3']."',
			'".$Log['data']['resumen']['sum_numero4']."',
			'".$Log['data']['resumen']['sum_numero5']."',
			'".$Log['data']['resumen']['prom_numero1']."',
			'".$Log['data']['resumen']['prom_numero2']."',
			'".$Log['data']['resumen']['prom_numero3']."',
			'".$Log['data']['resumen']['prom_numero4']."',
			'".$Log['data']['resumen']['prom_numero5']."'
		)
		WHERE
			id_p_ref_indicadores_indicadores = '".$_POST['id']."'
			".$extrawhere."
		AND 
			ano = '".$_POST['ano']."'
	
   ";
	$Consulta = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
	    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	    $Log['tx'][]='query: '.$query;
	    $Log['mg'][]='error interno';
	    $Log['res']='err';
	    terminar($Log);	
	}	   		
}	










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
		geogec.ref_indicadores_indicadores.id = '".$_POST['id']."'

        ";


if ($Log['data']['indicador']['periodicidad'] == 'mensual'){
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
    $Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo']." -estadocarga: sin carga";
} else {
    $Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
    
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
	
if($Log['data']['indicador']['funcionalidad']=='nuevaGeometria'){
	
	foreach($Log['data']['geom'] as $idgeom => $data){	
		if($data['estadocarga']=='sin carga'){
			unset($Log['data']['geom'][$idgeom]);
		}
	}	
}
$Log['res']="exito";
terminar($Log);
