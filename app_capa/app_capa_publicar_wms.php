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
$Usu = validarUsuario(); // en ./usu_valudacion.php

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
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificación de este marco académico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

if(!isset($_POST['id']) || $_POST['id']<1){
	$Log['res']='error';
	$Log['tx'][]='falta id de capa';	
	terminar($Log);
}
if(!isset($_POST['wms_layer'])){
	$Log['res']='error';
	$Log['tx'][]='falta nomvre vista wms a consultar';	
	terminar($Log);	
}



$query="SELECT  *
        FROM    geogec.ref_capasgeo
        WHERE 
  		id='".$_POST['id']."'
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	zz_publicada = '0'
  	AND
  		autor = '".$idUsuario."'
 ";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta capa figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}

	
$query="
	SELECT 
		id, autor, nombre, ic_p_est_02_marcoacademico, zz_borrada, descripcion, nom_col_text1, nom_col_text2, nom_col_text3, nom_col_text4, nom_col_text5, nom_col_num1, nom_col_num2, nom_col_num3, nom_col_num4, nom_col_num5, zz_publicada, srid, sld, tipogeometria, zz_instrucciones
	FROM 
		geogec.ref_capasgeo
	WHERE 
		id='".$_POST['id']."'
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
    $Log['tx'][]= "No se encuentra la capa solicitdad.";
    $Log['data']=null;
    $Log['res']='err';
    terminar($Log);	
}
$campos='';
$fila=pg_fetch_assoc($Consulta);

foreach($fila as $k => $v){
	$Log['data']['capa'][$k]=utf8_encode($v);		
}

$campogeom='geom';
if($fila['tipogeometria']=='Point'){$campogeom='geom_point';}
if(	$fila['tipogeometria']=='Line'){$campogeom='geom_line';}


$query = "UPDATE
                 geogec.ref_capasgeo
          SET    
                 wms_layer = '".$_POST['wms_layer']."'
          WHERE
                 ref_capasgeo.id = '".$_POST['id']."'
          AND
                 ref_capasgeo.ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'
          AND
                 ref_capasgeo.autor='".$idUsuario."'
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

if($_POST['wms_layer']==''){
	$Log['tx'][]=utf8_encode("se apagó la publicación wms de esta capa");
	$Log['res']="exito";
	terminar($Log);
}


if($_POST['wms_layer']!="v_capas_registros_capa_".$_POST['id']){
	$Log['tx'][]=utf8_encode("falla en la seguridad, nombre de vista wms improbable");
	$Log['res']="err";
	terminar($Log);
}


$query = "
DROP VIEW IF EXISTS geogec.".$_POST['wms_layer']."
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}



$query = "
CREATE OR REPLACE VIEW geogec.".$_POST['wms_layer']." AS
 SELECT ref_capasgeo_registros.id,
    ref_capasgeo_registros.texto1,
    ref_capasgeo_registros.texto2,
    ref_capasgeo_registros.texto3,
    ref_capasgeo_registros.texto4,
    ref_capasgeo_registros.texto5,
    ref_capasgeo_registros.numero1,
    ref_capasgeo_registros.numero2,
    ref_capasgeo_registros.numero3,
    ref_capasgeo_registros.numero4,
    ref_capasgeo_registros.numero5,
    ref_capasgeo_registros.".$campogeom." AS geom,
    ref_capasgeo_registros.id_ref_capasgeo
   FROM geogec.ref_capasgeo_registros
  WHERE ref_capasgeo_registros.id_ref_capasgeo = ".$_POST['id'].";
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}


$query="
	ALTER VIEW geogec.".$_POST['wms_layer']."
	OWNER TO general
";
$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}




//$_POST['capa_ver']='014_v001';

// Open log file
$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");


$Log['tx'][]='curl de consulta iniciado';


$service = "http://190.111.246.33:8080/geoserver/"; // replace with your URL
$request = "rest/layers.json"; // to add a new workspace
$url = $service . $request;
$ch = curl_init($url);

