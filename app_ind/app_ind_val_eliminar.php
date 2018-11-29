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

if(!isset($_POST['id']) || $_POST['id']<1){
    $Log['tx'][]='no fue enviada la variable id o es invalida';
    $Log['res']='err';
    terminar($Log);
}

$query="SELECT  *
        FROM    geogec.ref_indicadores_valores
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
    $Log['tx'][]='No se encuentra el Valor de indicador id '.$_POST['id'].'.';
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
} 

$fila=pg_fetch_assoc($Consulta);

if($fila['zz_borrado']=='1'){
    $Log['tx'][]='Valor de indicador id '.$_POST['id'].' ya estaba marcado como borrado.';
    $Log['mg'][]='Valor de indicador id '.$_POST['id'].' ya estaba marcado como borrado.';
    $Log['res']="err";
    terminar($Log);
}

$id_p_ref_indicadores_indicadores = $fila['id_p_ref_indicadores_indicadores'];
$id_p_ref_capas_registros = $fila['id_p_ref_capas_registros'];
$ano = $fila['ano'];
$mes = $fila['mes'];


$query = "UPDATE
                geogec.ref_indicadores_valores
         SET    
                zz_borrado='1'
         WHERE
                ref_indicadores_valores.id = '".$_POST['id']."'
        ;";

$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
    $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
    $Log['tx'][]='query: '.$query;
    $Log['mg'][]='error interno';
    $Log['res']='err';
    terminar($Log);	
}

$Log['tx'][]='Valor de indicador id '.$_POST['id'].' fue marcado como borrado.';


//Buscar un valor superado para reemplazar el recien borrado
$query="SELECT  id
        FROM    geogec.ref_indicadores_valores
        WHERE   id_p_ref_indicadores_indicadores = '".$id_p_ref_indicadores_indicadores."'
        AND     id_p_ref_capas_registros = '".$id_p_ref_capas_registros."'
        AND     ano = '".$ano."'";
        
        if ($mes != null && $mes > 0){
            $query.="AND     mes = '".$mes."'";
        }

$query.="
        AND     zz_borrado = '0'
        AND     zz_superado = '1'
        ORDER BY fechadecreacion DESC
        LIMIT 1
        ";


$Consulta = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
}

if (pg_num_rows($Consulta) > 0){
    $fila=pg_fetch_assoc($Consulta);
    $nuevoId = $fila['id'];
    
    $query = "UPDATE
                    geogec.ref_indicadores_valores
             SET    
                    zz_superado='0'
             WHERE
                    id = '".$nuevoId."'
            ;";

    $Consulta = pg_query($ConecSIG, $query);
    if(pg_errormessage($ConecSIG)!=''){
        $Log['tx'][]='error: '.pg_errormessage($ConecSIG);
        $Log['tx'][]='query: '.$query;
        $Log['mg'][]='error interno';
        $Log['res']='err';
        terminar($Log);	
    }

    $Log['tx'][]='Valor de indicador id '.$nuevoId.' fue marcado como no superado.';
    $Log['data']['id']=$nuevoId;
}


$Log['res']="exito";

terminar($Log);
