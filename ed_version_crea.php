<?php 
/**
*
* aplicaci�n para modificar los atributos de un registro de varsi�n candidata
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicaci�n se desarrollo sobre una publicaci�n GNU 2017 TReCC SA
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificaci�n de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', true);

session_start();

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include_once("./usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

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
	global $PROCESANDO;
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	if(isset($PROCESANDO)){
		return;	
	}else{
		echo $res;
		exit;
	}	
}


if(!isset($_POST['tabla'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear una nueva versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibio la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['accion'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear una nueva versi�n. Consulte al administrador');
	$Log['tx'][]='error, no se recibio la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

$query="
	SELECT table_name FROM information_schema.tables 
	WHERE table_schema = 'geogec' and table_name = '".$_POST['tabla']."' 
	order by table_name
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
	$Log['mg'][]='no se encontro la tabla solicitada en la base de datos';
	$Log['res']='err';
	terminar($Log);	
}


if($Usu['acc']['est']['gral']<3){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('no cuenta con permisos para generar una nueva versi�n de una capa estructural de la plataforma geoGEC');
	$Log['res']='err';
	terminar($Log);	
}



$query="
SELECT *
FROM information_schema.columns
WHERE table_schema = 'geogec'
  AND table_name   = '".$_POST['tabla']."'
 ";  
 $ConsultaTabl = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
if(pg_num_rows($ConsultaTabl)<1){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]=utf8_encode('fall� la indetificaci�n de campos paa la tabla seleccionada: '.$_POST['tabla']);
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($ConsultaTabl)){
	$Log['data']['columnas'][$fila['column_name']]=$fila['data_type'];
}


$query="
SELECT 
	*
  FROM geogec.sis_versiones
  WHERE 
  		tabla = '".$_POST['tabla']."' 
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	zz_publicada = '0'
  	AND
  		usu_autor = '".$Usu['datos']['id']."'
 ";
 
	$Log['tx'][]='query: '.$query; 
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($ConsultaVer)>0){
	$Log['tx'][]=utf8_encode('usted ya cuenta con una versi�n en proceso para esta capa');
	$Log['mg'][]=utf8_encode('usted ya cuenta con una versi�n en proceso para esta capa');
	
	$Log['data']['version']=pg_fetch_assoc($ConsultaVer);
	
	$carpeta='./documentos/subidas/ver/'.str_pad($Log['data']['version']['id'],8,"0",STR_PAD_LEFT);
	$dir=scandir($carpeta);	
	$Log['data']['archivos']=array();
	foreach($dir as $v){
		if($v=='..'){continue;}
		if($v=='.'){ continue;}
		
		$a['nom']=$v;
		
		$e=explode('.',$v);
		$ext=$e[(count($e)-1)];
		$a['ext']=$ext;
		
		$Log['data']['archivos'][]=$a;		
		
		$Log['data']['extarchivos'][$ext][]['nom']=$v;
	}


	$Log['data']['prj']['stat']='';
	$Log['data']['prj']['mg']='';
	$Log['data']['prj']['def']='';
	
	$SisRef[4326]='';
	$SisRef[3857]='';
	$SisRef[22171]='';
	$SisRef[22172]='';
	$SisRef[22173]='';
	$SisRef[22174]='';
	$SisRef[22175]='';
	$SisRef[22176]='';
	$SisRef[22177]='';
					
	$pj='';
	if(isset($Log['data']['extarchivos']['qpj'])){
		$pj=file_get_contents($carpeta.'/'.$Log['data']['extarchivos']['qpj'][0]['nom']);
	}elseif(isset($Log['data']['extarchivos']['prj'])){
		$pj=file_get_contents($carpeta.'/'.$Log['data']['extarchivos']['prj'][0]['nom']);
	}
	$Log['tx'][]=$pj;
	
	
	if($pj!=''){
		$t=explode(',',$pj);
		$final=",".$t[(count($t)-2)].",".$t[(count($t)-1)];
		$tf=explode('"',$final);
		if(strtoupper($tf[1])!='EPSG'){
			$Log['tx'][]='error: no reconocemos esta libreria de sistemas de referencia: '.strtoupper($tf[1]).' solo se admite EPSS';
			$Log['data']['extarchivos']['prj'][0]['mg']='libreria no reconocida';
			$Log['data']['extarchivos']['prj'][0]['stat']='inviable';
		}else{
			if(!isset($SisRef[$tf[3]])){
				$Log['tx'][]='error: no reconocemos esta proyeccion: '.$tf[3];
				$Log['data']['extarchivos']['prj'][0]['mg']='sistema de referencia no reconocida';
				$Log['data']['extarchivos']['prj'][0]['stat']='inviable';
				
				if($Log['data']['version']['fi_prj']!=''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='adoptado de base';
					$Log['data']['prj']['def']=$Log['data']['version']['fi_prj'];
				}
					
			}else{
				
				if($Log['data']['version']['fi_prj']==''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']=utf8_encode('adoptado de shp. Sin definici�n guardada en la base');
					$Log['data']['prj']['def']=$tf[3];
				}elseif($Log['data']['version']['fi_prj']==$tf[3]){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='coincidente de shp y base';
					$Log['data']['prj']['def']=$tf[3];
				}else{
					$Log['data']['prj']['stat']='viableobs';
					$Log['data']['prj']['mg']='error. adoptado solo de la de base. '.$Log['data']['version']['fi_prj'].' vs '.$tf[3];
					$Log['data']['prj']['def']=$Log['data']['version']['fi_prj'];
				}
				
			}
		}
	}
	
	
	$Log['data']['shp']['stat']='inviable';
	$Log['data']['shp']['mg']='no fue cargado u shapefile';
		
	$Log['data']['dbf']['stat']='inviable';
	$Log['data']['dbf']['mg']='no fue registrado un dbf';
	
	if(
		isset($Log['data']['extarchivos']['shx'])
		&&
		isset($Log['data']['extarchivos']['shp'])
		&&
		isset($Log['data']['extarchivos']['dbf'])
	){
		// Register autoloader
		
		
		
		try {
			
		    // Open shapefile
		    $ShapeFile = new ShapeFile($carpeta.'/'.$Log['data']['extarchivos']['shp'][0]['nom']);
			
			//$Log['tx'][]=$ShapeFile->valid();
			if($ShapeFile->valid()==1){
				$Log['tx'][]='shapefile valido: '.$ShapeFile->valid();
				$Log['data']['shp']['stat']='viable';	
				$Log['data']['shp']['cant']=$ShapeFile->getTotRecords();
				$Log['data']['shp']['tipo']=$ShapeFile->getShapeType(ShapeFile::FORMAT_STR);
				$Log['data']['shp']['mg']='reconocido '.$ShapeFile->getTotRecords(ShapeFile::FORMAT_STR).' registros '.$ShapeFile->getShapeType(ShapeFile::FORMAT_STR);
				$Log['tx'][]= get_class_methods($ShapeFile);


				$Log['data']['dbf']['campos']=$ShapeFile->getDBFFields();
				
				
				
				$instrucc=json_decode($Log['data']['version']['instrucciones'],true);
				$Log['tx'][]="inst:";
				$Log['tx'][]=print_r($instrucc,true);
				
				$Log['data']['columnasCubiertas']=array();
				
				foreach($Log['data']['columnas'] as $tnom => $ttipo){
					
					$Log['data']['columnasCubiertas'][$tnom]['stat']='no';
					
					if($tnom=='id'||$tnom=='geo'||$tnom=='id_sis_versiones'||$tnom=='zz_obsoleto'){
						$Log['data']['columnasCubiertas'][$tnom]['stat']='si';
						$Log['data']['columnasCubiertas'][$tnom]['dbfref']='';
						$Log['data']['columnasCubiertas'][$tnom]['dbfnom']='';
					}
					
					
					foreach($Log['data']['dbf']['campos'] as $iddbf => $v){
						
						$cnom = $v['name'];
						$Log['tx'][]=print_r($instrucc[$cnom],true);
						if(isset($instrucc[$cnom])){
							$cnom=$instrucc[$v['name']]['nom'];
						}
						
						if($tnom==$cnom){
							$Log['tx'][]=$tnom." -> ".$cnom;
							$Log['data']['columnasCubiertas'][$tnom]['stat']='si';
							$Log['data']['columnasCubiertas'][$tnom]['dbfref']=$iddbf;
							$Log['data']['columnasCubiertas'][$tnom]['dbfnom']=$v['name'];
						}
					}
				
				}
				
				
				$Log['data']['dbf']['stat']='viable';
				$Log['data']['dbf']['mg']='';
				
				foreach($Log['data']['columnasCubiertas'] as $tn => $stat){
					if($stat['stat']!='si'){
						$Log['data']['dbf']['stat']='inviable';
						$Log['data']['dbf']['mg']+='no encontrado campo en shapefile para este campo en tabla '.$tn;
					}
				}
				
	
		    }else{
		    	$Log['data']['shp']['stat']='inviable';
				$Log['data']['shp']['mg']='inviable';
		    }

 
		    
		}catch (ShapeFileException $e) {
		    // Print detailed error information
		    $Log['data']['shp']['stat']='inviable';
			$Log['data']['shp']['mg']='Error '.$e->getCode().' ('.$e->getErrorType().'): '.$e->getMessage();	    
		    
		}
				
	}

	$Log['mg']=array();
	$Log['res']='exito';
	terminar($Log);return;
}

$Log['tx'][]='esta tabla no tiene una version pendiente. se dispone a crear una nueva';

$query="
SELECT id, tabla, nombre, fechau, zz_borrada, zz_publicada, 
       zz_obsoleto
  FROM geogec.sis_versiones
  WHERE 
   tabla = '".$_POST['tabla']."'
   AND
  	zz_borrada = '0'
  	AND
  	zz_publicada = '1'
  	AND
  	zz_obsoleto = '0'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($Consultaver)<1){
	$Nombre='1';
}else{
	while($fila=pg_fetch_assoc($ConsultaVer)){}	
	$num = $fila['nombre'];
	preg_replace('/[^0-9]/', '', $num);
	$Vsuperar=$fila['id'];
	
	if($num>0){
		
		$Nombre = $num + 1;	
		
	}else{
		
		$query="
			SELECT id, tabla, nombre, fechau, zz_borrada, zz_publicada, 
			       zz_obsoleto
			  FROM geogec.sis_versiones
			  WHERE 
			  tabla = '".$_POST['tabla']."'
			  	zz_borrada = '0'
			  	AND
			  	zz_publicada = '1'
		 ";
		$ConsultaVers = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Nombre = pg_num_rows($Consultaver) + 1;
		
	}
	
}


$query="
	INSERT INTO 
		geogec.sis_versiones(
            tabla, 
            nombre, 
            usu_autor, 
            fechau)
    	VALUES (
    		'".$_POST['tabla']."', 
    		'".$Nombre."', 
    		'".$Usu['datos']['id']."',
    		'".time()."')
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
	$Nid=$fila['id'];
	$Log['tx'][]='version creada, id:'.$Nid;
	$Log['data']['nid']=$Nid;
}


$query="
SELECT 
	*
  FROM geogec.sis_versiones
  WHERE 
  		tabla = '".$_POST['tabla']."' 
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	zz_publicada = '0'
  	AND
  		usu_autor = '".$Usu['datos']['id']."'
  	AND
  		id ='".$Nid."'
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
	$Log['tx'][]='por algun motivo no encuentro la version recien generada.';
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);
}

$Log['data']['version']=pg_fetch_assoc($ConsultaVer);
$carpeta='./documentos/subidas/ver/'.str_pad($Log['data']['version']['id'],8,"0",STR_PAD_LEFT);
$dir=scandir($carpeta);	
$Log['data']['archivos']=array();
foreach($dir as $v){
	if($v=='..'){continue;}
	if($v=='.'){ continue;}
	
	$a['nom']=$v;
	
	$e=explode('.',$v);
	$ext=$e[(count($e)-1)];
	$a['ext']=$ext;
	
	$Log['data']['archivos'][]=$a;		
	
	$Log['data']['extarchivos'][$ext][]['nom']=$v;
}
	

$Log['data']['prj']['stat']='';
$Log['data']['prj']['mg']='';
$Log['data']['prj']['def']='';

$Log['data']['shp']['stat']='';
$Log['data']['shp']['mg']='';
	
$Log['res']='exito';
terminar($Log);		
?>