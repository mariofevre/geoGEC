<?php

if(!isset($_SESSION)) { session_start(); }

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


$query="
SELECT 
	id, log, email, nombre, apellido
	FROM geogec.sis_usu_registro
	order by apellido asc, nombre asc
";
	
$ConsultaUsu = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}	


while($fila=pg_fetch_assoc($ConsultaUsu)){
	$Log['data'][$fila['id']]=$fila;
}
		
$Log['res']='exito';
terminar($Log);	