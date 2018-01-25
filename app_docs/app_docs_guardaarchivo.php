<?php
/**
* guarda un archivo en el servidor y lo registra en la base de datos.
*
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
* 
*
*/


ini_set('display_errors', '1');

session_start();

chdir(getcwd().'/../'); 

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include_once("./usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php


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

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la varaible codMarco';
	$Log['res']='err';
	terminar($Log);	
}	

if($Usu['acc']['ref'][$_POST['codMarco']]<2){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('no cuenta con permisos para generar archivos y cajas en  tablas tipo ref para el marco académico id:'.$_POST['codMarco']);
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['nfile'])){
	$Log['tx'][]='no fue definido el tipo de contenido';
	$Log['res']='err';
	terminar($Log);
}


if(!isset($_FILES['upload'])){
	$Log['tx'][]='no fue enviada la imagen en la variable FILES[upload]';
	$Log['res']='err';
	terminar($Log);
}

	$Log['tx'][]= "archivo enviado";
	
	$ArchivoOrig = $_FILES['upload']['name'];	
	$Log['tx'][]= "cargando: ".$ArchivoOrig;
	
	$b = explode(".",$ArchivoOrig);
	$ext = strtolower($b[(count($b)-1)]);	
	
	$idmarcpad=str_pad($_POST['codMarco'],8,"0",STR_PAD_LEFT);
	$PathBase="./documentos/referencias/".$idmarcpad."/";
	
	$path=$PathBase;
	$carpetas= explode("/",$path);	
	$rutaacumulada="";			
	foreach($carpetas as $valor){		
	$Log['tx'][]= "instancia de ruta: $valor ";
	$rutaacumulada.=$valor."/";
		if (!file_exists($rutaacumulada)&&$valor!=''){
			$Log['tx'][]="creando: $rutaacumulada ";
		    mkdir($rutaacumulada, 0777, true);
		    chmod($rutaacumulada, 0777);
		}
	}		
	// FIN verificar y crear directorio				
										
	$nombretipo = ref_01_."[NID]_";
	$nombre=$nombretipo;
	$nombreprliminar='si';//indica que el documento debe ser renombrado luego de creado el registro.			
	
	$c=explode('.',$nombre);

	$cod = cadenaArchivo(10); // define un código que evita la predictivilidad de los documentos ante búsquedas maliciosas
	$nombre=$path.$c[0].$cod.".".$ext;
	
	/*
	$extVal['jpg']='1';
	$extVal['png']='1';
	$extVal['tif']='1';
	$extVal['bmp']='1';
	$extVal['gif']='1';
	//$extVal['pdf']='1';
	//$extVal['zip']='1';
	*/
	
	//if(isset($extVal[strtolower($ext)])){
		$Log['tx'][]= "guardado en: ".$nombre."<br>";
		
		if (!copy($_FILES['upload']['tmp_name'], $nombre)) {
		   	$Log['tx'][]= "Error al copiar $pathI...\n";
			$Log['res']='err';
			terminar($Log);
		}else{
			chmod($nombre, 0777);
			$Log['tx'][]= "archivo guardado";
		}
	/*}else{
		$ms="solo se aceptan los formatos:";
		foreach($extVal as $k => $v){$ms.=" $k,";}
		$Log['mg'][]= $ms;
		$ArchivoOrig='';
		$Log['res']='err';
		terminar($Log);
	}*/	

	
	$nombreGuard=str_replace("../", "./", $nombre);
		
	$query="
	INSERT INTO 
		geogec.ref_01_documentos(
			archivo,
			nombre,
			ic_p_est_02_marcoacademico
		)
		VALUES(
			'".$nombreGuard."',
			'".$ArchivoOrig."',
			'".$_POST['codMarco']."'	
		)
		RETURNING id
	";
	$ConsultaVer = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	while($fila=pg_fetch_assoc($ConsultaVer)){
		$NID=$fila['id'];
		$Log['tx'][]='item creado, id:'.$Nid;
		$Log['data']['nid']=$Nid;
	}
	
	
	$nuevonombre=str_replace("[NID]", $NID, $nombre);
	$nuevonombreGuard=str_replace("../", "./", $nuevonombre);
	$Log['data']['ruta']=$nuevonombreGuard;
	
	if(!rename($nombre,$nuevonombre)){		
	 	$Log['tx'][]=" error al renombrar el documento ".$origen['nombre']." con el nuevo id => $nuevonombre";
		$Log['res']='err';
		terminar($Log);	
	}else{
	 	$query="
	 		UPDATE 
	 			geogec.ref_01_documentos
	 		SET 
	 			archivo = '".$nuevonombreGuard."'
	 		WHERE
	 			id='".$NID."'
	 			AND
	 			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
	 	";
	 	
		 pg_query($ConecSIG, $query);
	
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}	
	}

//echo $query;
$Log['data']['nid']=$NID;
$Log['data']['nf']=$_POST['nfile'];
$Log['data']['ruta']=$nuevonombreGuard;
$Log['tx'][]='completado';
$Log['res']='exito';
terminar($Log);
?>