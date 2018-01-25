<?php 
/**
* ed_version_borra.php
*
* aplicaci�n para eliminar una versi�n candidata de archivos shapefile que no ha sido publicada.
 * un vez publicada una versi�n no puede ser eliminada. solo superada por una versi�n posterior. 
 *  
* @package    	TReCC - Mapa Visualizador de variables Ambientales. 
* @subpackage 	proyecto
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2018 TReCC SA
* @copyright	esta aplicaci�n se desarrollo sobre una publicaci�n GNU 2014 TReCC SA - http://www.trecc.com.ar/recursos/proyectoppu.htm
* @license    	http://www.gnu.org/licenses/agpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* Es decir, que debes mantener referencias a la publicaci�n original y publicar las nuevas versiones deribadas. 
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU AFFERO GENERAL PUBLIC LICENSE" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
* 
*
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificaci�n de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', '1');

session_start();

// funciones frecuentes
// funciones frecuentes
// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include("./usu_validacion.php");
$Usu= validarUsuario();

require_once('./classes/php-shapefile/src/ShapeFileAutoloader.php');
\ShapeFile\ShapeFileAutoloader::register();
// Import classes
use \ShapeFile\ShapeFile; 
use \ShapeFile\ShapeFileException;

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


if(!isset($_POST['tabla'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibi� la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['accion'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibi� la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if($_POST['accion']!='borrar candidato'){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibi� la variable borrar cantidato. unica habilitada';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para borrar una versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibi� la variable id de varsi�n';
	$Log['res']='err';
	terminar($Log);	
}


$query="
SELECT 
	*
  FROM geogec.sis_versiones
  WHERE 
  		tabla = '".$_POST['tabla']."'
  	AND 
  		id = '".$_POST['id']."'
  	AND
  		usu_autor = '".$Usu['datos']['id']."'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($ConsultaVer)<1){
	$Log['mg'][]='error interno no se encontro la versi�n con el id enviado';
	$Log['res']='err';
	terminar($Log);	
}

$f=pg_fetch_assoc($ConsultaVer);
	
if($f['zz_borrada']=='1'){
	$Log['tx'][]='error: esta versi�n ha sido eliminada previamente';
	$Log['mg'][]='error: esta versi�n ha sido eliminada previamente';
	$Log['res']='err';
	terminar($Log);	
}
if($f['zz_publicada']=='1'){
	$Log['tx'][]='error: esta versi�n ya fue publicada, no puede eliminarse. Solo sobreescribir con una versi�n nueva.';
	$Log['mg'][]='error: esta versi�n ya fue publicada, no puede eliminarse. Solo sobreescribir con una versi�n nueva.';
	$Log['res']='err';
	terminar($Log);	
}





$query="
UPDATE geogec.sis_versiones
   SET 
    zz_borrada = '1'
 WHERE 
		tabla = '".$_POST['tabla']."'
	AND 
		id = '".$_POST['id']."'
	AND
		zz_borrada = '0'
	AND
		zz_publicada = '0'
	AND
  		usu_autor = '".$Usu['datos']['id']."'
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$Log['res']='exito';
terminar($Log);		
?>


