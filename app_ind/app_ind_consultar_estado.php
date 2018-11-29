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
$Log['acc']=array();
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
	$Log['tx'][]='sesión caduca';
	$Log['acc'][]='login';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$query="SELECT  *
        FROM    geogec.ref_indicadores_indicadores
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


if ($Log['data']['indicador']['fechahasta'] < $Log['data']['indicador']['fechadesde']){
    $Log['tx'][]='la fechadesde es mayor a la fechahasta en el indicador con id '.$Log['data']['indicador']['id'];
    $Log['res']='err';
    terminar($Log);	
}




//Listado de todos los años
$years = array();
$i = 0;
for ($nYear = date('Y',strtotime($Log['data']['indicador']['fechadesde'])); 
        $nYear <= date('Y',strtotime($Log['data']['indicador']['fechahasta'])); $nYear++) {
    $years[$i] = $nYear;
    $i++;
}

$periodicidad = $Log['data']['indicador']['periodicidad'];

$Log['data']['periodos'] = array();
if ($periodicidad == 'anual'){
	
	if($Log['data']['indicador']['id_p_ref_capasgeo']<1){
		$Log['mg'][]=utf8_encode('este indicador no está completo en su definición. No tiene una capa asociada.');	
	}else{
				
	    foreach ($years as $ano) {
	        $Log['data']['periodos'][$ano] = array();
	        $geom = array();
	
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
	            $Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
	            //$Log['data']=null;
	        } else {
	            //$Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
	            while ($fila=pg_fetch_assoc($Consulta)){	
	                $geom[$fila['id']]=$fila;
	                $geom[$fila['id']]['ind_valores']=array();
	                $geom[$fila['id']]['estadocarga']='sin carga';
	                $Log['data']['periodos'][$ano]['geom'][$fila['id']]=array();
	                $Log['data']['periodos'][$ano]['geom'][$fila['id']]['estadocarga']='sin carga';
	            }
	        }
	
	
	        $query="SELECT 
	                        id, 
	                        id_p_ref_indicadores_indicadores, 
	                        ano,
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
	                        ano = '".$ano."'
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
	            if(!isset($geom[$fila['id_p_ref_capas_registros']])){
	                $Log['tx'][]='El indicador (id '.$_POST['id'].') tiene un valor asignado a un registro de capa inexistente.  id_p_ref_capas_registros: '.$fila['id_p_ref_capas_registros'];
	                $Log['tx'][]='query: '.$query;
	                continue;
	            }
	
	            $geom[$fila['id_p_ref_capas_registros']]['ind_valores']=$fila;
	        }
	
	
	        $query="
	                SELECT 
	                        ref_indicadores_valores.id,
	                        ref_indicadores_valores.id_p_ref_capas_registros,
	                        ref_indicadores_valores.ano,
	
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
	
	
	                LEFT JOIN
	                        geogec.ref_indicadores_valores
	                ON 
	                        geogec.ref_indicadores_valores.id_p_ref_indicadores_indicadores = geogec.ref_indicadores_indicadores.id
	                AND
	                        geogec.ref_indicadores_valores.id_p_ref_capas_registros = geogec.ref_capasgeo_registros.id
	
	                WHERE
	                        geogec.ref_indicadores_valores.ano = '".$ano."'
	                AND
	                        geogec.ref_indicadores_valores.zz_superado = '0'
	                AND
	                        geogec.ref_indicadores_valores.zz_borrado = '0'
	                AND
	                        geogec.ref_indicadores_indicadores.id = '".$_POST['id']."'
	
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
	            //$Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo']." -estadocarga: sin carga";
	        } else {
	            //$Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
	
	            while ($fila=pg_fetch_assoc($Consulta)){
	                if(!isset($geom[$fila['id_p_ref_capas_registros']])){continue;}
	
	                $geom[$fila['id_p_ref_capas_registros']]['estadocarga']='listo';
	                //$geom[$fila['id_p_ref_capas_registros']]['controles']=$fila;		
	
	                $Log['data']['periodos'][$ano]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='listo';
	
	                foreach($fila as $k => $v){
	                    //if($k =='id'){continue;}
	                    //if($k =='id_p_ref_capas_registros'){continue;}
	                    if (strpos($k, 'stat_col_') === false){continue;}
	
	                    if($v=='0'){
	                        $geom[$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
	
	                        $Log['data']['periodos'][$ano]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
	                    }
	                }
	            }
	        }
	
	        $estadocarga = 'sin carga';
	        $estadocargacompleto = true;
	
	        foreach ($geom as $registroGeom){
	            if ($registroGeom['estadocarga'] == 'incompleto'){
	                $estadocarga = 'incompleto';
	                $estadocargacompleto = false;
	            }
	            if ($registroGeom['estadocarga'] == 'sin carga'){
	                $estadocargacompleto = false;
	            }
	            if ($registroGeom['estadocarga'] == 'listo'){
	                $estadocarga = 'incompleto';
	                $estadocargacompleto = $estadocargacompleto && true;
	            }
	        }
	        if ($estadocargacompleto){
	            $estadocarga = 'completo';
	        }
	
	        $Log['data']['periodos'][$ano]['estado'] = $estadocarga;
	    }
	}
}

/*
 * 
 * Periodicidad Mensual
 * 
 */

