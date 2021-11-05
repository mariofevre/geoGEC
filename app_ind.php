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
    <link href="./css/geogec_app_ind.css" rel="stylesheet" type="text/css">
    
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

            <div id='elemento'>
                <img src='./img/app_ind_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de carga de indicadores</h2>
                <div id='descripcion'>
            		Espacio definir, y visualizar indicadores.<br>
            		Esta aplicación permite cargar datos asociados a recortes territoriales, y visualizarlos en el mapa.                	
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
        	
            <div id='indicadorActivo' idindicador='0' class="elementoOculto"></div>
            <div class='capaEncabezadoCuadro tituloCuerpo'>
                <h1>Indicadores</h1>
            </div>
            
            <a onclick="accionCrearIndicador(this)" id='botonCrearIndicador'>Crear nuevo indicador</a>
            <a onclick='accionCargaCancelar(this)'  id="botonCancelarCarga">Volver al listado de indicadores</a>
            
            <div id="formSeleccionInd" class="elementoOculto">   
                <div class='formSeleccionIndCuerpo' id='divSeleccionIndCuerpo'>
                    <h1>Indicadores publicados</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunind'>- ninguno -</p>
                    <div id='listaindpublicadas'></div>
                </div>
            </div>
            
            <div id='formEditarIndicadores' idindicador='0' class="elementoOculto">
            
	            <div class="menuAcciones elementoOculto" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	                <a onclick='accionCreaEliminar(this)' id="botonEliminar">Eliminar</a>
	                <a onclick='accionCreaGuardar(this)' id="botonGuardar">Guardar</a>
	                <a onclick='accionCreaPublicar(this)' id="botonPublicar">Publicar</a>
	            </div>
            
                <div class='elementoCarga'>
                    <h1>Nombre del indicador</h1>
                    <input type="text" id="indNombre" onkeypress="accionEditarIndCampo(event, this)"></input>
                </div>
                <div class='elementoCarga'>
                    <h1>Descripción</h1>
                    <input type="text" id="indDescripcion" onkeypress="accionEditarIndCampo(event, this)"></input>
                </div>
                <div class='elementoCarga'>
                    <h1>Funcionalidad</h1>
                    <select onchange="editarIndFuncionalidad(event, this)" id="funcionalidadSelector">
                        <option value="elegir" selected>-elegir-</option>
                        <option value="geometriaExistente">Asignar valores a las geometrías de una capa existente</option>
                        <option value="nuevaGeometria">Asignar valores a nuevas geometrías en una capa de trabajo</option>
                        <option value="otrosIndicadores">Calcular valores a partir de otros indicadores (no disponible)</option>
                        <option value="archivosGeograficos">Cargar valores subiendo archivos geográficos (no disponible)</option>
                        <option value="vinculosExternos">Obtener valores de vínculos externos (no disponible)</option>
                    </select>
                    <p id="tipodeometriaNuevaGeometria">
                    <select onchange="" id='inputTipoGeom'>
                        <option value="Point">Puntos</option>
                        <option value="LineString">Lineas</option>
                        <option value="Polygon">Polígonos</option>
                    </select>
                    </p>
                </div>
                <div class='elementoCarga'>
                    <h1>Periodicidad</h1>
                    <select onchange="editarIndPeriodicidad(event, this)" id="periodicidadSelector">
                        <option value="elegir" selected>-elegir-</option>
                        <option value="mensual">Mensual</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
                <div class='elementoCarga'>
                    <h1>Fecha inicial</h1>
                    <input name="fechaDesde" id="inputFechaDesde" type="date" onchange="editarCampoInd('inputFechaDesde')">
                </div>
                <div class='elementoCarga'>
                    <h1>Fecha final</h1>
                    <input name="fechaHasta" id="inputFechaHasta" type="date" onchange="editarCampoInd('inputFechaHasta')">
                </div>
                <div class='elementoCargaLargo'>
                    <h1>Capa</h1>
                    <div id="AccionesSeleccionCapa">
                        <a onclick="accionSeleccionarCapa(this)" id='botonSeleccionarCapa'>Seleccionar Capa </a></br>
                        <a onclick='accionCancelarSeleccionCapa(this)' id="botonCancelarSeleccionarCapa" class="elementoOculto">Cancelar seleccion de capa</a></br>
                    </div>
                    <div id="capaseleccionada" idcapa="0" class="elementoOculto">
                        <div>
                            <label>Nombre de la capa</label>
                            <div id="capaNombre" class="divValor"></div>
                        </div>
                        <div>
                            <label>Descripción</label>
                            <div id="capaDescripcion" class="divValor"></div>
                        </div>
                        
                        <div id="AccionesSeleccionCapaCambio">
                            <a onclick="accionSeleccionarCapaCambio(this)" id='botonSeleccionarCapaCambio'>Cambiar Capa Seleccionada</a></br>
                            <a onclick='accionCancelarSeleccionCapaCambio(this)' id="botonCancelarSeleccionarCapaCambio" class="elementoOculto">Cancelar cambio de capa</a></br>
                        </div>
                    </div>
                    <div id="formSeleccionCapa" class="elementoOculto">
                        
                        <div class='formSeleccionCapaCuerpo' id='divSeleccionCapaCuerpo'>
                            <h1>Capas publicadas</h1>
                            <p id='txningunacapa'>- ninguna -</p>
                            <div id='listacapaspublicadas'></div>
                        </div>
                    </div>
                    <div class="elementoOculto">
                        <h1>Selector criterio de simbología</h1>
                        <label>color de relleno</label>
                        <input onchange='cargarFeatures()' type="color" id="inputcolorrelleno"/>
                        <label>transparencia de relleno</label>
                        <input onchange='cargarFeatures()' id="inputtransparenciarellenoNumber" min="0" max="100" oninput="inputtransparenciarellenoRange.value=inputtransparenciarellenoNumber.value" type="number" style="width: 10%">
                        <input onchange='cargarFeatures()' id="inputtransparenciarellenoRange" min="0" max="100" oninput="inputtransparenciarellenoNumber.value=inputtransparenciarellenoRange.value" type="range" style="width: 25%">
                        <label>color de trazo</label>
                        <input onchange='cargarFeatures()' type="color" id="inputcolortrazo"/>
                        <label>ancho de trazo (en pixeles)</label>
                        <input onchange='cargarFeatures()' id="inputanchotrazoNumber" min="0" max="10" oninput="inputanchotrazoRange.value=inputanchotrazoNumber.value" type="number" style="width: 10%">
                        <input onchange='cargarFeatures()' id="inputanchotrazoRange" min="0" max="10" oninput="inputanchotrazoNumber.value=inputanchotrazoRange.value" type="range" style="width: 25%">
                    </div>
                </div>
                
                <div class='elementoCargaLargo'>
                    <h1>Valores a cargar</h1>
                    <div class="indValoresRep"></div>
                    <div class="indValoresNombre">Nombre</div>
                    <div class="indValoresTipo">Tipo de dato</div>
                    <div class="indValoresUnidad">Unidad de medida</div>
                    <div id="columnasValores"></div>
                    <a onclick="accionAnadirNuevaColumnaValor(this)" id='botonAnadirNuevaColumnaValor'>+ añadir valor</a>
                </div>
                
                <div class='elementoCargaLargo'>
                    <h1>Representación</h1>
                    <div id='representacionescalacolor' tipo='numero'>
                        <div class="fila" id='sobre'>
                            <div id='colorejemplo'></div>
                            <div id='valorminimo'><p>sobre</p></div>                    		
                        </div>
                        <div class="fila" id='maximo'>
                            <div id='colorejemplo'></div>
                            <div id='valorminimo'><input id='maximo' placeholder='max' onkeyup='accionRepresentacionValorMaximo();'></div>                    		
                        </div>
                        <div class="fila" id='alto'>
                            <div id='colorejemplo'></div>
                            <div id='valorminimo'><p>medio</p></div>                    		
                        </div>
                        <div class="fila" id='bajo'>
                            <div id='colorejemplo'></div>
                            <div id='valorminimo'><input id='minimo' placeholder='min' onkeyup='accionRepresentacionValorMinimo();'></div>                    		
                        </div>
                        <div class="fila" id='minimo'>
                            <div id='colorejemplo'></div>
                            <div id='valorminimo'><p>sub</p></div>                    		
                        </div>
                        <div class="fila" id='sub'>
                            <div id='colorejemplo'></div>                  		
                        </div>
                    </div>
                    
                    <div id='representacionescalacolor' tipo='texto'></div>
                </div>
                
                 
                <div class='elementoCargaLargo' id='formcalculo'>
                    <h1>Cálculos automáticos</h1>
                    <div>Área de Influencia (buffer): <input type="text" id="calc_buffer"></input></div>
                    <div>Superposición con otras variables: <input type="text" id="calc_superp"></input></div>
                    <div>Segmentación territorial del resultado: <input type="text" id="calc_zonificacion"></input></div>
                </div>
                
            </div>
        
            
            <div class='formCargaInd' id="divListaIndicadoresCarga" idindicadorcarga="" class="elementoOculto">
            	<div id='avanceproceso'></div>
            	
            	 <div id="AccionesSeleccionIndCambio">
                    <h1>Acciones</h1>
                    <a id='botonelim' onclick='eliminarCandidatoCapa(this.parentNode);' title="Eliminar Capa">Eliminar</a>
                    <a id='botonguarada' onclick='guardarCapa(this.parentNode);' title="guardar esta capa preliminarmente">Guardar</a>
                    <a id='botonpublica' onclick='publicarCapa(this.parentNode);' >Publicar</a>
                </div>
                
                <h2><span id="indTituloNombre"></span><a onclick="accionSeleccionarIndCambio(this)" id="botonSeleccionarIndCambio">editar</a></h2>
                
                <div>	
                    <label>Nombre del indicador</label>
                    <div id="indCargaNombre" class="divValor"></div>
                </div>
                <div>
                    <label>Descripción</label>
                    <div id="indCargaDescripcion" class="divValor"></div>
                </div>
                <div>
                    <label>Periodicidad</label>
                    <div id="indCargaPeriodicidad" class="divValor"></div>
                </div>
	           
	                
                <div class='elementoCargaLargo' id='periodo'>
                	<h2>Datos Cargados</h2>
                    <div class="scrolling-wrapper" id="selectorPeriodo">
                    </div>
                    
                    
                    <div id="divPeriodoSeleccionado" ano="" mes="" class="elementoOculto" idgeom="">
                    	<h3><span id="tituloPariodoSeleccionado"></h3>
               
               
                        <div style="display: inline-block;">
                            <label>Periodo Seleccionado</label>
                            <div id="indCargaPeriodoLabel" class="divValor"></div>
                        </div>
                        
                        <div class="menuAcciones" id="divMenuAccionesEditarValor" style="display: inline-block;">
                            <a onclick='accionEditarValorGuardar(this)' id="botonGuardar">Guardar</a>
                            <a onclick='accionEditarCrearGeometria(this)' id="botonCrearGeom">Añadir geometría</a>
                            <a onclick='accionCopiarGeometriaAnterior(this)' id="botonDuplicarGeom">Copiar geometría del período anterior</a>
                        </div>
						<div id='listaUnidadesInd'></div>
							
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./index_mapa.js"></script> <!-- carga funciona de gestión de mapa-->
<script type="text/javascript" src="./app_ind/app_ind_queries.js"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_ind/app_ind_pagina.js"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./app_ind/app_ind_mapa.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

</body>