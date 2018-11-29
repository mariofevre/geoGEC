<?php
session_destroy();
session_start();

chdir('..');
ini_set('display_errors', true);


// funciones frecuentes
// funciones frecuentes
// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");



$Log=array();
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){
		print_r($Log);
	}else{
		echo $res;
	}
	exit;
}

if(!isset($_POST['log'])){
	$Log['tx'][]='error, no se registra log.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['password'])){
	$Log['tx'][]='error, no se registra constrasena.';
	$Log['res']='err';
	terminar($Log);	
}


$query="
SELECT 
	id, log, pass, email, nombre, apellido
	FROM geogec.sis_usu_registro
	WHERE  log='".$_POST['log']."'";
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	

if(pg_num_rows($ConsultaUsu)!=1){
	$Log['tx'][]=utf8_encode('error, no se registra usuario asociado al log ingresado.');
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}

$fila=pg_fetch_assoc($ConsultaUsu);

if($fila['pass']!=md5($_POST['password'])){
	$Log['tx'][]=utf8_encode('la contraseña no coincide con nuestro registro.');
	$Log['res']='err';
	terminar($Log);	
}
	
$_SESSION["geogec"]["usuario"]=$fila;
unset($_SESSION["geogec"]["usuario"]['password']);

include_once("./usuarios/usu_validacion.php");
$Usu= validarUsuario();

foreach($fila as $k => $v){
	$filaD[$k]=utf8_encode($v);
}
$filaD['permisos']=$Usu;
$Log['tx'][]='se accedio correctamente.';
$Log['data']=$filaD;
$Log['res']='exito';
terminar($Log);	