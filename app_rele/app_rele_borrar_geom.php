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




$Acc=0;
$minAcc=2;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_capa'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}

if($Acc<$minAcc){
    $Log['mg'][]=utf8_encode('No cuenta con permisos (nivel '.$minAcc.' vs nivel '.$Acc.') para consultar un indicador. En el marco de investigaci�n c�digo '.$_POST['codMarco']);
    $Log['res']='err';
    terminar($Log);	
}


if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}
if(!isset($_POST['idcampa']) || $_POST['idcampa']<1){
	$Log['res']='err';
	$Log['tx'][]='falta id de campania';	
	terminar($Log);
}
if($_POST['modo']==''){
	$Log['res']='err';
	$Log['tx'][]='falta el modo de elmintacion';	
	terminar($Log);
}
if($_POST['modo']!='todos'&&$_POST['modo']!='propios'&&$_POST['modo']!='registro'){
	$Log['res']='err';
	$Log['tx'][]='el modo de eliminacion deber ser uno de estos: todos / propios';	
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
	
	$Log['res']='err';
	$Log['tx'][]='no se encontro la capa';	
	terminar($Log);
	
}

if($_POST['modo']=='todos'){
	$query="UPDATE
	           geogec.ref_capasgeo_registros
	       	SET
	           	zz_borrada='1',
	           	zz_auto_borra_usu='".$idUsuario."',
	           	zz_auto_borra_fechau='".time()."'
	        WHERE    
	           id_ref_capasgeo='".$IdCapa."'
	           AND
	           zz_borrada='0'
	";
}elseif($_POST['modo']=='propios'){
	$query="UPDATE
	           geogec.ref_capasgeo_registros
	       	SET
	           	zz_borrada='1',
	           	zz_auto_borra_usu='".$idUsuario."',
	           	zz_auto_borra_fechau='".time()."'
	           	
	        WHERE    
	           id_ref_capasgeo='".$IdCapa."'
	           AND
	           zz_auto_crea_usu='".$idUsuario."'
	           AND
	           zz_borrada='0'
	";
}elseif($_POST['modo']=='registro'){
	
	if(!isset($_POST['idgeom'])){
		$Log['res']='err';
		$Log['tx'][]='falta el idgeom';	
		terminar($Log);
		
	}
	
	$query="UPDATE
	           geogec.ref_capasgeo_registros
	       	SET
	           	zz_borrada='1',
	           	zz_auto_borra_usu='".$idUsuario."',
	           	zz_auto_borra_fechau='".time()."'
	           	
	        WHERE    
	           id_ref_capasgeo='".$IdCapa."'
	           AND
	           zz_borrada='0'
	           AND
	           id='".$_POST['idgeom']."'
	";	
}
//$Log['tx'][]=$query;
$Consulta = pg_query($ConecSIG,utf8_encode($query));
//$Log['tx'][]=$query;
    $Log['tx'][]=$query;
if(pg_errormessage($ConecSIG)!=''){
    $Log['res']='error';
    $Log['tx'][]='error al insertar registro en la base de datos';
    $Log['tx'][]=pg_errormessage($ConecSIG);
    $Log['tx'][]=$query;
    terminar($Log);
}	


$Log['res']='exito';	
terminar($Log);
