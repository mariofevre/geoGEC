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


if(strlen($_POST['log'])<5){
	$Log['tx'][]=utf8_encode('error, el log debe tener mas de 4 caracteres.'.$_POST['log']);
	$Log['mg'][]=utf8_encode('error, el log debe tener mas de 4 caracteres.'.$_POST['log']);
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['password'])){
	$Log['tx'][]='error, no se registra constrasena.';
	$Log['res']='err';
	terminar($Log);	
}

if(strlen($_POST['password'])<4){
	$Log['tx'][]='la contrasena no alcanza el mínimmo de 4 caracteres.';
	$Log['res']='err';
	terminar($Log);	
}

if($_POST['password2']!==$_POST['password']){
	$Log['tx'][]='error, no coinciden las dos conrtasenas suministradas.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['nombre'])){
	$Log['tx'][]='error, no se registra nombre.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['apellido'])){
	$Log['tx'][]='error, no se registra apellido.';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['mail'])){
	$Log['tx'][]='error, no se registra direccion de correo electronico.';
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT 
		sis_usu_registro.*
	FROM
		geogec.sis_usu_registro
	WHERE  log='".$_POST['log']."'";
/*$link=mysql_connect($server,$dbuser,$dbpass);
$result=mysql_db_query($database,$query,$link);*/

$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	

if(pg_num_rows($ConsultaUsu)>0){
	$Log['tx'][]='error, ya se encuentra registrado el log solicitado.';
	$Log['res']='err';
	terminar($Log);	
}



$query="
	INSERT 
		INTO geogec.sis_usu_registro (
            log, nombre, apellido, pass, email
    	)
    	
    	VALUES (
    	'".$_POST['log']."', 
    	'".$_POST['nombre']."', 
    	'".$_POST['apellido']."', 
    	'".md5($_POST['password'])."', 
    	'".$_POST['mail']."' 
    	)
";

		
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.utf8_encode($query);
	$Log['res']='err';
	terminar($Log);	
}	

$fila=pg_fetch_assoc($ConsultaUsu);

		
$Log['tx'][]='se creo correctamente.';
$Log['res']='exito';
terminar($Log);	