if ($periodicidad == 'mensual'){
	
	if($Log['data']['indicador']['id_p_ref_capasgeo']<1){
		$Log['mg'][]=utf8_encode('este indicador no está completo en su definición. No tiene una capa asociada.');	
	}else{
	
	    $mes = date('n',strtotime($Log['data']['indicador']['fechadesde']));
	
	    foreach ($years as $ano) {
	        $Log['data']['periodos'][$ano] = array();
	        
	        if ($mes > 12){
	            $mes = 1;
	        }
			
	        while ($mes <= 12) {
	            if ($ano == date('Y',strtotime($Log['data']['indicador']['fechahasta']))
	                    && $mes > date('n',strtotime($Log['data']['indicador']['fechahasta']))){
	                break;
	            }
	            
	            $Log['data']['periodos'][$ano][$mes] = array();
	            
	            //$Log['data']['periodos'][$ano][$mes]['estado'] = calcularEstadoMes($ano, $mes);
	
	            $geom = array();
	            $Log['data']['periodos'][$ano][$mes]['geom'] = array();
	
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
	                $Log['tx'][]= "No se encontraron registros para la capa id ".$Log['data']['indicador']['id_p_ref_capasgeo']." asociada al indicador ".$_POST['id'];
	                //$Log['data']=null;
	            } else {
	                //$Log['tx'][]= "Consulta de capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
	                while ($fila=pg_fetch_assoc($Consulta)){	
	                    $geom[$fila['id']]=$fila;
	                    $geom[$fila['id']]['ind_valores']=array();
	                    $geom[$fila['id']]['estadocarga']='sin carga';
	                    $Log['data']['periodos'][$ano][$mes]['geom'][$fila['id']]=array();
	                    $Log['data']['periodos'][$ano][$mes]['geom'][$fila['id']]['estadocarga']='sin carga';
	                }
	            }
	
	
	            $query="SELECT 
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
	                            ano = '".$ano."'
	                    AND
	                            mes = '".$mes."'
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
	                if(!isset($geom[$fila['id_p_ref_capas_registros']])){continue;}
	
	                $geom[$fila['id_p_ref_capas_registros']]['ind_valores']=$fila;
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
	
	
	                    LEFT JOIN
	                            geogec.ref_indicadores_valores
	                    ON 
	                            geogec.ref_indicadores_valores.id_p_ref_indicadores_indicadores = geogec.ref_indicadores_indicadores.id
	                    AND
	                            geogec.ref_indicadores_valores.id_p_ref_capas_registros = geogec.ref_capasgeo_registros.id
	
	                    WHERE
	                            geogec.ref_indicadores_valores.ano = '".$ano."'
	                    AND
	                            geogec.ref_indicadores_valores.mes = '".$mes."'
	                    AND
	                            geogec.ref_indicadores_valores.zz_superado = '0'
	                    AND
	                            geogec.ref_indicadores_valores.zz_borrado = '0'
	                    AND
	                            geogec.ref_indicadores_indicadores.id = '".$_POST['id']."'
	
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
	                //$Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo']." -estadocarga: sin carga";
	            } else {
	                //$Log['tx'][]= "Consulta de valores asociados a la capa existente id: ".$Log['data']['indicador']['id_p_ref_capasgeo'];
	
	                while ($fila=pg_fetch_assoc($Consulta)){
	                    if(!isset($geom[$fila['id_p_ref_capas_registros']])){continue;}
	
	                    $geom[$fila['id_p_ref_capas_registros']]['estadocarga']='listo';
	                    //$geom[$fila['id_p_ref_capas_registros']]['controles']=$fila;		
	
	                    $Log['data']['periodos'][$ano][$mes]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='listo';
	
	                    foreach($fila as $k => $v){
	                        if (strpos($k, 'stat_col_') === false){continue;}
	
	                        if($v=='0'){
	                            $geom[$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
	
	                            $Log['data']['periodos'][$ano][$mes]['geom'][$fila['id_p_ref_capas_registros']]['estadocarga']='incompleto';
	                        }
	                    }
	                }
	            }
	
	            $estadocarga = 'sin carga';
	            $estadocargacompleto = true;
	
	            foreach ($geom as $registroGeom){
	                if ($registroGeom['estadocarga'] == 'incompleto'){
	                    $estadocarga = 'incompleto';
	                    $estadocargacompleto = false;
	                }
	                if ($registroGeom['estadocarga'] == 'sin carga'){
	                    $estadocargacompleto = false;
	                }
	                if ($registroGeom['estadocarga'] == 'listo'){
	                    $estadocarga = 'incompleto';
	                    $estadocargacompleto = $estadocargacompleto && true;
	                }
	            }
	            if ($estadocargacompleto){
	                $estadocarga = 'completo';
	            }
	
	            $Log['data']['periodos'][$ano][$mes]['estado'] = $estadocarga;
	            
	            $mes++;
	        }
	    }
    }
}





$query="

SELECT 
	id, geom_buffer, 
	superp_sum, id_p_ref_indicadores_indicadores, ano, mes, superp_max_numero1,
	sum_numero1, sum_numero2, sum_numero3, sum_numero4, sum_numero5, 
	prom_numero1, prom_numero2, prom_numero3, prom_numero4, prom_numero5
	

	FROM geogec.ref_indicadores_resumen
	WHERE
	id_p_ref_indicadores_indicadores='".$_POST['id']."'
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
	
	if ($periodicidad == 'anual'){
		if(!isset($Log['data']['periodos'][$fila['ano']])){continue;}
		$Log['data']['periodos'][$fila['ano']]['resumen']=$fila;
	}
	if ($periodicidad == 'mensual'){
		if(!isset($Log['data']['periodos'][$fila['ano']][$fila['mes']])){continue;}
		$Log['data']['periodos'][$fila['ano']][$fila['mes']]['resumen']=$fila;
	}
}



$Log['res']="exito";
terminar($Log);
