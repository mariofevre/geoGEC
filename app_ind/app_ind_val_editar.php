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

function IsNullOrEmptyString($string){
    return (!isset($string) || trim($string)==='');
}

function verifyDate($date, $strict = true)
{
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if ($strict) {
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
    }
    return $dateTime !== false;
}

function validarFechaQuery($fechaPorValidar){
    $fechaNueva = null;
    if (IsNullOrEmptyString($fechaPorValidar) || $fechaPorValidar == 'NULL'){
        $fechaNueva = 'NULL';
    } else {
        if (verifyDate($fechaPorValidar, true)){
            $fechaNueva = "'".$fechaPorValidar."'";
        } else {
            $Log['tx'][]='error, la fecha es incorrecta: -|'.$fechaPorValidar.'|-';
            $Log['res']='err';
            terminar($Log);
        }
    }
    
    return $fechaNueva;
}

function valorNulableQuery($valorAValidar){
    $valorParaQuery = null;
    
    if (IsNullOrEmptyString($valorAValidar) || $valorAValidar == 'NULL'){
        $valorParaQuery = 'NULL';
    } else {
        $valorParaQuery = "'".$valorAValidar."'";
    }
    
    return $valorParaQuery;
}

if(!isset($_POST['codMarco'])){
	$Log['tx'][]='no fue enviada la variable codMarco';
	$Log['res']='err';
	terminar($Log);	
}

$Acc=0;
if(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['app_ind'];
}elseif(isset($Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general'];
}elseif(isset($Usu['acc']['est_02_marcoacademico']['general']['general'])){
	$Acc=$Usu['acc']['est_02_marcoacademico']['general']['general'];
}elseif(isset($Usu['acc']['general']['general']['general'])){
	$Acc=$Usu['acc']['general']['general']['general'];
}
$minacc=2;
if($Acc<$minacc){
	$Log['mg'][]=utf8_encode('no cuenta con permisos para modificar la planificaci�n de este marco acad�mico. \n minimo requerido: '.$minacc.' \ nivel disponible: '.$Acc);
	$Log['tx'][]=print_r($Usu,true);
	$Log['res']='err';
	terminar($Log);
}

$idUsuario = $_SESSION["geogec"]["usuario"]['id'];

$query="SELECT  *
        FROM    geogec.ref_indicadores_indicadores
        WHERE
                id = '".$_POST['idIndicador']."'
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
    $Log['tx'][]= "No se encontro el indicador id ".$_POST['idIndicador'];
    $Log['data']=null;
} else {
    $Log['tx'][]= "Consulta de indicador valido";
    $fila = pg_fetch_assoc($Consulta);
    $Indicador=$fila;
}





$query = "UPDATE
                geogec.ref_indicadores_valores
        SET    
                zz_superado='1'
        WHERE
                id_p_ref_capas_registros = '".$_POST['id_p_ref_capas_registros']."'
        AND
                zz_borrado='0'
        AND
                zz_superado='0'
        AND
                ano = '".$_POST['ano']."'
        ";

if ($Indicador['periodicidad'] == 'mensual'){
    $query = $query."
        AND
                mes = '".$_POST['mes']."'
        ";
}

$query .= " RETURNING ID";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
} else {
    while ($fila=pg_fetch_assoc($Consulta)){	
        $Log['tx'][]='Valor de indicador id '.$fila['id'].' fue marcado como superado.';
    }
}
//$Log['tx'][]='Query: '.$query;

$Log['data']['indid']=$_POST['idIndicador'];
$Log['data']['id_p_ref_capas_registros']=$_POST['id_p_ref_capas_registros'];
$Log['data']['ano']=$_POST['ano'];
$Log['data']['mes']=$_POST['mes'];

$camposviables=array(
	'col_texto1_dato',
	'col_texto2_dato',
	'col_texto3_dato',
	'col_texto4_dato',
	'col_texto5_dato',
	'col_numero1_dato',
	'col_numero2_dato',
	'col_numero3_dato',
	'col_numero4_dato',
	'col_numero5_dato'
);

$campos='';
$valores='';
foreach($camposviables as $v){
	if(isset($_POST[$v])){
		$campos.=$v.', ';
		$valores.=valorNulableQuery($_POST[$v]).', ';
	}
}

$query = "INSERT INTO   geogec.ref_indicadores_valores
        (   id_p_ref_indicadores_indicadores, 
            ano,";

            if ($Indicador['periodicidad'] == 'mensual'){
                $query .= " mes, ";
            }

$query .="
            usu_autor, 
            fechadecreacion, 
            zz_superado,
            zz_borrado,
           	".$campos."
            id_p_ref_capas_registros
        )

        VALUES
        (   '".$_POST['idIndicador']."',
            '".$_POST['ano']."',";

            if ($Indicador['periodicidad'] == 'mensual'){
                $query .="'".$_POST['mes']."',";
            }

$query .="
            ".$idUsuario.",
            ".validarFechaQuery($_POST['fechadecreacion']).",
            0,
            0,
            ".$valores."
            ".valorNulableQuery($_POST['id_p_ref_capas_registros'])."
        )
        RETURNING id;";

if ($query != ''){
    $Consulta = pg_query($ConecSIG, $query);
    if(pg_errormessage($ConecSIG)!=''){
            $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
            $Log['tx'][]='query: '.$query;
            $Log['mg'][]='error interno';
            $Log['res']='err';
            terminar($Log);	
    }

    $fila=pg_fetch_assoc($Consulta);
    
    $Log['tx'][]="Editado indicador valor id: ".$fila['id'];
    $Log['data']['id']=$fila['id'];
    $Log['res']="exito";
} else {
    $Log['tx'][]="Error al editar indicador valor id: ".$_POST['id'];
    $Log['res']="error";
}

//$Log['tx'][]='Query: '.$query;

terminar($Log);
