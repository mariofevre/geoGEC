<?php 
/**
*
*
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
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
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', true);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

if(!isset($_SESSION)) { session_start(); }

// funciones frecuentes
include($GeoGecPath."/includes/fechas.php");
include($GeoGecPath."/includes/cadenas.php");
include($GeoGecPath."/includes/pgqonect.php");
include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

require_once($GeoGecPath.'/classes/php-shapefile/src/ShapeFileAutoloader.php');
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
    if($res==''){
        $res=print_r($Log,true);
    }
    if(isset($PROCESANDO)){
        return;	
    }else{
        echo $res;
        exit;
    }	
}


$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<2){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel 2 vs nivel '.$Acc.') para generar una nueva capa en la plataforma geoGEC. En el marco de investigación código '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}


$query="SELECT  *
        FROM    information_schema.columns
        WHERE   table_schema = 'geogec'
        AND     table_name   = 'ref_capasgeo_registros'
        AND     (column_name LIKE 'texto%' OR column_name LIKE 'numero%')
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
	$Log['mg'][]=utf8_encode('fallo la identificación de campos para la tabla ref_capasgeo');
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($ConsultaTabl)){
	$Log['data']['columnas'][$fila['column_name']]=$fila['data_type'];
}

if(!isset($_POST['id'])){
	$Log['tx'][]='no fue enviada la variable id';
	$Log['res']='err';
	terminar($Log);	
}

$query="SELECT  
			*,
			zz_instrucciones as instrucciones
        FROM    
        	geogec.ref_capasgeo
        WHERE
                id = '".$_POST['id']."'
        AND
	  		zz_borrada = '0'
	  	AND
	 	 	(zz_publicada = '0'
	 	 	OR
	 	 	zz_publicada = '1')
	  	AND
	  		autor = '".$_SESSION["geogec"]["usuario"]['id']."'
";

$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

if(pg_num_rows($ConsultaVer)<=0){
    $Log['tx'][]='error: no no es posible acceder a la capa con id '.$_POST['id'];
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);
}

$Log['data']['version'] = pg_fetch_assoc($ConsultaVer);


/*
if(!isset($_SESSION['fi_prj'])) { $_SESSION['fi_prj'] = ''; }
if(!isset($_SESSION['instrucciones'])) { $_SESSION['instrucciones'] = ''; }
$Log['data']['version']['fi_prj'] = $_SESSION['fi_prj'];
$Log['data']['version']['instrucciones'] = $_SESSION['instrucciones'];
$_SESSION['instrucciones'] = '';**/

//if(isset($_SESSION['fi_prj'])) { $Log['data']['version']['fi_prj'] = $_SESSION['fi_prj']; }
//if(isset($_SESSION['instrucciones'])) { $Log['data']['version']['instrucciones'] = $_SESSION['instrucciones']; }

$carpeta=$GeoGecPath.'/documentos/subidas/capa/'.str_pad($_POST['id'],8,"0",STR_PAD_LEFT);

if(!file_exists($carpeta)){
    $Log['tx'][]="creando carpeta $carpeta";
    mkdir($carpeta, 0777, true);
    chmod($carpeta, 0777);	
}

$dir=scandir($carpeta);

$Log['data']['archivos']=array();
$Log['data']['extarchivos']=array();
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


$Log['tx'][]= 'PHP version: ' . phpversion();


$tipogeom=$Log['data']['version']['tipogeometria'];
if($tipogeom==''&& isset($_POST['tipo_geometria_form'])){
	$tipogeom=$_POST['tipo_geometria_form'];
	
	

	$query="UPDATE  
					
				geogec.ref_capasgeo
			SET
				tipogeometria='".$tipogeom."'
			WHERE
					id = '".$_POST['id']."'
			AND
				autor = '".$_SESSION["geogec"]["usuario"]['id']."'
	";
	$updatever = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	$Log['data']['version']['tipogeometria']=$tipogeom;
	
}


