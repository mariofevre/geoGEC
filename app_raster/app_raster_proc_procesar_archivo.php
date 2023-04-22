<?php
/**
 * 
 aplicación para procesar archivos raster e incorporarlos a la base de datos como capas raster
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


chdir('..');
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



$minacc=2;
if(isset($_POST['nivelPermiso'])){
    $minacc=$_POST['nivelPermiso'];
}

$Acc=0;
$Accion='app_raster';
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']][$Accion];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minacc){
    $Log['mg'][]=utf8_encode('no cuenta con permisos para gerear capas raster. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
    $Log['tx'][]=print_r($Usu,true);
    $Log['res']='err';
    terminar($Log);
}
$idUsuario = $_SESSION["geogec"]["usuario"]['id'];




if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['idraster'])){
	$Log['tx'][]='no fue enviada la variable idraster';
	$Log['res']='err';
	terminar($Log);	
}


if(!isset($_POST['iddoc'])){
	$Log['tx'][]='no fue enviada la variable iddoc';
	$Log['res']='err';
	terminar($Log);	
}

$Log['data']['iddoc']=$_POST['iddoc'];

if($_POST['idraster']<1){
	
	$query="
		INSERT INTO geogec.ref_raster_coberturas(
			autor, nombre, descripcion, ic_p_est_02_marcoacademico			
		)
		VALUES (
		 '".$idUsuario."', 
		 'nueva', 
		 'nueva', 
		 '".$_POST['codMarco']."'
		)
		RETURNING id
		";
	$Resultado = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	$row=pg_fetch_assoc($Resultado);
	$_POST['idraster']=$row['id'];
	$Log['data']['nid']=$_POST['idraster'];
	$Log['tx'][]='sea ha creado un nuevo registro en ref_raster_coberturas';
}

$carpetaid=str_pad($_POST['idraster'],8,'0',STR_PAD_LEFT);// id carpeta destino auxiliar para EXTRAER ZIP
///$zipfilename ='/var/www/html/geoGEC/documentos/referencias/DATAENTRY2022/ref_01_767_9H8ADeLEwI.zip'; EJEMPLO
$destino = './documentos/auxiliares/raster/'.$carpetaid.'/';// carpeta destino auxiliar para EXTRAER ZIP
$Log['tx'][]='destino: '.$destino;
if(file_exists($destino)){// si ya existia la borramos
	$comando='rm -r '.$destino;
	exec($comando,$exec_res);
	$Log['tx'][]='eliminando carpeta preexistente: '.print_r($exec_res,true);
}

if(!file_exists($destino)){
    $Log['tx'][]="creando carpeta $destino";
    mkdir($destino, 0777, true);// y la creamos vacía
    chmod($destino, 0777);	
}



if($_POST['iddoc']<1){
	
	// No contamos con datos para añadir. terminamos el comando.	
	$Log['tx'][]="no se definio zip con contenido";
	$Log['res']="exito";
	terminar($Log);	
	
}else{
	$query="
		SELECT 
			id, id_p_est_02_marcoacademico, zz_borrada, nombre, archivo, id_p_ref_02_pseudocarpetas, orden, 
			descripcion, ic_p_est_02_marcoacademico, zz_protegida, zz_preliminar, zz_auto_crea_usu
		FROM 
			geogec.ref_01_documentos
		WHERE
			id = '".$_POST['iddoc']."'
		AND
			ic_p_est_02_marcoacademico = '".$_POST['codMarco']."'	
	";

	$Resultado = pg_query($ConecSIG, $query);
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
		$Log['tx'][]='query: '.$query;
		$Log['mg'][]='error interno';
		$Log['res']='err';
		terminar($Log);	
	}
	while ($row=pg_fetch_assoc($Resultado)){
		$zipfilename=$row['archivo'];
	}	
}


$comando='unzip '.$zipfilename.' -d '.$destino;
exec($comando,$exec_res);
$Log['tx'][]=$comando;
$Log['tx'][]='dezipeando: '.print_r($exec_res,true);




///encontrar  MTD_MSIL2A.xml (en carpeta única en raiz)
$dezip=scandir($destino);

foreach($dezip as $c){
	if($c=='.'){continue;}
	if($c=='..'){continue;}
	$Log['tx'][]='carpeta raiz: '.$c;
	$carpetaraiz = $c;
}

if(!isset($carpetaraiz)){
	$Log['tx'][]='No pudo interpretarse el contenido el archivo comrimido';
	$Log['mg'][]='No pudo interpretarse el contenido el archivo comrimido';
	$Log['res']='err';
	terminar($Log);
}

$filename ='./documentos/auxiliares/raster/'.$carpetaid.'/'.$carpetaraiz.'/MTD_MSIL2A.xml';

//PARSEAR XML  

$contenido = file_get_contents($filename);
$contenido = str_replace('n1:Geometric_Info','Geometric_Info',$contenido); //NO PUDE LOGRAR parsear elementos con prefijo (n1) dentro de elementos con prefijo (n1)
$contenido = str_replace('n1:General_Info','General_Info',$contenido); //NO PUDE LOGRAR parsear elementos con prefijo (n1) dentro de elementos con prefijo (n1)
$data = new SimpleXMLElement($contenido);

$comando='rm -r '.$destino;
exec($comando,$exec_res);
$Log['tx'][]='eliminando carpeta preexistente: '.print_r($exec_res,true);

$global_footprint = $data->Geometric_Info->Product_Footprint->Product_Footprint->Global_Footprint->EXT_POS_LIST;

$v = explode(' ',$global_footprint);

$par='1';

$wkt='POLYGON((';
foreach($v as $k => $val){
	
	$par*=-1;	

	if($par=='-1'){
		if(!isset($v[$k+1])){break;}
		$nueva_coor=$v[$k+1].' '.$v[$k].', ';
		$wkt.=$nueva_coor;
	}
	
}
$wkt=substr($wkt,0,-2);
$wkt.='))';

//$wkt='POLYGON(('.$v[1].' '.$v[0].','.$v[3].' '.$v[2].','.$v[5].' '.$v[4].','.$v[7].' '.$v[6].','.$v[1].' '.$v[0].'))';
$srid='4326';

$t=$data->General_Info->Product_Info->PRODUCT_START_TIME;

//print_r($data);


$e=explode('T',$t);

$h=explode('.',$e[1]);

$f=explode('-',$e[0]);

$query="
	UPDATE 
		geogec.ref_raster_coberturas
	SET 
		nombre = 'Sentinel ".$f[0]." - region a definir -',
		geom =  ST_GeomFromText('".$wkt."', $srid),
		tipo='sentinel-2A',
		fecha_ano= '".$f[0]."',
		fecha_mes= '".$f[1]."',
		fecha_dia= '".$f[2]."',
		hora_utc = '".$h[0]."',
		id_p_ref_01_documentos = '".$_POST['iddoc']."'
	WHERE id='".$_POST['idraster']."'
";
$Resultado = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}
$Log['tx'][]="guardados los limites de la cobertura";



//BUSCAR ARCHIVOS DE IMAGEN

$dir_granule='./documentos/auxiliares/raster/'.$carpetaid.'/'.$carpetaraiz.'/GRANULE/';
$grs=scandir($dir_granule);
$cant=0;
foreach($grs as $c){
	if($c=='.'){continue;}
	if($c=='..'){continue;}
	$Log['tx'][]='carpeta en granule: '.$c;
	$cant++;
	$carpeta_engranule = $c;
}
if($cant>1){
	$Log['tx'][]='mas de una carpeta en granule';
	$Log['mg'][]=utf8_encode('Atención. Al parecer el zip que está procesando se compone de más de una imagen. Verifiquelo en la carpeta granule, en este momento solo procesamos una imagen por banda por zip');
}
if($cant<1){
	$Log['tx'][]='ninguna carpeta en granule';
	$Log['mg'][]=utf8_encode('Atención. Al parecer el zip que está procesando no contiene imágenes en la carpeta granule. Verifiquelo, en este momento solo procesamos imágenes en esa carpeta');
	$Log['res']='err';
	terminar($Log);	
}

$dir_img=$dir_granule.$carpeta_engranule.'/IMG_DATA/';


$d10=scandir($dir_img.'/10m');

$bandas=Array();
foreach($d10 as $f){  // EJ: "/R10m/T19FEF_20211215T141051_B02_10m.jp2";
	if($c=='.'){continue;}
	if($c=='..'){continue;}
	$e=explode('_',$f);
	if($e[2]==null){continue;}
	if($e[2]==''){continue;}
	$bandas[$e[2]]=$f;
}
//CARGAR BANDA 1 en BASE DE DATOS (nueva tabla)

$file_img=$bandas['B02']; //OJO PUEDE NECESITAR RUTA ABSOLUTA
if(!file_exists($file_img)){
	$Log['tx'][]='No se encontro el archivo de imagen: '.$file_img;
	$Log['mg'][]=utf8_encode('Atención. No se encontró el archivo de imagen en la carpeta esperada:'.$file_img. ' \n Consulte a el/la/le responsable de programación');
	$Log['res']='err';
	terminar($Log);	
}

$nombretabla=str_pad($_POST['idraster'],8,'0',STR_PAD_LEFT);



$exec="raster2pgsql -c -C -f rast -F -I -M -t 100x100 ".$file_img." geogec_raster.".$nombretabla." | PGPASSWORD=".$_SESSION["AppSettings"]->DATABASE_PASSWORD." psql -U ".$_SESSION["AppSettings"]->DATABASE_USERNAME." -d www-data -h localhost -p 5432";
console.log($exec);

$Log['res']="exito";
terminar($Log);

//CARGAR BANDA 2 en BASE DE DATOS (tabla existente)

$exec="raster2pgsql -d -C -f rast -F -I -M -t 100x100 /[...]/S2A_[...]_B02.TIF public.december_b02 | psql -Uthemagiscian -h<ip-address> -dchange_detection";



$Log['res']="exito";
terminar($Log);

?>