// Optional settings for debugging
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
$passwordStr = "general:mostaza"; // replace with your username:password
curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
$buffer = curl_exec($ch); // Execute the curl request
curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile

$capas=json_decode($buffer, true); //el parametro true fuerza la salida como array, no stdClass
$Log['tx'][]='curl consulta ejecutado';
$elmiinarantes='no';
foreach($capas['layers']['layer'] as $layer){
	if($layer['name']==$_POST['wms_layer']){
		$Log['mg'][]=utf8_encode('la capa ya esta publicada en el servidor wms');
		$Log['data']['creacionWMS']='exito';// fue creada en el pasado pero al parecer no fue registrado
		$elmiinarantes='si';
	}
}


//consultar geometría
$query="
	SELECT 
		ST_Extent(".$campogeom.") as bextent,
		ST_Extent(ST_Transform(".$campogeom.",4326)) as bextentg
		
		FROM
			geogec.ref_capasgeo_registros
	WHERE 
		ref_capasgeo_registros.id_ref_capasgeo = ".$_POST['id'].";
";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}

$fila=pg_fetch_assoc($Consulta);


if($fila['bextent']==''){
	$Log['tx'][]='sin geometria accesible para epublica en wms';
	$Log['res']="exito";
	terminar($Log);
}



$Log['tx'][]='bextent: '.$fila['bextent'];

$coords=substr($fila['bextent'],4,-1);
$Log['tx'][]='coord: '.$coords;
$co=explode(',',$coords);
$c=explode(' ',$co[0]);
$xmin=$c[0];
$ymin=$c[1];
$c=explode(' ',$co[1]);
$xmax=$c[0];
$ymax=$c[1];


$coords=substr($fila['bextentg'],4,-1);
$co=explode(',',$coords);
$c=explode(' ',$co[0]);
$gxmin=$c[0];
$gymin=$c[1];
$c=explode(' ',$co[1]);
$gxmax=$c[0];
$gymax=$c[1];


/////////////////CREAR CAPA
// Initiate cURL session

$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");
//$service = "http://170.210.177.36:8080/geoserver/"; // replace with your URL
$request = "rest/workspaces"; // to add a new workspace
$url = $service . $request;

$url.="/geoGEC/datastores/geogec/featuretypes";
$ch = curl_init($url); $Log['tx'][]='curl de crecion iniciado';
// Optional settings for debugging
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
//Required POST request settings
curl_setopt($ch, CURLOPT_POST, True);
$passwordStr = "general:mostaza"; // replace with your username:password
curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
//crea un workspace llamado test_ws
curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-type: application/xml"));
				  
	
	    $xmlStr = '
<featureType>
  <name>'.$_POST['wms_layer'].'</name>
  <nativeName>'.$_POST['wms_layer'].'</nativeName>
  <namespace>
    <name>UNMgeo</name>
    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="'.$service.'rest/namespaces/geoGEC.xml" type="application/xml"/>
  </namespace>
  <title>'."capa generada automaticamente".'</title>
  <keywords>
    <string>features</string>
    <string>'."capa generada automaticamente".'</string>
  </keywords>
  <srs>EPSG:3857</srs>
  <nativeBoundingBox>
    <minx>'.$xmin.'</minx>
    <maxx>'.$xmax.'</maxx>
    <miny>'.$ymin.'</miny>
    <maxy>'.$ymax.'</maxy>
  </nativeBoundingBox>
  <latLonBoundingBox>
    <minx>'.$gxmin.'</minx>
    <maxx>'.$gxmax.'</maxx>
    <miny>'.$gymin.'</miny>
    <maxy>'.$gymax.'</maxy>
    <crs>GEOGCS[&quot;WGS84(DD)&quot;, 
  DATUM[&quot;WGS84&quot;, 
    SPHEROID[&quot;WGS84&quot;, 6378137.0, 298.257223563]], 
  PRIMEM[&quot;Greenwich&quot;, 0.0], 
  UNIT[&quot;degree&quot;, 0.017453292519943295], 
  AXIS[&quot;Geodetic longitude&quot;, EAST], 
  AXIS[&quot;Geodetic latitude&quot;, NORTH]]</crs>
  </latLonBoundingBox>
  <projectionPolicy>FORCE_DECLARED</projectionPolicy>
  <enabled>true</enabled>
  <metadata>
    <entry key="elevation">
      <dimensionInfo>
        <enabled>false</enabled>
      </dimensionInfo>
    </entry>
    <entry key="time">
      <dimensionInfo>
        <enabled>false</enabled>
        <defaultValue/>
      </dimensionInfo>
    </entry>
    <entry key="cachingEnabled">false</entry>
  </metadata>
  <store class="dataStore">
    <name>geoGEC:geogec</name>
    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="'.$service.'/rest/workspaces/geoGEC/datastores/geogec.xml" type="application/xml"/>
  </store>
  <maxFeatures>0</maxFeatures>
  <numDecimals>0</numDecimals>
  <overridingServiceSRS>false</overridingServiceSRS>
  <skipNumberMatched>false</skipNumberMatched>
  <circularArcPresent>true</circularArcPresent>
 
