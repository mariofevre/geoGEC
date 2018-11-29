<?php 
/**
* aplicación de consulta de tablas disponibles clasificadas según la estructura general del proyecto.
 * Se utiliza en el índice general para obtener un listado navegable de la información.
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
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



$ID = isset($_GET['id'])?$_GET['id'] : '';

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

$Acc=0;
if(isset($_SESSION["geogec"]["usuario"]['id'])){
	include('./usuarios/usu_validacion.php');
	global $ConecSIG;
	$Usu =validarUsuario();include_once("./usuarios/usu_validacion.php");
	if(isset($Usu['acc']['general']['general']['general'])){
		$Acc=$Usu['acc']['general']['general']['general'];
	}
}

if(!isset($_POST['selecTabla'])){
	$Log['tx'][]='no fue enviada la varaibla selecttabla indicando una tabla o un conjunto de estas';
	$Log['res']='err';
	terminar($Log);	
}	


		
	$query="
		SELECT 
			table_name 
		FROM 
			information_schema.tables 
		WHERE 
			table_schema = 'geogec'
		order by 
			table_name
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
		$Log['mg'][]='no se encontraron tablas disponibles';
		$Log['res']='err';
		terminar($Log);	
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		$pre=substr($fila['table_name'],0,3);
		if($pre=='sis'){continue;}

		$Log['data']['tablas'][$pre][]=$fila['table_name'];
		$Log['data']['tablasConf'][$fila['table_name']]=array();
		
	}	
	
	$query="
	SELECT 
		id, 
		campo_id_geo, campo_id_humano, campo_desc_humano,
		tabla, nombre_humano, descripcion, 
	    resumen, tipo_geometria, crs, categoria_tabla_geogec
	  FROM geogec.sis_tablas_config
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
		$Log['mg'][]='no se encontró el proyecto solicitado en la base de datos';
		$Log['res']='err';
		terminar($Log);	
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		//if($fila=='categoria_tabla_geogec'){continue;}
		$Log['data']['tablasConf'][$fila['tabla']]=$fila;
		$Log['data']['tablasConf'][$fila['tabla']]['acciones']=array();
		
		$Acct=$Acc;
		if(isset($Usu['acc'][$fila['tabla']]['general']['general'])){
			$Acct=$Usu['acc'][$fila['tabla']]['general']['general'];
		}
		$Log['data']['tablasConf'][$fila['tabla']]['acceso']=$Acct;
	}	

	$query="
	SELECT id, tabla, accion
	  FROM geogec.sis_tablas_acciones
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
		$Log['mg'][]='no se encontró el proyecto solicitado en la base de datos';
		$Log['res']='err';
		terminar($Log);	
	}
	
	while($fila=pg_fetch_assoc($ConsultaProy)){
		$Log['data']['tablasConf'][$fila['tabla']]['acciones'][]=$fila['accion'];
		//if($fila=='categoria_tabla_geogec'){continue;}
	}	
	
	
if($_POST['selecTabla']==''){
	
}

$Log['res']='exito';
terminar($Log);		
?>


