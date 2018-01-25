<?php
/**
*
 * registra un archivo dentro de una carpeta específica.
 * 
* @package    	TReCC(tm) paneldecontrol.
* @subpackage 	
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar> <trecc@trecc.com.ar>
* @author    	www.trecc.com.ar  
* @copyright	2013-2015 TReCC SA
* @license    	https://www.gnu.org/licenses/agpl-3.0-standalone.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 (agpl-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFero General Public License version 3" 
* publicada por la Free Software Foundation
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
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
	$Log['mg'][]=utf8_encode('no cuenta con permisos para generar una nueva versión de una capa estructural de la plataforma geoGEC');
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id'])){
	$Log['tx'][]='no fue definido el id del documento';
	$Log['res']='err';
	terminar($Log);
}
if(!isset($_POST['id_anidadoen'])){
	$Log['tx'][]='no fue definido el destino de anidamiento';
	$Log['res']='err';
	terminar($Log);
}

$query="
	UPDATE
		geogec.ref_01_documentos 
	SET 
		id_p_ref_02_pseudocarpetas='".$_POST['id_anidadoen']."'
	WHERE
		id='".$_POST['id']."'
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

//echo $query;
$Log['data']['idfi']=$_POST['id'];

$Log['tx'][]='completado';
$Log['res']='exito';

terminar($Log);

?>