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

if(!isset($_SESSION)) { session_start(); }

require_once($GeoGecPath.'/external/php-shapefile/src/Shapefile/ShapefileAutoloader.php');
ShapeFile\ShapeFileAutoloader::register();
// Import classes
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileWriter;
use Shapefile\Geometry\Point;
use Shapefile\Geometry\Linestring;
use Shapefile\Geometry\Polygon;
use Shapefile\Geometry\MultiPolygon;


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

if(!isset($_POST['idcapa'])){
	$Log['tx'][]='no fue enviada la variable idcapa';
	$Log['res']='err';
	terminar($Log);	
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];
$Log['data']['idcapa']=$_POST['idcapa'];

$query="
	SELECT  *
    FROM    geogec.ref_capasgeo
    WHERE 
    	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
    AND
        id='".$_POST['idcapa']."'
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
	$Log['tx'][]= "No se encontraron capas existentes para este usuario.";
	$Log['data']=null;
	terminar($Log);	
} else {
	//Asumimos que solo devuelve una fila
	$fila=pg_fetch_assoc($Consulta);
	$Log['tx'][]= "Consulta de capa existente id: ".$fila['id'];
	$Log['data']['capa']=$fila;
}

$campos='';
foreach($fila as $k => $v){
	if($v==''){continue;}
	if(substr($k,0,8)=='nom_col_'){
		$campo=str_replace('nom_col_', '', $k);
		$campo=str_replace('text', 'texto', $campo);
		$campo=str_replace('num', 'numero', $campo);
		$campos.=' r.'.$campo.', ';
		
		$ref_campos[$k]=$campo;
		
	}
}   



$CampoGeomTipo=Array(
'Polygon'=>'geom',
'LineString'=>'geom_line',
'Point'=>'geom_point'
);



$campogeom='';


$tipogeom=$fila['tipogeometria'];
if(
	$tipogeom=='Polygon'
	||
	$tipogeom=='LineString'
	||
	$tipogeom=='Point'
	){
		
	$fuentegeometria='local';
}elseif(
	$tipogeom=='Tabla'
){
	$fuentegeometria='sin geometria';

	if(
		$fila['link_capa']!=''
		&&
		$fila['link_capa']!='-1'
		&&
		$fila['link_capa_campo_local']!=''
		&&
		$fila['link_capa_campo_externo']!=''	
	){
		
		$fuentegeometria='externa_capa';
			
		$query="
			SELECT 
				c.id, c.autor, c.nombre, 
				c.ic_p_est_02_marcoacademico,
				c.tipogeometria, c.zz_instrucciones
				
			FROM 
				geogec.ref_capasgeo as c
			
			WHERE 
				id = '".$fila['link_capa']."'
		";
		$Consulta = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}	
		$link=pg_fetch_assoc($Consulta);	
		$tipogeom=$link['tipogeometria'];
		
	}elseif(
		$fila['link_capa']=='-1'
		&&
		$fila['link_capa_campo_local']!=''
		&&
		$fila['link_capa_campo_externo']!=''	
	){
				
		$fuentegeometria='externa_est01';
		$tipogeom='Polygon';
	}

}else{
	$Log['tx'][]= "No se encontraron registros para esta capa.";
	$Log['tx'][]= utf8_encode("Esta capa a�n no cuenta con un tipo de geometr�a definida.");    
	$Log['res']="exito";
	terminar($Log);
}