if($tipogeom=='Tabla'){

	if(!isset($Log['data']['extarchivos']['xlsx'])){
		$Log['tx'][]='Sin archivo xlsx guardado.';
		$Log['res']='exito';
		terminar($Log);	
		
	}
	
	$instrucc = json_decode($Log['data']['version']['instrucciones'],true);
	    
	require_once($GeoGecPath.'/external/simplexlsx/src/SimpleXLSX.php');
	$file=$carpeta.'/'.$Log['data']['extarchivos']['xlsx'][0]['nom'];
	//echo $file;
	$Log['tx'][]=$file;
	$tabla = SimpleXLSX::parse($file, 0);

	//$Log['tx'][]=print_r($tabla);
	$Filas=$tabla->rows();
	
	$Log['data']['xlsx']['campos']=Array();
	$camposxlsx=$Filas[0];
	foreach($Filas[0] as $n => $c){
		$e=explode(",",$c);
		$Columnas[$e[0]]=$n;
	}
	
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



		foreach($camposxlsx as $iddbf => $v){
			if($v==''){continue;}
			//$Log['tx'][]=print_r($v,true);
			$e=explode(',',$v);
			
			if(!isset($e[1])){
				
				$Log['mg'][]='error en el nombre de columna. debe contener un texto seguido de una coma y luego el caracter C para textos o N para numeros. Error en campo: '.$v;
				$Log['res']='err';
				terminar($Log);
			}
			if($e[1]!='C'&&$e[1]!='N'){
				$Log['mg'][]='error en el nombre de columna. debe contener un texto seguido de una coma y luego el caracter C para textos o N para numeros. Error en campo: '.$v;
				$Log['res']='err';
				terminar($Log);
			}
			
			$arr=array(
				'name'=>$e[0],
				'type'=>$e[1]
			);
			$Log['data']['xlsx']['campos'][$iddbf]=$arr;
			
			$tnom = $e[0];
			
			if(isset($instrucc[$tnom])){
				$Log['tx'][]=print_r($instrucc[$tnom],true);
				$cnom=$instrucc[$tnom]['nom'];
				$Log['tx'][]=$tnom." -> ".$cnom;
				$Log['data']['columnasCubiertas'][$cnom]['stat']='si';
				$Log['data']['columnasCubiertas'][$cnom]['dbfref']=$iddbf;
				$Log['data']['columnasCubiertas'][$cnom]['dbfnom']=$tnom;
			}else{
				
			}
		
		}
		
		
	}
	
	$Log['data']['xlsx']['stat']='viable';
	$Log['data']['xlsx']['mg']='';
	
	
	
}elseif(
	$tipogeom=='Polygon'
	||
	$tipogeom=='LineString'
	||
	$tipogeom=='Point'
){

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

	$NomSisRef['POSGAR_98_Argentina_1']='22171';
	$NomSisRef['POSGAR_98_Argentina_2']='22172';
	$NomSisRef['POSGAR_98_Argentina_3']='22173';
	$NomSisRef['POSGAR_98_Argentina_4']='22174';
	$NomSisRef['POSGAR_98_Argentina_5']='22175';
	$NomSisRef['POSGAR_98_Argentina_6']='22176';
	$NomSisRef['POSGAR_98_Argentina_7']='22177';
	
	$NomSisRef['GCS_WGS_84_CRS84']='4326';
	$NomSisRef["GCS_WGS_1984"]='4326';
	
	
	$pj='';
	if(isset($Log['data']['extarchivos']['qpj'])){
		$pj=filez($carpeta.'/'.$Log['data']['extarchivos']['qpj'][0]['nom']);
	}elseif(isset($Log['data']['extarchivos']['prj'])){
		$pj=file_get_contents($carpeta.'/'.$Log['data']['extarchivos']['prj'][0]['nom']);
	}elseif($Log['data']['version']['srid']!=null){
		$pj='generado, AUTHORITY["EPSG","4326"]]';
	}


	$Log['tx'][]=$pj;


	if($pj!=''){
		$t=explode(',',$pj);
		$final=",".$t[(count($t)-2)].",".$t[(count($t)-1)];
		$tf=explode('"',$final);
		
		$lee=str_replace(",","",$tf[0]);
		$lee=str_replace(" ","",$lee);
		$lee=strtoupper($lee);
		$lee=substr($lee,0,9);
		$Log['tx'][]=$lee;
		if($lee!='AUTHORITY'){
			$Log['tx'][]=utf8_encode('el sistema de coordenadas no denife como último parametro la autoridad (gvsig 2.x). Intentaremos reconocer su nombre');
			
			$nom=substr($pj,8,21);
			if(!isset($NomSisRef[$nom])){
				$Log['tx'][]=utf8_encode('error: no reconocemos este nombre de proyeccion: '.$nom.' solo se admiten POSGAR_98_Argentina_x');
				$Log['mg'][]=utf8_encode('error: no reconocemos este nombre de proyeccion: '.$nom.' le recomendamos no subir el archivo de proyección prj o qpj y defina la proyección válida en el formulario.');
				$Log['data']['extarchivos']['prj']['mg']='libreria no reconocida';
				$Log['data']['extarchivos']['prj']['stat']='inviable';
			}else{
				
				if($_SESSION['fi_prj']==''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']=utf8_encode('adoptado de shp. Sin definición guardada en la base');
					$Log['data']['prj']['def']=$NomSisRef[$nom];
				} elseif($_SESSION['fi_prj']==$NomSisRef[$nom]){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='coincidente de shp y base';
					$Log['data']['prj']['def']=$NomSisRef[$nom];
				} else {
					$Log['data']['prj']['stat']='viableobs';
					$Log['data']['prj']['mg']=utf8_encode('error. adoptado solo de la de base. '.$_SESSION['fi_prj'].' vs '.$NomSisRef[$nom]);
					$Log['data']['prj']['def']=$_SESSION['fi_prj'];
				}
			}
			
		}else if(strtoupper($tf[1])!='EPSG'){
			$Log['tx'][]=utf8_encode('error: no reconocemos esta libreria de sistemas de referencia: '.strtoupper($tf[1]).' solo se admite EPSG');
			$Log['data']['extarchivos']['prj']['mg']='libreria no reconocida';
			$Log['data']['extarchivos']['prj']['stat']='inviable';
		} else {
			if(!isset($SisRef[$tf[3]])){
				$Log['tx'][]='error: no reconocemos esta proyeccion: '.$tf[3];
				$Log['data']['extarchivos']['prj']['mg']='sistema de referencia no reconocida';
				$Log['data']['extarchivos']['prj']['stat']='inviable';

				if($_SESSION['fi_prj']!=''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='adoptado de base';
					$Log['data']['prj']['def']=$_SESSION['fi_prj'];
				}
			} else {
				if($_SESSION['fi_prj']==''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']=utf8_encode('adoptado de shp. Sin definición guardada en la base');
					$Log['data']['prj']['def']=$tf[3];
				} elseif($_SESSION['fi_prj']==$tf[3]){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='coincidente de shp y base';
					$Log['data']['prj']['def']=$tf[3];
				} else {
					$Log['data']['prj']['stat']='viableobs';
					$Log['data']['prj']['mg']='error. adoptado solo de la de base. '.$_SESSION['fi_prj'].' vs '.$tf[3];
					$Log['data']['prj']['def']=$_SESSION['fi_prj'];
				}
			}
		}
	}


	$Log['data']['shp']['stat']='inviable';
	$Log['data']['shp']['mg']='no fue cargado u shapefile';

	$Log['data']['dbf']['stat']='inviable';
	$Log['data']['dbf']['mg']='no fue registrado un dbf';

	if(isset($Log['data']['extarchivos']['shx'])
		&& isset($Log['data']['extarchivos']['shp'])
		&& isset($Log['data']['extarchivos']['dbf'])){
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
	 
				$instrucc = json_decode($Log['data']['version']['instrucciones'],true);
				
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
						
						$tnom = $v['name'];
						
						if(isset($instrucc[$tnom])){
							$Log['tx'][]=print_r($instrucc[$tnom],true);
							$cnom=$instrucc[$tnom]['nom'];
							$Log['tx'][]=$tnom." -> ".$cnom;
							$Log['data']['columnasCubiertas'][$cnom]['stat']='si';
							$Log['data']['columnasCubiertas'][$cnom]['dbfref']=$iddbf;
							$Log['data']['columnasCubiertas'][$cnom]['dbfnom']=$tnom;
						}else{
						}
					
					}
				}

				$Log['data']['dbf']['stat']='viable';
				$Log['data']['dbf']['mg']='';

				/*
				//Esto no lo usamos porque no todas las columnas tiene que estar cubiertas necesariamente
				foreach($Log['data']['columnasCubiertas'] as $tn => $stat){
					if($stat['stat']!='si'){
						$Log['data']['dbf']['stat']='inviable';
						$Log['data']['dbf']['mg']+='no encontrado campo en shapefile para este campo en tabla '.$tn;
					}
				}
				 */
			} else {
				$Log['data']['shp']['stat']='inviable';
				$Log['data']['shp']['mg']='inviable';
			}
		}catch (ShapeFileException $e) {
			// Print detailed error information
			$Log['data']['shp']['stat']='inviable';
			$Log['data']['shp']['mg']='Error '.$e->getCode().' ('.$e->getErrorType().'): '.$e->getMessage();	    
		}
	}
}else{
	$Log['mg'][]=utf8_encode('no se ha definido un tipo de geometría válida para procesar el archivo de datos. Variable tipogeom:'.$tipogeom);
}


$Log['res']='exito';
terminar($Log);	
