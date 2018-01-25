<?php
/**
 * 
 aplicaci�n para guardar archivos cargados en el sevidor 
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

include ('./includes/encabezado.php');
include('./includes/pgConect.php');	
ini_set('display_errors', '1');

include_once("./usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php


function terminar($Log){
	$res=json_encode($Log);
	if($res==''){
		echo "Error al codificar en json el resultado".PHP_EOL;		
		print_r($Log);
	}
	else{
		echo $res;
	}
	exit;	
}



$Log['data']['ncont']=$_POST['cont'];

if(!isset($_POST['idver'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de version';	
	terminar($Log);
}

if($_POST['idver']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de version';	
	terminar($Log);
}
			


$query="
SELECT 
	*
  FROM geogec.sis_versiones
  WHERE 
  		id='".$_POST['idver']."'
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




$fila=pg_fetch_assoc($ConsultaVer);

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta versi�n figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['zz_publicada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta versi�n ya figura como publicada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['usu_autor']!=$Usu['datos']['id']){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='usted no figura como el autor de esta versi�n: '.$Usu['datos']['id'];
	$Log['res']='err';
	terminar($Log);	
}

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");
	


$carpeta='./documentos/subidas/ver/'.str_pad($_POST['idver'],8,"0",STR_PAD_LEFT);
if(!file_exists($carpeta)){
	$Log['tx'][]="creando carpeta $carpeta";mkdir($carpeta, 0777, true);chmod($carpeta, 0777);	
}


$nombre = $_FILES['upload']['name'];
$Log['data']['nombreorig']=$nombre;
$b = explode(".",$nombre);
$extO =$b[(count($b)-1)];
$ext = strtolower($extO);
$nombreSinExt=str_replace(".".$extO, "", $nombre);

$extVal['prj']='1';
$extVal['qpj']='1';
$extVal['dbf']='1';
$extVal['shp']='1';
$extVal['shx']='1';
$extVal['cpg']='1';


if(!isset($extVal[strtolower($ext)])){	
	$Log['tx'][]="solo se aceptan los formatos:";
	foreach($extVal as $k => $v){$Log['tx'][]=" $k,";}
	$Log['tx'][]="archivo cargado: ".$nombre;
	$Log['res']='err';
	terminar($Log);
}


$Log['tx'][]="cargando archivo en modo shapefile.";
$Log['data']['cargado']='shapefile parcial';

$fn = $nombre;	
$nom=substr($fn,0,-4);
$nom=str_replace("-","_",$nom);
$nom=str_replace(" ","_",$nom);
$e=explode("_",$nom);

$nuevonombre= $carpeta.'/'.$nombreSinExt.'.'.strtolower($ext);
//echo "<br>var:".$e[0] ."esc:".$e[1]."per:".$e[2].alt:".$e[3];	

if (!copy($_FILES['upload']['tmp_name'], $nuevonombre)){
    $Log['tx'][]="Error al copiar $nuevonombre";
	$Log['res']="err";
	terminar($Log);
}

$Log['tx'][]="copiado archivo punto ".strtolower($ext);
$Log['res']="exito";
terminar($Log);