if($fuentegeometria=='local'){
				

$query="
	SELECT 
		id, 
		ST_AsText(".$CampoGeomTipo[$tipogeom].") as geomtx,  
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
		id_ref_capasgeo
	FROM 
		geogec.ref_capasgeo_registros
		WHERE
		id_ref_capasgeo  ='".$_POST['idcapa']."'
		AND
		zz_borrada='0'

";


}elseif($fuentegeometria=='externa_capa'){
		

	$query="
		SELECT 
			r.id,
			ST_AsText(lr.".$CampoGeomTipo[$tipogeom].") as geomtx,
			r.texto1, 
			r.texto2, 
			r.texto3, 
			r.texto4, 
			r.texto5, 
			r.numero1, 
			r.numero2, 
			r.numero3, 
			r.numero4, 
			r.numero5, 
			r.id_ref_capasgeo
		FROM 
			geogec.ref_capasgeo_registros as r
			
           LEFT JOIN
			geogec.ref_capasgeo_registros as lr ON lr.".$ref_campos[$fila['link_capa_campo_externo']]." = r.".$ref_campos[$fila['link_capa_campo_local']]." AND lr.id_ref_capasgeo = '".$fila['link_capa']."'
        WHERE 
  			r.id_ref_capasgeo = '".$_POST['idcapa']."'
		AND
			r.zz_borrada='0'

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
	
	$strcapa=str_pad($_POST['idcapa'],6,'0',STR_PAD_LEFT);



	// Open Shapefile
	
	$f=$GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.shp';
	if(file_exists($f)){unlink($f);}
	
	$f=$GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.shx';
	if(file_exists($f)){unlink($f);}
	
	$f=$GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.dbf';
	if(file_exists($f)){unlink($f);}
	
    $Shapefile = new ShapefileWriter(
						$GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.shp', [
							Shapefile::OPTION_FORCE_MULTIPART_GEOMETRIES
						]
	);
    
    $Log['data']['ruta']='/documentos/auxiliares/capa/descargas/'.$strcapa.'.shp';
    
    // Set shape type
    if($tipogeom=='Point'){
		$Shapefile->setShapeType(Shapefile::SHAPE_TYPE_POINT);
	}elseif($tipogeom=='LineString'){
		$Shapefile->setShapeType(Shapefile::SHAPE_TYPE_POLYLINE);
	}elseif($tipogeom=='Polygon'){
		$Shapefile->setShapeType(Shapefile::SHAPE_TYPE_POLYGON);
	}else{		
		$Log['mg'][]='tipo de geometria no reconocido:'.$tipogeom;
		$Log['res']='err';
		terminar($Log);	
	}
 

    // Create field structure
    
    
    $Shapefile->addNumericField('db_id', 10);
    $Log['tx'][]='campo:'.'db_id';
    $camposusados['db_id']='';
    
    $camposusados=array();
    $campodb_a_camposhp=array();
    $camposcreados=array();
    
    foreach($Log['data']['capa'] as $k => $v){
		
		$val='';
		
		if(substr($k,0,7)=='cod_col'&&$v!=''){
			$campodb=str_replace('cod_col_num','numero',$k);
			$campodb=str_replace('cod_col_text','texto',$campodb);
			$val=$v;
		}
		
		if($val!=''){
			$campodb_a_camposhp[$campodb]=$val;
			$camposusados[$val]='';
		}
	}
	
	
	
	
	foreach($Log['data']['capa'] as $k => $v){
		if(substr($k,0,7)=='nom_col'){
			$campodb=str_replace('nom_col_num','numero',$k);
			$campodb=str_replace('nom_col_text','texto',$campodb);
			if(isset($campodb_a_camposhp[$campodb])){
				//si el nombre de este campo ya fue definido por el campo de codigo de campo: cod_col_numN o cod_col_textoN. Se suspende la definici�n del nombre del campo.
				continue;
			}
			
			$vlimpio=asegurarfilename($v);
			$val=strtoupper(str_replace(' ','_',substr($vlimpio,0,8)));
			
			
			//verifica repeticiones en campos definidos
			$c=0;			
			while(isset($camposusados[$val])){
				//cambia el nombre de campo con un 
				$c++;
				//$val=str_pad($c,8,$val,STR_PAD_LEFT);
				$val=substr($val,0,(8-strlen($c))).$c;
			}
			
			if($val!=''){
				$campodb_a_camposhp[$campodb]=$val;
				$camposusados[$val]='';
			}			
		}
	}
	
	
	foreach($campodb_a_camposhp as $campodb => $nombre_campo){
		
		if(substr($campodb,0,5)=='texto'){			
			
			$Shapefile->addCharField($nombre_campo, 250);
			$camposcreados[]=$nombre_campo;
			$Log['tx'][]='campo c:'.$nombre_campo;
			
		}elseif(substr($k,8,3)=='num'){
			
			$Shapefile->addNumericField($nombre_campo, 10);
			$camposcreados[]=$nombre_campo;
			$Log['tx'][]='campo n:'.$nombre_campo;
			
		}
			
	}
   
	$Log['tx'][]="campos creados: ".print_r($camposcreados,true);

	while($fila=pg_fetch_assoc($Consulta)){
		
		try {
			
		if($tipogeom=='Point'){
			// Create en empty Linestring and initialize it with some WKT
			$reg = new Point();
			$geotx=$fila['geomtx'];
			$reg->initFromWKT($geotx);
		}elseif($tipogeom=='LineString'){
			// Create en empty Linestring and initialize it with some WKT
			$reg = new Linestring();
			$geotx=$fila['geomtx'];
			$reg->initFromWKT($geotx);
		}elseif($tipogeom=='Polygon'){
			// Create en empty Linestring and initialize it with some WKT
			if(substr($fila['geomtx'],0,12)=='MULTIPOLYGON'){
				$reg = new MultiPolygon();
			}else{
				$reg = new Polygon();
			}
			$geotx=$fila['geomtx'];
			$reg->initFromWKT($geotx);
			
		}else{		
			$Log['mg'][]='tipo de geometria no reconocido:'.$tipogeom;
			$Log['res']='err';
			terminar($Log);					
		}
		
		$reg->setData('db_id', $fila['id']);
		//$Log['tx'][]='valor db_id:'.$fila['id'];
		
		$regdata=array();
		foreach($fila as $k => $v){
			if(isset($campodb_a_camposhp[$k])){
				if($campodb_a_camposhp[$k]!=''){
					//$Log['tx'][]=$campodb_a_camposhp[$k].' -> '. $v;
					$reg->setData($campodb_a_camposhp[$k], $v);
					$regdata[$campodb_a_camposhp[$k]]= $v;
				}
			}
		}
		
		$Shapefile->writeRecord($reg);
		/*
		$reg = new Polygon();
		
		$reg->initFromWKT('POLYGON((0 0, 0 100000, 100000 100000, 100000 0, 0 0))');
		$reg->setData('db_id', $fila['id']);
		$Shapefile->writeRecord($reg);	
		*/
		} catch (ShapefileException $e) {
			// Print detailed error information
			$Log['tx'][]= $e->getErrorType()
				. "\nMessage: " . $e->getMessage()
				. "\nDetails: " . $e->getDetails()
				. "\n red id:". $fila['id']
				. "\n ". substr($geotx,0,100)."... "
				. "\n ".print_r($regdata,true);
		}
	}
	
	$Shapefile = null;
  	
  	chmod($GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.shp',0777);
  	chmod($GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.shx',0777);
  	chmod($GeoGecPath.'/documentos/auxiliares/capa/descargas/'.$strcapa.'.dbf',0777);
	
	



$Log['res']="exito";
terminar($Log);
