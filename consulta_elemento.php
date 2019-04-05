<?php 
/**

* consulta de un elemento puntual de alguna de las tablas de la base de datos.
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @copyright	2018 Universidad de Buenos Aires
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', '1');

if(!isset($_SESSION)) { session_start(); }

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");


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

if(!isset($_POST['tabla'])){
	$Log['tx'][]='no fue enviada la varaibla tabla indicando una tabla o un conjunto de estas';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['id'])&&!isset($_POST['cod'])){
	$Log['tx'][]='no fue enviada alguna variable de identificacion id o cod';
	$Log['res']='err';
	terminar($Log);	
}	

if(!isset($_POST['id'])){$_POST['id']=0;}
if($_POST['id']==''){$_POST['id']=0;}


$Acc=0;
if(isset($_SESSION["geogec"]["usuario"]['id'])){

	include_once('./usuarios/usu_validacion.php');
	global $ConecSIG;
	$Usu =validarUsuario();//en include_once("./usuarios/usu_validacion.php");
	
	if(isset($Usu['acc']['general']['general']['general'])){
		$Acc=$Usu['acc']['general']['general']['general'];
	}
	if(isset($Usu['acc'][$_POST['tabla']]['general']['general'])){
		$Acc=$Usu['acc'][$_POST['tabla']]['general']['general'];
	}
}

$query="
	SELECT 		
		id, 
		campo_id_geo, campo_id_humano, campo_desc_humano,
		tabla, nombre_humano, descripcion, 
	    resumen, tipo_geometria, crs, categoria_tabla_geogec
	  FROM geogec.sis_tablas_config
	  WHERE
	  tabla = '".$_POST['tabla']."'
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	if(pg_num_rows($ConsultaProy)<1){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]=utf8_encode('no se encontró el proyecto solicitado en la base de datos');
		$Log['res']='err';
		terminar($Log);	
	}
	

	while($fila=pg_fetch_assoc($ConsultaProy)){
		//if($fila=='categoria_tabla_geogec'){continue;}
		$Log['data']['tablasConf']=$fila;
		$Log['data']['tablasConf']['acciones']=array();
	}	

	$query="
	SELECT 
		tabla, accion, resumen, accmin
	  FROM 
	  	geogec.sis_tablas_acciones,
	  	geogec.sis_acciones
	  WHERE 
	  	tabla='".$_POST['tabla']."'
	  	AND 
	  	sis_acciones.codigo=sis_tablas_acciones.accion
	";
	$ConsultaProy = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		$Log['data']['tablasConf']['acciones'][$fila['accion']]=$fila;
		//if($fila=='categoria_tabla_geogec'){continue;}
	}	

		
	$query="
		SELECT 
			*,
			ST_AsText(geo) as geotx
		FROM 
			geogec.".$_POST['tabla']."
		WHERE 
		 zz_obsoleto = '0'
		 AND
		 (
			\"".$Log['data']['tablasConf']['campo_id_geo']."\" = '".$_POST['cod']."'
		OR			
			id = '".$_POST['id']."'
			)
			
			
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
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
			
		if(isset($Usu['acc'])){
			if(isset($Usu['acc'][$_POST['tabla']][$_POST['cod']]['general'])){
				$Acc=$Usu['acc'][$_POST['tabla']][$_POST['cod']]['general'];
			}
		}
		
		if(isset($fila['zz_accesolibre'])){
			if($fila['zz_accesolibre']=='1'){
				$Acc=max('2',$Acc);
			}
		}
		
		$Log['data']['elemento']=$fila;
		$Log['data']['elemento']['acceso']=$Acc;
		
		foreach($Log['data']['tablasConf']['acciones'] as $accion => $accdata){
			$accAccion=$Acc;
			if(isset($Usu['acc'][$_POST['tabla']][$_POST['cod']][$accion])){
				$accAccion=$Usu['acc'][$_POST['tabla']][$_POST['cod']][$accion];
			}
			$Log['data']['elemento']['accesoAccion'][$accion]=$Acc;
		}
	}	

$Log['res']='exito';
terminar($Log);		
?>


