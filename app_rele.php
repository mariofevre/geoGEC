<?php
/**
* aplicación de visualización y gestion de relevamientos de campo.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_rele. Aplicacion para la gestión de relevamientos
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
    <title>GEC - Plataforma Geomática</title>
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
	<link href="./css/geogecgeneral.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_rele.css" rel="stylesheet" type="text/css">
    
    <link href="./js/ol6.3/ol.css" rel="stylesheet" type="text/css">
    <style>
    	
    </style>
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol6.3/ol.js"></script>

<div id="pageborde">
    <div id="page">
        <div id='encabezado'>
        	
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2>geoGEC</h2>
                <p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
            </a>

            <div id='elemento' tipo="Accion">
                <img src='./img/app_rele_hd.png' style='float:left;'>
                <h2 id='titulo'>cargando...</h2>
                <div id='descripcion'>cargndo...</div>
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
            <div id='auxiliar' mostrando=''><div id='cont'></div></div>
            <div id="wrapper">
                <div id="location"></div>
                <div id="scale"></div>
            </div>
        </div>
        <div id="cuadrovalores">
        	<div id="contenido">
            
	            <div class="formSeleccionCampa" id="divSeleccionCampa">
	                <div class='formSeleccionCampaCuerpo' id='divSeleccionCampaCuerpo'>
	                    <h1>Campañas publicadas <a onclick="accionCrearCampa(this)" id='botonAnadirCampa'>+ añadir</a></h1>
	                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
	                    <p id='txningunacampa'>- ninguna -</p>
	                    <div id='listacampaspublicadas'></div>
	                </div>
	            </div>
	            
	            <div class='formCargaCampa' id='divCargaCampa' idcampa=''>
	                <div id='avanceproceso'></div>
	                <div class='elementoCarga accionesCampa cajaacciones'>
	                    <h1>Acciones</h1>
	                    <a onclick='accionCancelarCargarNuevaCampa(this)'>Cancelar</a></br>
	                    <a id='botonelim' onclick='eliminarCandidatoCampa(this.parentNode);' title="Eliminar Campa">Eliminar</a></br>
	                    <a id='botonedita' onclick='editarCampa(this.parentNode);' title="guardar esta campaña preliminarmente">Editar</a></br>
	                    <a id='botonguarada' onclick='guardarCampa(this.parentNode);' title="guardar esta campaña preliminarmente">Guardar</a></br>
	                    <a id='botonguarada' onclick='document.querySelector("#divReleACapa").style.display="block";' title="generar una capa a partir de este relevamiento">Generar Capa a partir de este relevamiento</a></br>
	                    <a id='botoncampo' onclick='nuevoCampo()'>+ Añadir campo</a>
	                </div>
					
					 <div style='display:none;' class='formReleACapa' id='divReleACapa' idcapa='' >
                				
		                <div class='formReleACapaCuerpo' id='ReleACapa'>
		                	<div id='campos'>
		                		
		                		<p>campo1 nombre:<input name='nombrec_1'> fuente:<select name='fuentec_1'></select></p>
		                		<p>campo2 nombre:<input name='nombrec_2'> fuente:<select name='fuentec_2'></select></p>
		                		<p>campo3 nombre:<input name='nombrec_3'> fuente:<select name='fuentec_3'></select></p>
		                		<p>campo4 nombre:<input name='nombrec_4'> fuente:<select name='fuentec_4'></select></p>
		                		<p>campo5 nombre:<input name='nombrec_5'> fuente:<select name='fuentec_5'></select></p>                		
		                	</div>    
		                	<input type='submit' value='generar capa' onclick='event.preventDefault();ReleACapa()'>
		                </div>
		            </div>
		            
	                <div class='formCargaCampaCuerpo' id='carga'>
	                    <div id='nombrecampa' class='elementoCarga'>
	                        <h2>Nombre de la campaña</h2>
	                        <input type="text" id="campaNombre"></input>
	                    </div>
	                    <div id='desccampa' class='elementoCarga'>
	                        <h2>Descripción</h2>
	                        <textarea type="text" id="campaDescripcion"></textarea>
	                    </div>
	                    
	                    <div id='desccampa' class='elementoCarga'>
	                        <h2>Unidad de Análisis</h2>
	                        <label>Nombre</label><input name='unidadanalisis' value=''><br>
	                        <label>Tipo</label>
                        	<select name='tipogeometria'>
                        		<option value=''>- elegir -</option>
					  			<option value="Point">Puntos</option>
					  			<option value="LineString">Lineas</option>
					  			<option value="Polygon">Polígonos</option>
					  		</select>
	                    </div>
	                    
	                    <div id='geometrias' class='elementoCarga'>
	                      	<h1>Geometrías</h1>   	
	                      	<a id='botoncargar' onclick='activacargarGeometrias();' title="generar geometrias desde archivo (shp o dxf)">cargar geometrias</a>
	                      	<a id='botoncargar' onclick='borrarGeometrias("propios");' title="generar geometrias desde archivo (shp o dxf)">borrar mis geometrías</a>
	                      	<a id='botoncargar' onclick='borrarGeometrias("todos");' title="generar geometrias desde archivo (shp o dxf)">borrar toda geometrías</a></br>
	                      	
	                      	
		                    <div id='cargarGeometrias'  class='elementoCargaLargo'>                 
		                    	<h2>Gargar geometrías</h2>   	
			                    <div id='earchivoscargando' class='elementoCarga'>
			                        
			                        <div id='archivosacargar'>
			                            <form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
			                                <label style='position:relative;' class='upload'>							
			                                <span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo (shp o dxf)</span>
			                                <input id='uploadinput' style='opacity:0;' type='file' multiple name='upload' value='' onchange='enviarArchivosSHPDXF(event,this);'></label>
			                                <div id='earchivoscargados' class='elementoCarga'>
						                        <h3>archivos cargados</h3>
						                        <p id='txningunarchivo'>- ninguno -</p>
						                        <div id='cargando'></div>
						                    </div>
			                                <select id='crs' onchange='ValidarProcesarBotonSHP()'>
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
			                                
			                                <a id='procesarBotonSHP' onclick='alert("en desarrollo");return;procesarCampaSHP(this.parentNode)' estado='inviable'>Procesar Shapefile</a>
					                        <a id='procesarBotonDXF' onclick='procesarCampaDXF(0);' estado='inviable'>Procesar DXF</a>
			                            </form>
			                            
			                        </div>
			                    </div>
		                    </div>
		                </div>   
		                
	                    <div id='simbologia' class='elementoCargaLargo'>
	                        <h1>Simbología<a onclick='guardarSLD(this.parentNode);'> Guardar Simbología</a></h1>
	                        <h2>Símbolo por defecto</h2>
	                        <label class='l1'>color de relleno</label>
	                        <input onchange='cargarFeatures()' type="color" id="inputcolorrelleno"/>
	                        <label class='l2'>transparencia de relleno</label>
	                        <input onchange='cargarFeatures()' id="inputtransparenciarellenoNumber" min="0" max="100" oninput="inputtransparenciarellenoRange.value=inputtransparenciarellenoNumber.value" type="number" style="width: 10%">
	                        <input onchange='cargarFeatures()' id="inputtransparenciarellenoRange" min="0" max="100" oninput="inputtransparenciarellenoNumber.value=inputtransparenciarellenoRange.value" type="range" style="width: 25%">
	                        <label class='l1'>color de trazo</label>
	                        <input onchange='cargarFeatures()' type="color" id="inputcolortrazo"/>
	                        <label class='l2'>ancho de trazo (pixeles)</label>
	                        <input onchange='cargarFeatures()' id="inputanchotrazoNumber" min="0" max="10" oninput="inputanchotrazoRange.value=inputanchotrazoNumber.value" type="number" style="width: 10%">
	                        <input onchange='cargarFeatures()' id="inputanchotrazoRange" min="0" max="10" oninput="inputanchotrazoNumber.value=inputanchotrazoRange.value" type="range" style="width: 25%">
	                        <h2>Reglas adicionales<a onclick='anadirReglaSLD(this.parentNode);' > añadir regla</a></h1></h2>
	                    </div>
	                </div>
	            </div>
	            
	            <form class='elementoCargaLargo' style='display:none;' id='FormularioNuevaUA' onsubmit='event.preventDefault();enviarDatosRegistro()'()>
	            	<div class='campo' id=''><input onclick="enviarCreaRegistroCapa()" type='button' value='dibujar nueva UA'></div>
	            	<div class='campo' id=''><input onclick="descargarMapaDXF()" type='button' value='descargar mapa DXF'></div>
	            </form>
	            
	            
	            
	            <form class='elementoCargaLargo' style='display:none;' id='FormularioRegistro' onsubmit='event.preventDefault();enviarDatosRegistro()'()>
	            	
	            	<h2>Datos Cargados</h2>
	                <div id='autoria'>por: <span id='usu'></span><br><span id='fecha'></span></div>
	                <div class='campo' id='secid'><label>id db:</label>:<input name='idgeom' readonly='readonly'></div>
	                <div class='campo' id='sect1'><label>nombre</label>:<input name='t1'></div>
	                
	                <div class='campo'>
	                	<input onclick='cambiarGeometria(this.parentNode.parentNode.querySelector("#secid [name=\"idgeom\"]").value)' type='button' value="redibujar geometría">
	                	<input type='hidden' idgeom='' id='nuevageometria'>
	                	<input onclick='borrarGeometrias("registro",this.parentNode.parentNode.querySelector("#secid [name=\"idgeom\"]").value)' type='button' value="eliminar UA"></div>
	                <div id='campospersonalizados'></div>
	                <input type='submit' value='guardar valores'>
	                <div>
	                	Datos completos UA: 
	                	<input type='checkbox' for='n1' valorsi='1' valorno='0' onchange='toglevalorSiNo(this)'>
	                	<input type='hidden' name='n1' value='0' onchange='toglevalorSiNoRev(this)'>
	                </div>
	            </form>
	            
	            
	            <form id='nuevocampo'>
	            	<h2>Campo</h2>
	            	<a onclick='guardarCampo()'>guardar campo</a>
	            	<a onclick='eliminarCampo()'>eliminar campo</a>
	            	<a onclick='cancelarCampo()'>cancelar</a>
	            	<br>
	            	<input name='idcampo' type='hidden' value=''>
	            	<label>Nombre:</label><input name='nombre'>
	            	<label>Ayuda:</label><textarea name='ayuda'></textarea>
	            	<label>Tipo de campo:</label><select name='tipo' onchange='cambiaTipoCampo()'>
	            		<option>- elegir -</option>
	            		<option value='texto'>texto</option>
	            		<option value='checkbox'>checkbox</option>
	            		<option value='select'>menu desplegable</option>
	            		<option value='numero'>numero</option>
	            		<option value='coleccion_imagenes'>imagenes</option>
	            	</select>
	            	
	            	<div para='select'>
	            	<label>opciones (separar con slto de línea):</label><textarea name='opciones_select'></textarea>
	            	</div>
	            	
	            	<div para='numero'>
	            	<label>Unidad de Medida:</label><input name='unidademedida'>	            	
	            	</div>
	            	
	            	<label>Incorporar a una matriz de campos</label>
	            	<input para='matriz' type='checkbox' onchange='toogleCheck(this);cambiaMatrizCampo()'><input name='matriz' type='hidden' value='-1'>
	            	<div para='matriz'>
	            		<label>Nombre matriz (debe ser igual en todos sus campos):</label><input name='nombre_matriz'>
	            		<label>Nombre columna:</label><input name='nombre_columna'>
	            		<label>Nombre fila:</label><input name='nombre_fila'>	            		
	            	</div>	
	            </form>
	       </div>     
        </div>
    </div>
</div>

<script type="text/javascript">

	var _Acc = "rele";
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
</script>
<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sistema/sis_acciones.js"></script> <!-- funciones de consulta general del sistema: acciones -->

<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_rele/app_rele_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_rele/app_rele_consultas.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_rele/app_rele_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_rele/app_rele_uploads.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
