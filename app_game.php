<?php
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_ind. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2017 TReCC SA
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
* 
*
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); 
//error_reporting(-1);
ini_set('display_errors', 1);
// verificación de seguridad 
//include('./includes/conexion.php');
if(!isset($_SESSION)) {
	session_start(); 

	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}

$GeoGecPath = $_SERVER["DOCUMENT_ROOT"]."/geoGEC";

// funciones frecuentes
include($GeoGecPath."/includes/fechas.php");
include($GeoGecPath."/includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<head>
    <title>GEC - Plataforma Geomática</title>
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./css/geogecgeneral.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_game.css" rel="stylesheet" type="text/css">
    
    <style>
	    #botonCrearGeom{
	    	display:none;
	    }
	    #formcalculo{
	    	display:none;
	    }
	     #botonDuplicarGeom{
	    	display:none;
	    }
	    #periodo > #selectorPeriodo div #valor{
	    	height:15px;
	    }
	     #periodo > #selectorPeriodo{
	    	vertical-align:top;
	    }
	    #periodo > #selectorPeriodo div{
	    	vertical-align: top;
	    	text-align:center;
	    }
	    #tipodeometriaNuevaGeometria{
	    	display:none;
	    }
	    
	    .unidad input.renombra {
			border:none;
			width:200px;
			font-size:11px;
			font-weight:bold;
			color:#000;
			cursor:pointer;
		}
		
		.unidad input.renombra[editando='si'] {
			color:red;
			background-color:pink;
		}
		
		
    </style>
</head>

<body>   
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>

<div id="pageborde">
    <div id="page">
        <div id='encabezado'>
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2>geoGEC</h2>
                <p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
            </a>

            <div id='elemento' tipo='Accion'>
                <img src='./img/app_game_hd.png' style='float:left;'>
                <h2 id='titulo'></h2>
                <div id='descripcion'>
                	           	
            	</div>
            </div>	
        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de proyecto -</h1>
            <p id='descripcion'>- descripcion de proyecto -</p>
        	<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
        </div>	
        <div class="portamapa">
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
            <div id='mapa' class="mapa"></div>
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
        </div>
        <div id="cuadrovalores">
            <div id='sesionActiva' idindicador='0' class="elementoOculto"></div>
            <div class='capaEncabezadoCuadro tituloCuerpo'>
                <h1>Sesiones</h1>
            </div>
            
            <a onclick="accionCrearSesion(this)" id='botonCrearSesion'>Crear nueva sesion</a>
            <a onclick='accionCargaCancelar(this)'  id="botonCancelarCarga">Volver al listado de sesiones</a>

            
            <div id="formSeleccionSesion" class="elementoOculto">   
                <div class='formSeleccionSesionCuerpo' id='divSeleccionSesionCuerpo'>
                    <h1>Sesiones publicadas</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunasesion'>- ninguna -</p>
                    <div id='listaSesionesPublicadas'></div>
                </div>
            </div>
            
            <div id='formEditarSesiones' idindicador='0' class="elementoOculto">
            
	            <div class="menuAcciones elementoOculto" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	                <a onclick='accionCreaEliminar(this)' id="botonEliminar">Eliminar</a>
	                <a onclick='accionCreaGuardar(this)' id="botonGuardar">Guardar</a>
	                <a onclick='accionCreaPublicar(this)' id="botonPublicar">Publicar</a>
	                <a onclick='accionJugar()' >¡Jugar!</a>
            		<a href='./app_game_highscores.php'>high scores</a>
	            </div>
            
                <div class='elementoCarga largo'>
                    <h1>Nombre de la sesión</h1>
                    <input type="text" id="sesionNombre" onkeypress="accionEditarIndCampo(event, this)">
                </div>
                <div class='elementoCarga largo'>
                    <h1>Presentacion</h1>
                    <textarea type="text" id="sesionPresentacion" onkeypress="accionEditarIndCampo(event, this)"></textarea>
                </div>
                
                <div class='elementoCarga'>
                    <h1>Indicador asociado</h1>
                    <select onchange="editarIndFuncionalidad(event, this)" id="sesionIndicadorAsociado">
                        <option value="elegir" selected>-elegir-</option>                        
                    </select>
                </div>
                <div class='elementoCarga'>
                    <h1>Costo unitario de ejecución</h1>
                    <input name="costounitario" id="sesionCostounitario" type="number">
                </div>
                <div class='elementoCarga'>
                    <h1>Límite unitario de ejecución por turno</h1>
                    <input name="limiteunitarioporturno" id="sesionLimiteunitarioporturno" type="number">
                </div>
                <div class='elementoCarga'>
                    <h1>Modo red</h1>
                    <input type='hidden' name="limiteunitarioporturno" id="sesionModored">
                    <input onclick='chekearinput(this)' t='1' f='0' type='checkbox' name="checklimiteunitarioporturno" for="sesionModored"  id="checksesionModored">
                </div>
                <div class='elementoCarga'>
                    <h1>Turnos</h1>
                    <input name="turnos" id="sesionTurnos" type="number">
                </div>        
            </div>
        
        </div>
    </div>
</div>


<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_game/app_game_queries.js"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_game/app_game_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_game/app_game_mapa.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>