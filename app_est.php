<?php
/**
* aplicaci�n de visualizaci�n y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuraci�n de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_capa. Aplicacion para la gesti�n de documento
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
<!DOCTYPE html>
<head>
    <title>GEC - Plataforma Geom�tica</title>
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css" rel="stylesheet" type="text/css">
    <link href="./css/BaseSonido.css" rel="stylesheet" type="text/css">
    <link href="./css/ad_navega.css" rel="stylesheet" type="text/css">	
    <link href="./css/tablarelev.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    <link href="./css/BA_salidarelevamiento.css" rel="stylesheet" type="text/css">
    <link href="./css/geogecgeneral.css" rel="stylesheet" type="text/css">
    <link href="./css/geogecindex.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_docs.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_capa.css" rel="stylesheet" type="text/css">
    <style>
    	#mapa{width:600px;}
    	#page > div#cuadrovalores{
			display: inline-block;
			width: 400px;
		}
    </style>
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>

<div id="pageborde">
    <div id="page">
        <div id='cuadrovalores'>
        	
		<a href='./index.php?est=est_03_candidatos&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
            <h2>geoGEC</h2>
            <p>Plataforma Geom�tica del centro de Gesti�n de Espacios Costeros</p>
        </a>

            <div id='elemento'>
                <img src='' style='float:left;'>
                <h2 id='titulo'>titulo</h2>
                <div id='descripcion'>descrioci�n.</div>
            </div>	
        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de proyecto -</h1>
            <p id='descripcion'>- descripcion de proyecto -</p>
        </div>
        <div id="portamapa">
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
            <div id='mapa' class="mapa"></div>
            <div id='auxiliar' mostrando=''><div id='cont'></div></div>
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
        </div>
        <div id="cuadrovalores">
        	<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
            <div id='menuelementos'>
				<div id='lista'></div>	
			</div>
            
            
             <div id='formEditarCandidato' idcandidato='0' class="elementoOculto">
            
	            <div class="menuAcciones" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	                <a onclick='accionCreaEliminar(this)' id="botonEliminar">Eliminar</a>
	                <a onclick='accionGuardarCandidato(this)' id="botonGuardar">Guardar</a>
	                <a onclick='accionPublicarCandidato(this)' id="botonPublicar">Publicar</a>
	            </div>
            
                <div class='elementoCarga'>
                    <h1>Nombre del Marco</h1>
                    <input type="text" id="marco_nombre" name='nombre'>
                </div>
                <div class='elementoCarga'>
                    <h1>Nombre oficial</h1>
                    <input type="text" id="marco_nombre_oficial" name='nombre_oficial'>
                </div>
                <div class='elementoCarga'>
                    <h1>Descripci�n</h1>
                    <input type="text" id="marco_descripcion" name='descripcion'>
                </div>
                <div class='elementoCarga'>
                    <h1>Tabla</h1>
                    <input type="text" id="marco_tabla" name='tabla' value='est_02_marcoacademico'>
                </div>
                <div class='elementoCarga'>
                    <h1>Geometr�a (WKT)</h1>
                    <input type="text" id="marco_geotx" name='geotx' campoSeleccion='geotx'>
                </div>
                
                
                
            </div>
             
            <h1>Geometr�a</h1>
            <a onclick="alert('funci�n en desarrollo');accionCargarNuevaCapa(this)" id='botonAnadirCapa'>Subir un shapefile a la plataforma</a><br>
            <a onclick="accionCargarmunicipios(this)" id='botonAnadirCapa'>Seleccionar de un municipio costero</a>
            
            <div id='muestra'></div>
            
            <div class="formSeleccionCapa" id="divSeleccionCapa">
                <div class='elementoCarga accionesCapa'>
                    <a id="botonCancelarCarga"  onclick='accionCancelarSeleccionCapa(this)'>Cancelar</a></br>
                </div>
            </div>
            
            <div class='formCargaCapa' id='divCargaCapa' idcapa=''>
                <div id='avanceproceso'></div>
                <div class='elementoCarga accionesCapa'>
                    <h1>Acciones</h1>
                    <a id='botonelim' onclick='eliminarCandidatoCapa(this.parentNode);' title="Eliminar Capa">Eliminar</a></br>
                </div>
				
                <div class='formCargaCapaCuerpo' id='carga'>
                    <div id='nombrecapa' class='elementoCarga'>
                        <h1>Nombre de la capa</h1>
                        <input type="text" id="capaNombre" onkeypress="editarCapaNombre(event, this)"></input>
                    </div>
                    <div id='desccapa' class='elementoCarga'>
                        <h1>Descripci�n</h1>
                        <textarea type="text" id="capaDescripcion" onkeypress="editarCapaDescripcion(event, this)"></textarea>
                    </div>
                    
                    <div  id='cargarGeometrias'  class='elementoCargaLargo'>                 
                    	<h1>Gargar geometr�as</h1>   	
	                    <div id='earchivoscargando' class='elementoCarga'>
	                        <h2>archivos cargando</h2>
	                        <div id='archivosacargar'>
	                            <form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
	                                <label style='position:relative;' class='upload'>							
	                                <span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aqu� un archivo</span>
	                                <input id='uploadinput' style='opacity:0;' type='file' multiple name='upload' value='' onchange='enviarArchivosSHP(event,this);'></label>
	                                <select id='crs' onchange='ValidarProcesarBoton()'>
	                                    <option value=''>- elegir -</option>
	                                    <option value='4326'>4326</option>
	                                    <option value='3857'>3857</option>
	                                    <option value='22171'>22171</option>
	                                    <option value='22172'>22172</option>
	                                    <option value='22173'>22173</option>
	                                    <option value='22174'>22174</option>
	                                    <option value='22175'>22175</option>
	                                    <option value='22176'>22176</option>
	                                    <option value='22177'>22177</option>
	                                </select>
	                                <div id='cargando'></div>
	                            </form>
	                        </div>
	                    </div>
	
	                    <div id='earchivoscargados' class='elementoCarga'>
	                        <h2>archivos cargados</h2>
	                        <p id='txningunarchivo'>- ninguno -</p>
	                        <div id='archivoscargados'></div>
	                    </div>
	
	                    <div  id='ecamposdelosarchivos'  class='elementoCargaLargo'>
	                        <h2>campos identificados</h2>
	                        <p id='verproccampo'></p>
	                        <div id='camposident'></div>
	                        <a id='procesarBoton' onclick='procesarCapa(this.parentNode)' estado='inviable'>Procesar Shapefile</a>
	                    </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
	
<script type="text/javascript">
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	var _Cod = '';
	var _Est = '';
	var _Tabla='est_03_candidatos';
	
</script>
<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./app_est/app_est_consultas.js"></script> <!-- carga funciona de gesti�n de mapa-->


<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciona de gesti�n de mapa-->
<script type="text/javascript" src="./app_est/app_est_mapa.js"></script> <!-- carga funciona de gesti�n de mapa-->
<script type="text/javascript" src="./app_est/app_est_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_est/app_est_Shapefile.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript">

	

	//consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_03_candidatos');
	function accionCargarmunicipios(_this){
		mostrarTablaEnMapa('est_01_municipios');
		consultarCentroidesSeleccion('est_01_municipios');
	}
	
</script>

</body>
