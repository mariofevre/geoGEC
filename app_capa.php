<?php
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_capa. Aplicacion para la gestión de documento
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
    <link href="./css/geogec_app_docs.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_capa.css" rel="stylesheet" type="text/css">
    
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

            <div id='elemento'>
                <img src='./img/app_capa_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de capas complementarias de información</h2>
                <div id='descripcion'>Espacio para visualizar, explorar y descargar capas compartidas.</div>
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
        	
            <div class='capaEncabezadoCuadro'>
                <h1>Capas Complementarias de Información</h1>
            </div>
            
            <a onclick="accionCargarNuevaCapa(this)" id='botonAnadirCapa'>Subir una nueva capa a la plataforma</a>
            <a onclick="accionReleaCapa(this)" id='botonReleaCapa'>Crear una capa a partir de un relevamiento</a>
            
            <div class="formSeleccionCapa" id="divSeleccionCapa">
                <div class='elementoCarga accionesCapa'>
                    <a id="botonCancelarCarga"  onclick='accionCancelarSeleccionCapa(this)'>Cancelar</a></br>
                </div>
                <div class='formSeleccionCapaCuerpo' id='divSeleccionCapaCuerpo'>
                    <h1>Capas publicadas</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunacapa'>- ninguna -</p>
                    <div id='listacapaspublicadas'></div>
                </div>
            </div>
            
            <div style='display:none;' class='formReleACapa' id='divReleACapa' idcapa='' >
                				
                <div class='formReleACapaCuerpo' id='ReleACapa'>
                    <div id='idcampa' class='elementoCarga'>
                        <h1>Campaña de relevamiento</h1>
                        <select name='idcampa'>
                        	<option value=''>-elegir-</option>	
                        </select>
                    </div>
                	<div id='campos'>
                		
                		<p>campo1 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo2 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo3 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo4 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		<p>campo5 nombre:<input id='nombre'> fuente:<select name='fuentec1'></select></p>
                		
                		
                	</div>    
                </div>
                
            </div>
                    
            <div class='formCargaCapa' id='divCargaCapa' idcapa=''>
                <div id='avanceproceso'></div>
                <div class='elementoCarga accionesCapa'>
                    <h1>Acciones</h1>
                    <a onclick='accionCancelarCargarNuevaCapa(this)'>Volver al listado de capas</a></br>
                    <a id='botonexporta' onclick='exportarCapaMenu();'>Exportar a otro proyecto</a></br>
                    <a id='botonelim' onclick='eliminarCandidatoCapa(this.parentNode);' title="Eliminar Capa">Eliminar</a></br>
                    <a id='botonguarada' onclick='guardarCapa(this.parentNode);' title="guardar esta capa preliminarmente">Guardar</a></br>
                    <a id='botonpublica' onclick='publicarCapa(this.parentNode);' >Publicar</a>
                </div>
				
				<div id='exportarCapaMenu'>
					<a onclick='this.parentNode.style.display="none"'>Cancelar</a>
					<h2>Seleccione un proyecto al cual copiar esta capa</h2>
					<div id='lista'>							
					</div>	
				</div>
				
				 	
                <div class='formCargaCapaCuerpo' id='carga'>
                    <div id='nombrecapa' class='elementoCarga'>
                        <h1>Nombre de la capa <div id='cantreg'>registros:<span id='contador'></span></div></h1>
                        <input type="text" id="capaNombre" onkeypress="editarCapaNombre(event, this)"></input>
                    </div>
                    
                    <div id='desccapa' class='elementoCarga'>
                        <h1>Descripción</h1>
                        <textarea type="text" id="capaDescripcion" onkeypress="editarCapaDescripcion(event, this)"></textarea>
                    </div>
                    

					<div id='tipo_fuente' class="elementoCarga">
						
						<p> modo WMS 
						<label class="switch">
						  <input type="checkbox" for='modo_wms' valorsi="si" valorno="no" onclick="toglevalorSiNo(this)">
						  <span class="slider round"></span>
						  <input type="hidden" name='modo_wms' onchange='toglevalorSiNoRev(this)'>
						</label>
						</p>	
						
						
						<p>Información pública	                    
						  <select name='modo_publica'>
						  	<option value=''>-elegir-</option>	
						  	<option value='personal'>Información preliminar</option>
						  	<option value='gec'>Publicada para otros equipos GEC</option>
						  	<option value='publica'>Pública</option>
						  </select>						
						</p>	
						
						
	                    <p> Origen del dato
						  <select name='tipo_fuente'>
						  	<option value=''>-elegir-</option>	
						  	<option value='propia'>Datos propio</option>
						  	<option value='derivada'>Datos derivados de otra fuente</option>
						  	<option value='externa'>Datos tomados de otra fuente</option>
						  </select>						
						</p>
						
								
						
						<p>Geometría
	                    
						  <select onchange='actualizarOpcionesGeometria()' name='tipogeometria'>
						  	<option value=''>-elegir-</option>	
						  	<option value='Point'>Point</option>
						  	<option value='LineString'>LineString</option>
						  	<option value='Polygon'>Polygon</option>
						  	<option value='Tabla'>Tabla sin geometrías</option>
						  </select>
						
						</p>
								
								
						<p id='vinculaciones'>Vincular a geometrías de otra capa<br>
						
							<a onclick='consultarCapasLinkeables()'>Elegir capa externa</a><input type='hidden' name='link_capa'><span id='muestra_link_capa'></span><br>
							<a onclick='consultarCamposExternosLinkeables()'>Elegir campo externo de viculación</a><input type='hidden' name='link_capa_campo_externo'><span id='muestra_link_capa_campo_externo'></span><br>
							<a onclick='mostrarCamposLocalesLinkeables()'>Elegir campo local de viculación</a><input type='hidden' name='link_capa_campo_local'><span id='muestra_link_capa_campo_local'></span><br>
							
							<div id='formlinkcapa' class='formlink'>
								<a onclick='cerrarFormLink(this)'>cerrar</a>
								<div id='lista'></div>
							</div>
							
							<div id='formlinkcampoexterno' class='formlink'>
							<a onclick='cerrarFormLink(this)'>cerrar</a>
								<div id='lista'></div>
							</div>
							
							<div id='formlinkcampolocal' class='formlink'>
							<a onclick='cerrarFormLink(this)'>cerrar</a>
							<div id='lista'></div>
							</div>
						</p>
						
					</div>
					
					
					
					<div id='fechas' class="elementoCarga">
						
						<p>Año del dato
                        <input type="number" name="fecha_ano"></input></p>
					
						<p>Mes del dato
                        <input type="number" name="fecha_mes"></input></p>
                   
						<p>Dia del dato
                        <input type="number" name="fecha_dia"></input></p>
                        
                    </div>   
                    
                    
                    
					<div  id='configurarCampos'  class='elementoCargaLargo'>                 
                    	<h1>Campos</h1>  
                    	
                    	
                    	
                    	<div class='campo' id='campo_texto_1'>
							<h3>campo de texto <span class='n'>1</span></h3>
							<label>Nombre:</label><input name='nom_col_text1' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text1' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_2'>
							<h3>campo de texto <span class='n'>2</span></h3>
							<label>Nombre:</label><input name='nom_col_text2' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text2' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_3'>
							<h3>campo de texto <span class='n'>3</span></h3>
							<label>Nombre:</label><input name='nom_col_text3' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text3' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_4'>
							<h3>campo de texto <span class='n'>4</span></h3>
							<label>Nombre:</label><input name='nom_col_text4' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text4' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_texto_5'>
							<h3>campo de texto <span class='n'>5</span></h3>
							<label>Nombre:</label><input name='nom_col_text5' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_text5' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	
                    	
                    	
                    	
                    	<div class='campo' id='campo_numero_1'>
							<h3>campo numérico <span class='n'>1</span></h3>
							<label>Nombre:</label><input name='nom_col_num1' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num1' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_2'>
							<h3>campo numérico <span class='n'>2</span></h3>
							<label>Nombre:</label><input name='nom_col_num2' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num2' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_3'>
							<h3>campo numérico <span class='n'>3</span></h3>
							<label>Nombre:</label><input name='nom_col_num3' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num3' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_4'>
							<h3>campo numérico <span class='n'>4</span></h3>
							<label>Nombre:</label><input name='nom_col_num4' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num4' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	<div class='campo' id='campo_numero_5'>
							<h3>campo numérico <span class='n'>5</span></h3>
							<label>Nombre:</label><input name='nom_col_num5' onchange='setAttribute("editado","si")'></input>
							<label>Nombre Corto (shapefile):</label><input name='cod_col_num5' onchange='setAttribute("editado","si")'></input>
                    	</div>
                    	
                    </div>
				
				
				
                    <div  id='cargarGeometrias'  class='elementoCargaLargo'>                 
                    	<h1>Gargar geometrías</h1>   	
	                    <div id='earchivoscargando' class='elementoCarga'>
	                        <h2>archivos cargando</h2>
	                        <div id='archivosacargar'>
	                            <form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
	                                <label style='position:relative;' class='upload'>							
	                                <span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo</span>
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
	                        <div id='tituloscampos'>
	                        <p id='titulonombres'>nombre definido</p>
	                        <p id='titulocampo'>campos disponibles</p>
	                        <p id='tituloasignado'>contenido asignado</p></p>
	                        </div>
	                        
	                        <p id='verproccampo'></p>
	                        <div id='camposident'></div>
	                        <p id='titulodesechar'>Contenidos a desechar:</p>
	                        <div id='camposdesecha'></div>
	                        
	                        <a id='procesarBoton' onclick='procesarCapa(this.parentNode)' estado='inviable'>Procesar Shapefile</a>
	                    </div>
                    </div>
                    <div id='simbologia' class='elementoCargaLargo'>
                        <h1>Simbología<a onclick='guardarSLD(this.parentNode);' > Guardar Simbología</a></h1>
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
        </div>
    </div>
</div>

<script type="text/javascript">
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	var _Acc = "capa";
</script>
<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./app_capa/app_capa_consultas.js"></script> <!-- carga funciones de consultas ajax -->
<script type="text/javascript" src="./app_capa/app_capa_interaccion.js"></script> <!-- carga funciones dei interacción -->
<script type="text/javascript" src="./app_capa/app_capa_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_capa/app_capa_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_capa/app_capa_Shapefile.js"></script> <!-- carga funciones de operacion del formulario central para la carga de SHP -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>