</featureType>
';


curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
//POST return code
$successCode = 201;


$buffer = curl_exec($ch); // Execute the curl request

$Log['tx'][]='curl ejecutado';

// Check for errors and process results
$info = curl_getinfo($ch);

$Log['tx'][]=$info;   
if ($info['http_code'] != $successCode) {

  $msgStr = "# Unsuccessful cURL request to ";
  $msgStr .= $url." [". $info['http_code']. "]\n";
  fwrite($logfh, $msgStr);
	$Log['res']='err';
	$Log['tx'][]='error al publicar en geoserver';
	$Log['tx'][]=$msgStr;
	$Log['tx'][]=$xmlStr;
	terminar($Log);		  
} else {
  $msgStr = "# Successful cURL request to ".$url."\n";
  fwrite($logfh, $msgStr);
  $Log['data']['creacionWMS']='exito';
}
fwrite($logfh, $buffer."\n");

$Log['tx'][]=$buffer;

curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile




$logfh = fopen($GeoGecPath."/app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");
///CREAR ESTILO
$request = "rest/workspaces"; // to add a new workspace
$url = $service . $request;
$url.="/geoGEC/styles";
$ch = curl_init($url); $Log['tx'][]='curl de crecion iniciado';
// Optional settings for debugging
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
curl_setopt($ch, CURLOPT_VERBOSE, true);
//curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
//Required POST request settings
curl_setopt($ch, CURLOPT_POST, True);
$passwordStr = "general:mostaza"; // replace with your username:password
curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
//crea un workspace llamado test_ws
curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-type: application/xml"));
				  
$xmlStr = $Log['data']['capa']['sld'];
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
//POST return code
$successCode = 201;


$buffer = curl_exec($ch); // Execute the curl request

$Log['tx'][]='curl de estilo ejecutado';

// Check for errors and process results
$info = curl_getinfo($ch);

$Log['tx'][]=$info;   
if ($info['http_code'] != $successCode) {

  $msgStr = "# Unsuccessful cURL request to ";
  $msgStr .= $url." [". $info['http_code']. "]\n";
  fwrite($logfh, $msgStr);
	$Log['res']='err';
	$Log['tx'][]='error al publicar en geoserver';
	$Log['tx'][]=$msgStr;
	$Log['tx'][]=$xmlStr;
	terminar($Log);		  
} else {
  $msgStr = "# Successful cURL request to ".$url."\n";
  fwrite($logfh, $msgStr);
  $Log['data']['creacionWMS']='exito';
}
fwrite($logfh, $buffer."\n");

$Log['tx'][]=$buffer;






curl_close($ch); // free resources if curl handle will not be reused
fclose($logfh);  // close logfile
$Log['tx'][]=utf8_encode("se creó la vista solicitada para la consulta wms");
$Log['data']['id']=$_POST['id'];
$Log['res']="exito";


terminar($Log);
