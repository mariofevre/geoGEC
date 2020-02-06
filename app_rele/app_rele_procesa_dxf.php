<?php 
/**
*
* aplicaci�n para procesar una versi�n candidta como definitiva.
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
ini_set('display_errors', 1);
$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

// funciones frecuentes
include($GeoGecPath."/includes/encabezado.php");
include($GeoGecPath."/includes/pgqonect.php");

include_once($GeoGecPath."/usuarios/usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php
$idUsuario = $_SESSION["geogec"]["usuario"]['id'];


global $PROCESANDO;
$PROCESANDO='si';

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

if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='err';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}

if(!isset($_POST['archivo_nom'])){
	$Log['res']='err';
	$Log['tx'][]='falta nombre del archivo';	
	terminar($Log);
}
if($_POST['archivo_nom']==''){
	$Log['res']='err';
	$Log['tx'][]='falta nombre del archivo';	
	terminar($Log);
}


$query="
SELECT 
	id, nombre, descripcion, id_p_ref_capasgeo, ic_p_est_02_marcoacademico, 
	fechadesde, fechahasta, usu_autor, zz_borrada, zz_publicada, 
	col_texto1_nom, col_texto2_nom, col_texto3_nom, col_texto4_nom, col_texto5_nom, col_numero1_nom, col_numero2_nom, col_numero3_nom, col_numero4_nom, col_numero5_nom, col_texto1_unidad, col_texto2_unidad, col_texto3_unidad, col_texto4_unidad, col_texto5_unidad, col_numero1_unidad, col_numero2_unidad, col_numero3_unidad, col_numero4_unidad, col_numero5_unidad, representar_campo, representar_val_max, representar_val_min, zz_borrada_usu, zz_borrada_utime, col_texto6_nom, col_texto7_nom, col_texto8_nom, col_texto9_nom, col_texto10_nom
	FROM geogec.ref_rele_campa
	WHERE
	id = '".$_POST['idcampa']."'
	AND
	ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
";
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	
$f=pg_fetch_assoc($Consulta);

if($f['id_p_ref_capasgeo']>0){
	
	$IdCapa=$f['id_p_ref_capasgeo'];

}else{
				
	$query="
		
		INSERT INTO 
			geogec.ref_capasgeo(
				autor, 
				nombre,
				descripcion, 
				ic_p_est_02_marcoacademico, 
				srid, 
				zz_aux_rele)
			VALUES (
				'".$idUsuario."',
				'auxiliar rele ".$f['id']."',
				'capa auxiliar para relevamiento ".$f['nombre']."',
				'".$_POST['codMarco']."', 
				'3857', 
				'".$f['id']."')
			RETURNING id		
	";
	$Consulta = pg_query($ConecSIG,$query);
	if(pg_errormessage($ConecSIG)!=''){
        $Log['res']='error';
        $Log['tx'][]='error al intentar crear una capa para este relevamiento';
        $Log['tx'][]=pg_errormessage($ConecSIG);
        $Log['tx'][]=$query;
        terminar($Log);
    }
	$f2=pg_fetch_assoc($Consulta);
	$IdCapa=$f2['id'];
	$Log['tx'][]='creada capa auxiliar con id: '.$IdCapa;
	
	$query="
		UPDATE 
			geogec.ref_rele_campa
		SET 
			id_p_ref_capasgeo='".$IdCapa."'
		WHERE 
			id = '".$_POST['idcampa']."'
		AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
		";
	$Consulta = pg_query($ConecSIG,$query);
	if(pg_errormessage($ConecSIG)!=''){
        $Log['res']='error';
        $Log['tx'][]='error al intentar crear una capa para este relevamiento';
        $Log['tx'][]=pg_errormessage($ConecSIG);
        $Log['tx'][]=$query;
        terminar($Log);
    }		
	
}


$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if(!isset($_POST['avance'])){
	$_POST['avance']=0;
}

$carpeta=$GeoGecPath;
$carpeta.='/documentos/subidas/rele/';
$carpeta.=str_pad($_POST['idcampa'],8,"0",STR_PAD_LEFT);
$carpeta.='/'.str_pad($idUsuario,8,"0",STR_PAD_LEFT);

$archivo=$carpeta.='/'.$_POST['archivo_nom'];

$contenido=file_get_contents($archivo);

// return mime type ala mimetype extension
$finfo = finfo_open(FILEINFO_MIME);

//check to see if the mime-type starts with 'text'

$Log['tx'][]='mime:'.finfo_file($finfo, $archivo);
	
if(substr(finfo_file($finfo, $archivo), 0, 4) == 'text'){
	$Log['tx'][]='al parecer es formato ASCII';
}else{
	$Log['tx'][]='al parecer es formato binario... procesando';
	$output = '';
	for($i=0; $i<strlen($contenido); $i+=8) {
	  $output .= chr(intval(substr($contenido, $i, 8), 2));
	}
	$contenido=$output;
}

$array = preg_split("/\r\n|\n|\r/", $contenido);

$tot=count($array);
$Log['tx'][]='se identificaron '.count($array).' filas';

$estado='iniciando';

$coords='';
$tipo='sd';
$ent=0;
foreach($array as $k => $v){
	
	if(
		$estado=='iniciando'		 
	){
		if($v=='ENTITIES'){
			$estado='entidades';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}

	if(
		$estado=='entidades'
	){
		//$Log['tx'][]='v: '.$v;
		if($v==="LWPOLYLINE"){
			$ent++;
			if($_POST['avance']>=$ent){continue;}
			
			$_POST['avance']=$ent;
			
			$primercargado='no';
			$primerpunto='';
			
			$puntos=0;
			$tipo='Polygon';
			$coords.='POLYGON((';
			$estado='poli';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
		
		$Log['tx'][]='v: '.$v;
		if($v==="POLYLINE"){
			$ent++;
			if($_POST['avance']>=$ent){continue;}
			
			$_POST['avance']=$ent;
			
			$primercargado='no';
			$primerpunto='';
			
			$tipo='Polygon';
			$coords.='POLYGON((';
			$estado='poli2d';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}
	
	if(
		$estado=='poli'
	){
		if($v==="AcDbPolyline"){
			$estado='geometria';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}
	
	if(
		$estado=='geometria'
		||
		$estado=='y-fin'
	){
		if($v===" 10"){
			$estado='x';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}

	if(
		$estado=='x'
	){
		$coords.=$v.' ';
		$estado='x-fin';
		//$Log['tx'][]='estado: '.$estado;
		
		if($primercargado=='no'){
			$primerpunto.=$v.' ';
		}
		
		continue;
	}
	
	if(
		$estado=='x-fin'
	){
		if($v===" 20"){
			$estado='y';
			$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}
		
	if(
		$estado=='y'
	){
		$coords.=$v.', ';
		$estado='y-fin';
		//$Log['tx'][]='estado: '.$estado;
		
		if($primercargado=='no'){
			$primerpunto.=$v;
			$primercargado='si';
			$puntos=0;
		}
		$puntos++;
		
		continue;
	}
	
	if(
		$estado=='y-fin'
	){
		if($v=="  0"){
			
			$coords.=$primerpunto.'))';
			$estado='entidades';
			$Log['tx'][]='estado: '.$estado;
			$Log['tx'][]='creando entidad '.$coords;
			$avancep=100*$v/$tot;
			
			if($tipo=='Polygon'&&$puntos<3){
				$Log['tx'][]=utf8_encode('poligono con cantidad de puntos insuficientes. salteado'.$coords);
				$Log['mg'][]=utf8_encode('poligono con cantidad de puntos insuficientes. salteado'.$coords);	
			}else{
			
				guardarGeometria($coords,$tipo);
			}
			$coords='';
			$carga++;
			if($carga==100000){
				$Log['data']['avance']=$_POST['avance'];	
				$Log['res']='exito';	
				terminar($Log);
			}
			
			continue;
		}
	}
	
if(
		$estado=='poli2d'
		||
		$estado=='poli2d_y-fin'
	){
		if($v=="AcDb2dVertex"){
			$estado='poli2d_geometria';
			$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}
	
	if(
		$estado=='poli2d_geometria'		
	){
		if($v==" 10"){
			$estado='poli2d_x';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}

	if(
		$estado=='poli2d_x'
	){
		$coords.=$v.' ';
		$estado='poli2d_x-fin';
		//$Log['tx'][]='estado: '.$estado;
		
		if($primercargado=='no'){
			$primerpunto.=$v.' ';
		}
		
		continue;
	}
	
	if(
		$estado=='poli2d_x-fin'
	){
		if($v==" 20"){
			$estado='poli2d_y';
			//$Log['tx'][]='estado: '.$estado;
			continue;
		}
	}
		
	if(
		$estado=='poli2d_y'
	){
		$coords.=$v.', ';
		$estado='poli2d_y-fin';
		$Log['tx'][]='estado: '.$estado;
		
		if($primercargado=='no'){
			$primerpunto.=$v;
			$primercargado='si';
			$puntos=0;
		}
		$puntos++;
		continue;
	}
	
	if(
		$estado=='poli2d_y-fin'
	){
		if($v=="SEQEND"){
			
			$coords.=$primerpunto.'))';
			$estado='entidades';
			$Log['tx'][]='estado: '.$estado;
			$Log['tx'][]='creando entidad '.$coords;
			$avancep=100*$v/$tot;
			guardarGeometria($coords,$tipo);
			
			$coords='';
			$carga++;
			if($carga==100000){
				$Log['data']['avance']=$_POST['avance'];	
				$Log['res']='exito';	
				terminar($Log);
			}
			
			continue;
		}
	}	
	
}



function guardarGeometria($coords,$tipo){
	global $Log, $ConecSIG, $IdCapa, $avancep, $carga, $idUsuario;
 	
	$geomTX= "ST_GeomFromText('".$coords."', 22175)";
    $geomTX= "ST_Transform(".$geomTX.", 3857)";

    if($tipo == 'Polygon'){
        $campo_g = 'geom';
    } elseif ($tipo == 'Point'){
        $campo_g = 'geom_point';
    } elseif ($tipo == 'LineString'){
        $campo_g = 'geom_line';
    } else {
        $Log['res']='error';
        $Log['mg'][]='No reconci el tipo de geometria';
        terminar($Log);
    }

    $query="INSERT INTO 
                geogec.ref_capasgeo_registros(
                    ".$campo_g.", 
                    id_ref_capasgeo,
                    zz_auto_crea_usu,
                    zz_auto_crea_fechau
                )
            VALUES (
                    ".$geomTX.",
                    '".$IdCapa."',
                    '".$idUsuario."',
                    '".time()."'
                )
            RETURNING id;
    ";
    //$Log['tx'][]=$query;
    $Consulta = pg_query($ConecSIG,utf8_encode($query));
    //$Log['tx'][]=$query;
    $carga++;
    if(pg_errormessage($ConecSIG)!=''){
        $Log['res']='error';
        $Log['tx'][]='error al insertar registro en la base de datos';
        $Log['tx'][]=pg_errormessage($ConecSIG);
        $Log['tx'][]=$query;
        terminar($Log);
    }	
    $f=pg_fetch_assoc($Consulta);
    $Log['data']['inserts'][]=$f['id'];

    $Log['data']['avanceP']=$avancep;

	
	
}

$Log['data']['avance']='final';
$Log['res']='exito';	
terminar($Log);
