<?php
/**
* aplicaci�n de visualizaci�n y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuraci�n de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_ind. Aplicacion para la gesti�n de documento
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
* 
*
*/

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); 
//error_reporting(-1);
ini_set('display_errors', 1);
// verificaci�n de seguridad 
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

// funci�n de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");
$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<head>
    <title>GEC - Plataforma Geom�tica</title>
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
		
		#presentacion{
			position:fixed;
			top:20vh;
			left:20vw;
			width:0vw;
			height:0vh;
			max-height:40vh;
			opacity:0;
			transition: width 2s, height 2s, opacity 2s;
			z-index:1000;
			border:1px solid #08afd9;
			background-color: #fff;
			box-shadow:10px 10px 20px #000;
			overflow:hidden;
		}
		
		#presentacion p{
			
			width: 60vw;
			font-size: 14px;
			margin-bottom: 4px;
			margin-top: 4px;
			line-height: 1.5;
			width: calc(60vw - 20px);
			text-align:justify;
			
		}
		
		#presentacion p span.variable{
			display:inline-block;
			width:260px;
			text-align:right;
			margin:2px;
		}
		
		#presentacion p span.valor{

			display:inline-block;
			font-weight:bold;

		}	
		
		#presentacion #botonpresentok{
			font-family:'game';
			display:block;
			width:80px;
			text-align:center;
			line-height:20px;
			height:20px;
			border:1px solid #08afd9;
			position:absolute;
			right:5px;
			bottom:5px;
			font-size:15px;
			border-radius:5px;
		}
		
		.senales{
			border:1px solid #000;
			box-shadow:1px 1px 1px #08afd9;
			background-color:#fff;
			display:block;
			position:absolute;
			left:5px;
			top:5px;
			width:100px;
			font-size:11px;
			z-index:200;
			height: 40px;
		}	
		#ayuda.senales{
			top:5px;
		}
		#ayuda.senales > div{
			display: inline-block;
			width: 47px;
			top: 5px;
			text-align: right;
		}
		
		
		#limite.senales{
			top:60px;
		}
		
		
		#limite.senales #barra{
			width:calc(100% - 10px);
			position:relative;
			display:block;
			margin:5px;
			
		}
		#limite.senales #barra #avan{
			width:1%;
			background-color:#08afd9;
			height:5px;
		}
		.senales[estado='alerta']{
			background-color:rgba(255,100,100,0.8);
		}
		#limite.senales[estado='alerta'] #barra #avan{
			background-color:red;
		}
		
		
		#pasar.senales{
			top:115px;
		}
		#pasar.senales[estado='inactivo']{
			opacity:0.5;
			cursor:auto;
		}
		
		#pasar.senales[estado='inactivo'] a:{
			cursor:auto;
		}
		
		#pasar.senales[estado='inactivo'] a:hover{
			background-color:#fff;
			color:#08afd9;
			cursor:auto;
		}
		#pasar.senales #flecha{
			width:calc(100% - 10px);
			font-size:24px;
			margin:5px;
			display:inline-block;
			line-height: 16px;
			 text-align:center;
		}
		
		#pasar.senales #flecha span{
			font-size:11px;
			display:block;
			text-align:center;
		}	
		
		
		
		#gameover{
			position:fixed;
			top:20vh;
			left:20vw;
			width:0vw;
			height:0vh;
			max-height:40vh;
			opacity:0;
			transition: width 2s, height 2s, opacity 2s;
			z-index:1000;
			border:1px solid #08afd9;
			background-color: #fff;
			box-shadow:10px 10px 20px #000;
			overflow:hidden;
		}
		#gameover h1{
			font-family:'game';
		}
		#gameover p{
			font-size:14px;
			margin-top:8px;
			margin-top:8px;
		}
			
		#gameover a{
			display:block;
			text-align:center;
			line-height:20px;
			height:20px;
			border:1px solid #08afd9;
			position:absolute;
			right:5px;
			bottom:5px;
			font-size:15px;
			border-radius:5px;
			font-family:'game';
			width:auto;
		}	
		
		#gameover input{
			text-align:center;
			height:30px;
			border:1px solid #08afd9;
			font-size:20px;
			border-radius:5px;
			font-family:'game';
			width:80px;
		}		
    </style>
</head>

<body>   
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol5.3/ol.js"></script>
<!---<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>--->

<div id="pageborde">
    <div id="page">
        <div id='encabezado'>
		<a target='_blank' href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2>geoGEC</h2>
                <p>Plataforma Geom�tica del centro de Gesti�n de Espacios Costeros</p>
            </a>

        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de la sesion -</h1>
            <p id='descripcion'>- presentacion de la sesion -</p>
            <div id='ayuda'>ayuda extendida</div>
        </div>	
        <div class="portamapa">
        	
        	<div id='ayuda' class='senales'>
				<div id='func'>click:</div> inicia traza<br>
				<div id='func'>dobleclick:</div> termina<br>
				<div id='func'>rueda:</div> zoom
        	</div>
        	
        	<div id='limite' class='senales'><div id='barra'><div id='avan'></div></div><span id='porc'>0% 0m</span><div id='tx'>propuesta v�lida...</div></div>
        	<div id='pasar' class='senales' estado='inactivo'><a id='flecha' onclick='pasarTurno()'>pasar -><span>al siguiente turno</span></a></div>
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
            <div id='turnos'>
                <h1>Turnos</h1>
                <p id='actual'><span id='titulo'>turno actual:</span><span id='nturn'></span>. Ten�s otros <span id='nturndif'> turnos.</p>
                <div id='historial'></div>
            </div>
            
            <div id='graficos'>
            	<h1>Puntaje</h1>
            	<p><span id='puntajeactual'></span> (<span id='puntajePactual'></span>%)</p>
            	<br>
            	
            	
            </div>
           
            
        
        </div>
    </div>
</div>
<div id='presentacion'></div>
<div id='gameover'></div>
<div id='gifanimado'>
	<img src='./img/cargando.gif'>               
</div>
 <script type="text/javascript">
 
</script>

 <script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
 <script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciona de gesti�n de mapa-->
 <script type="text/javascript" src="./app_game/app_game_play_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
 <script type="text/javascript" src="./app_game/app_game_play_queries.js"></script> <!-- carga funciones de consulta de base de datos -->
 
 <script type="text/javascript" src="./app_game/app_game_mapa.js"></script> <!-- carga funciones de interaccion con el mapa -->
 
 <script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->

 <script type="text/javascript" src="./js/jsts/jsts.js"></script> <!-- geoprocesos del lado del cliente -->
 
 
</body>