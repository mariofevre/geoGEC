<?php
/**
* aplicación de visualización y gestion publicaciónes oficiales compartidas
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
    <link href="./css/geogec_app_publ.css" rel="stylesheet" type="text/css">
    
    <style>
    </style>
</head>

<body onkeydown="tecleoGeneral(event)">   
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>



<div id="pageborde">	
    <div id="page">
        <div id='cuadrovalores'>
			<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2>geoGEC</h2>
                <p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
            </a>

            <div id='elemento' tipo='Accion'>
                <img src='./img/app_publ_hd.png' style='float:left;'>
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
        <div id="portamapa">
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
        	
            <div id='PublActiva' idindicador='0' class="elementoOculto"></div>
            
            <div class='capaEncabezadoCuadro tituloCuerpo'>
                <h1>Publicaciones</h1>
            </div>
            
            <a onclick="accionCargarNuevaPubl(this)" id='botonAnadirPubl'>Compartir una nueva publicación</a>
            
            <div class="formSeleccionPublicacion" id="divSeleccionPubl">
                <div class='formSeleccionPublicacionCuerpo' id='divSeleccionPublCuerpo'>
                    <div id='barrabusqueda'>
                    	<input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);">
					</div>                    	
                    <div id='listapublicaciones'></div>
                </div>
            </div>
                        
            <div id='formEditarPublicaciones' idPubl='0' class="elementoOculto" modo='off'>
            	
            	
            	
	            <div class="menuAcciones elementoCarga" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	            	<a onclick='accionVolverAlListado(this)' id="botonCancelar">Volver al listado de publicaciones</a>
	            	<a onclick='accionIniciarEdicion(this)' id="botonEditar">Modificar</a>
	                <a onclick='accionEliminar(this)' id="botonEliminar">Eliminar</a>
	                <a onclick='accionCreaGuardar(this)' id="botonGuardar">Guardar</a>
	            </div>
            	
            	<div id="gestorarchivos">
            		<input id='publ_DOC' type='hidden' value='' name='id_p_ref_01'>
            		<div id="documento" onclick='descargarArchivo(this)' ruta=''>
            			<img src='./img/app_publ.png'>
            			<span id='txdoc'></span>
		            </div>
		            
		            <div id='edicion'>
	            		<a  id='botonDeactivaGestorarchivos' style="display:none;" onclick="deactivarGestorarchivos();">cancelar</a>
	            		<a onclick='activarUploader()' id='botonActivaUploader'>Subir Archivo</a>
	            		
		            	<form action='' enctype='multipart/form-data' method='post' id='uploader' ondragover='resDrFile(event)' ondragleave='desDrFile(event)'>
			                <div id='contenedorlienzo'>									
			                    <div id='upload'>
			                        <label>Arrastre archivo aquí.</label>
			                        <input id='uploadinput' type='file' name='upload' value='' onchange='cargarCmp(this);'>
			                    </div>
			                </div>
			            </form>
			            
			            <a onclick='activarSelectorDocs()' id='botonActivaSelectorDocs'>Elegir archivo cargado</a>
			            <div id="listadoDocumentos">
			            </div>
		            </div>
		            
		            		
            	</div>
            	
                <div id='titulo' class='elementoCarga'>
                    <h1>Título de la Publicación</h1>
                    <input type="text" id="pubTitulo" name='titulo'></input>
                </div>
                <div id='Autoria' class='elementoCarga'>
                    <h1>Autoría:</h1>
                    <input type="text" id="publAutoria" name='autoria'></input>
                </div>
                <div id='Tipo' class='elementoCarga'>
                    <h1>Tipo de documento:</h1>
                    <select id="publTipo"  name='id_p_ref_publ_tipos'></select>
                </div>

                <div id='observaciones' class='elementoCarga'>
                    <h1>Observaciones:</h1>
                    <textarea id='publObservaciones' name='observaciones'></textarea>
                </div>
                              
                <div id='Url' class='elementoCarga'>
                    <h1>Web oficial (o donde la encontraste):</h1>
                    <input type="text" id="publUrl" name='url'  onclick='consultarUrl(this)' ></input>
                </div>
                <div id='fecha' class='elementoCarga'>
                    <h1>Fecha de publicacion:</h1>
                    Año: <input id="publAnio" type='number' name='ano'></input>
                    Mes: <input id="publMes" type='number' name='mes'></input>
                </div>
                
                <div id='departamentos'  class='elementoCarga'>
                    <h1>Departamientos/partidos:</h1>
                    <div id='listadodepartamentosSelectos'></div>
                    
                    <div id='selectorDepartamentos'>
	                    <label>buscar: </label><input type="text" id="publBuscarDepto" onkeyup="accionBuscardpto(event, this)"></input>                    
	                    <div id='listadodepartamentos'></div>
	                </div>
	                
                </div>
                
                <div class='elementoCarga'>
                    <h1>Area</h1>
                    <a id='dibujarArea' onclick='dibujarAreaEnMapa()'>Dibujar Area de Estudio</a>
                     <input type="hidden" id="publArea" name='areatx'></input>
                </div>
                
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript">
	var _Acc = "publ";
	var _Data_Publ = {};
</script>
<script type="text/javascript" src="./sistema/sis_acciones.js"></script> <!-- funciones de consulta general del sistema: acciones -->


<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciones de carga de mapa-->
<script type="text/javascript" src="./app_publ/app_publ_consultas.js"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_publ/app_publ_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_publ/app_publ_mapa_funciones.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
