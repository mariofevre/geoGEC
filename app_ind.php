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
    <title>GEC - Plataforma Geom�tica</title>
    <?php include("./includes/meta.php");?>
    <link href="./css/mapauba.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link rel="manifest" href="pantallahorizontal.json">
    
    <link href="./css/geogecgeneral.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_ind.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">
    
    <style>
	  
		#ventanagrafico{
			display:none;
			background-color:#fff;
			position:fixed;
			top:max(80px, calc(50vh - 400px));
			left:max(80px, calc(50vw - 500px));
			border:2px solid #08afd9;
			box-shadow:10px 10px 20px rgba(0,0,0,0.5);
			z-index:2000
		}
		#ventanagrafico[estado='activo']{
			display:block;
		}
		
		#ventanagrafico a{
			position:absolute;
			top:10px;
			left:10px;
		}
		#ventanagrafico a#botondescargar{
			top:60px;			
		}
			
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
                <p>Plataforma Geom�tica del centro de Gesti�n de Espacios Costeros</p>
            </a>

            <div id='elemento'>
                <img src='./img/app_ind_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de carga de indicadores</h2>
                <div id='descripcion'>
            		Espacio definir, y visualizar indicadores.<br>
            		Esta aplicaci�n permite cargar datos asociados a recortes territoriales, y visualizarlos en el mapa.                	
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
            <div id='botonera_mapa'></div>
        </div>
        <div id="cuadrovalores">
        	
            <div id='indicadorActivo' idindicador='0' class="elementoOculto"></div>
            <div class='capaEncabezadoCuadro tituloCuerpo'>
                <h1>Indicadores</h1>
            </div>
             <div class="cajaacciones">
				<a onclick="accionCrearIndicador(this)" id='botonCrearIndicador'><img src="./img/agregar.png"> Crear indicador</a>
				<a onclick="accionCrearModelo()" id='botonCrearModelo'><img src="./img/agregar.png"> Crear Modelo</a>
				<a onclick='accionCargaCancelar(this)'  id="botonCancelarCarga">Volver al listado de indicadores</a>
				<a onclick='cargarListadoModelo()'  id="botonIndicadoresModelo">Mostrar indicadores modelo</a>
				<a onclick='formularCruce()'  id="botonFormularCruce">Cruzar indicadores</a>
			</div>	
            <div id="formSeleccionInd" class="elementoOculto">   
                <div class='formSeleccionIndCuerpo' id='divSeleccionIndCuerpo'>
                    <h1>Indicadores publicados</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaI' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunind'>- ninguno -</p>
                    <div id='listaindpublicadas'></div>
                </div>
            </div>
            
            <div id="formSeleccionMod" class="elementoOculto">   
                <div class='formSeleccionModCuerpo' id='divSeleccionModCuerpo'>
                    <h1>Modelos disponibles</h1>
                    <div id='barrabusqueda'><input id='barrabusquedaM' autocomplete='off' value='' onkeyup="actualizarBusqueda(event);"><a onclick='limpiaBarra(event);'>x</a></div>
                    <p id='txningunmod'>- ninguno -</p>
                    <div id='listamodpublicadas'></div>
                </div>
            </div>
            
            <div id='formEditarIndicadores' idindicador='0' class="elementoOculto">
            
	            <div class="menuAcciones elementoOculto" id="divMenuAccionesCrea">
	            	<h1>Acciones</h1>
	                <a onclick='accionCreaEliminar(this)' id="botonEliminar">Eliminar</a>
	                <a onclick='accionCreaGuardar(this)' id="botonGuardar">Guardar</a>
	                <a onclick='accionCreaPublicar(this)' id="botonPublicar">Publicar</a>
	                <a onclick='formularioAmpliado("indicador")' id="botonAmpliar">Formulario Completo</a>
	            </div>
            
                <div class='elementoCarga'>
                    <h1>Nombre del indicador</h1>
                    <input type="text" id="indNombre" onkeypress="accionEditarIndCampo(event, this)"></input>
                </div>
                <div class='elementoCarga'>
                    <h1>Descripci�n</h1>
                    <input type="text" id="indDescripcion" onkeypress="accionEditarIndCampo(event, this)"></input>
                </div>
                <div class='elementoCarga'>
                    <h1>Funcionalidad</h1>
                    <select onchange="editarIndFuncionalidad(event, this)" id="funcionalidadSelector">
						
                        <option value="elegir">-elegir-</option>
                        <option value="sinGeometria">Asignar un �nico valor para toda el �rea</option>
                        <option value="geometriaExistente">Asignar valores a geometr�as fijas</option>
                        <option value="nuevaGeometria">Asignar valores a nuevas cambiantes</option>
                        <option value="otrosIndicadores">Calcular valores a partir de otros indicadores (no disponible)</option>
                        <option value="archivosGeograficos">Cargar valores subiendo archivos geogr�ficos (no disponible)</option>
                        <option value="vinculosExternos">Obtener valores de v�nculos externos (no disponible)</option>
                    </select>
                    
                    
                    <p id="tipodeometriaNuevaGeometria">
                    <select onchange="" id='inputTipoGeom'>
                        <option value="Point">Puntos</option>
                        <option value="LineString">Lineas</option>
                        <option value="Polygon">Pol�gonos</option>
                    </select>
                    </p>
                </div>
                <div class='elementoCarga'>
                    <h1>Periodicidad</h1>
                    <select onchange="editarIndPeriodicidad(event, this)" id="periodicidadSelector">
                        <option value="elegir" selected>-elegir-</option>
                        <option value="diario">Diario</option>
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
						tipo de geometr�a: 
						<select name='tipogeometria'>
						  	<option value=''>-elegir-</option>	
						  	<option value='Point'>Point</option>
						  	<option value='LineString'>LineString</option>
						  	<option value='Polygon'>Polygon</option>
						</select>
						<a 
							title='la capa auxiliar aloja las UA del indicador pero no es visible en el menu de capas' 
							onclick="accionCrearCapaAux(this)" id='botonCrearCapaAux'
							>Crear Capa Auxiliar</a>
						</br>
                        <a 
							title='En lugar de usar una capa auxiliar (no visible) se puede utilizar una capa ya cargada' 
							onclick="accionSeleccionarCapa(this)" id='botonSeleccionarCapa'
							>Seleccionar Capa de referencia </a>
						</br>
                        <a onclick='accionCancelarSeleccionCapa(this)' id="botonCancelarSeleccionarCapa" class="elementoOculto">Cancelar seleccion de capa</a></br>
                                        
                        
                    </div>
                    <div id="capaseleccionada" idcapa="0" class="elementoOculto">
                        <div>
                            <label>Nombre de la capa</label>
                            <div id="capaNombre" class="divValor"></div>
                        </div>
                        <div>
                            <label>Descripci�n</label>
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
                        <h1>Selector criterio de simbolog�a</h1>
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
                    <a onclick="accionAnadirNuevaColumnaValor(this)" id='botonAnadirNuevaColumnaValor'>+ a�adir valor</a>
                </div>
                
                <div class='elementoCargaLargo'>
                    <h1>Representaci�n</h1>
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
                    <h1>C�lculos autom�ticos</h1>
                    <div>�rea de Influencia (buffer): <input type="text" id="calc_buffer"></input></div>
                    <div>Superposici�n con otras variables: <input type="text" id="calc_superp"></input></div>
                    <div>Segmentaci�n territorial del resultado: <input type="text" id="calc_zonificacion"></input></div>
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
                    <label>Descripci�n</label>
                    <div id="indCargaDescripcion" class="divValor"></div>
                </div>
                <div>
                    <label>Periodicidad</label>
                    <div id="indCargaPeriodicidad" class="divValor"></div>
                </div>
                <div>
                    <label>Capa alojando geometrias</label>
                    <div id="indCapaGeom" class="divValor"></div>
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
                        
                        <div class="menuAcciones cajaacciones" id="divMenuAccionesEditarValor" style="display: inline-block;">
                            <a onclick='accionEditarValorGuardar(this)' id="botonGuardar">Guardar</a>
                            <a onclick='accionEditarCrearGeometria(this)' id="botonCrearGeom">A�adir geometr�a</a>
                            <a onclick='accionCopiarGeometriaAnterior(this)' id="botonDuplicarGeom">Copiar geometr�a del per�odo anterior</a>
                            <a onclick='formularCopiarGeometriaCapa(this)' id="botonCopiarGeomCapa">Copiar geometr�as de una capa</a>
                            <a 
								title='Puede generar una capa auxiliar a partir de los datos de un relevamiento' 
								onclick="formularCopiarGeometriaRele(this)" id='botonImporatrRele'
								>Importar datos de un relevamiento</a>
						</br>     
                        </div>
                        <form id='formImportarRele'>
							<div id='listarelesfuente'></div>
							<div id='selectorCamposRele'>
							<p>campo 1:<select name='campo_importar_1'></select></p>
							<p>campo 2:<select name='campo_importar_2'></select></p>
							<p>campo 3:<select name='campo_importar_3'></select></p>
							<p>campo 4:<select name='campo_importar_4'></select></p>
							<a id='accionImportarRele' id_rele='' onclick='importarRele()'>Importar</a>
							</div>
                        </form>
                        <form>
							<div id='listacapasfuente'></div>
                        </form>
						<div id='listaUnidadesInd'></div>
							
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>


<div class='formcent' id='form_ind_exp'>
	<div class='borde-contenido'>
	<div class='contenido'>
		<div class="cajaacciones">
			<a class='' onclick='$("#form_ind_exp").attr("estado","inactivo")'>Cerrar</a>
			<a class='eliminar' onclick='accionEliminarFormCent()'>Eliminar</a>
		</div>
		<div id='esmodelo' class='cajaacciones'>
			<a class='administra' onclick='accionGuardaMod()'>Guardar Cambios</a>
			<a>Generar Indicador desde este modelo</a>
		</div>
		
		<div id='esindicador' class='cajaacciones'>
			<a>Guardar Cambios</a>	
			<a class='administra'>Generar un modelo desde este indicador</a>
		</div>
		
		<div id='caracter�sticas_generales'>
			
			<input name="id" type='hidden'>
			
			<h4>Definici�n te�rica</h4>
			
			
			<div class="row">
				<div class="col">
					<label>nombre</label><input name="nombre" type='text'>
				</div>	
					
				<div class="col">	
					<label>unidad_medida</label><input name="unidad_medida" type='text'>
				</div>
				
				<div class="col">
					<label>escala_espacial</label><input name="escala_espacial" type='text'>
				</div>
				
				<div class="col">
					<label>desagrgacion</label><input name="desagrgacion" type='text'>
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<label>descripcion</label><textarea name="descripcion"></textarea>
				</div>	
					
				<div class="col">	
					<label>relevancia_acc</label><textarea name="relevancia_acc"></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<label>limitaciones</label><textarea name="limitaciones"></textarea>
				</div>	
					
				<div class="col">	
				<label>ejemplo</label><textarea name="ejemplo"></textarea>
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<label>Datos necesarios para su generaci�n</label><textarea name="datos_input"></textarea>
				</div>
				
				<div class="col">
					<label>Fuentes posibles para la obteci�n de datos</label><textarea name="fuentes_input"></textarea>
				</div>
			</div>
			
			<div class="row">
				
				<div class="col">
				<label>calculo</label><textarea name="calculo"></textarea>
				</div>

				
				<div class="col">
				<label>valoracion</label><textarea name="valoracion"></textarea>
				</div>
			</div>
			
			<h4>Configuraci�n general en la plataforma</h4>

			<div class="row">
				<div class="col">
				<label>funcionalidad</label><select name="funcionalidad">
					<option value="elegir" selected>-elegir-</option>
					<option value="geometriaExistente">Asignar valores a las geometr�as de una capa existente</option>
					<option value="nuevaGeometria">Asignar valores a nuevas geometr�as en una capa de trabajo</option>
					<option value="otrosIndicadores">Calcular valores a partir de otros indicadores (no disponible)</option>
					<option value="archivosGeograficos">Cargar valores subiendo archivos geogr�ficos (no disponible)</option>
					<option value="vinculosExternos">Obtener valores de v�nculos externos (no disponible)</option>                    
				</select>
				</div>
				
				<div class="col">
				<label>periodicidad</label><select name="periodicidad">
					<option value="elegir" selected>-elegir-</option>
					<option value="mensual">Mensual</option>
					<option value="anual">Anual</option>
				</select>		
				</div>
			</div>
			
			<div class="row">
				
				<div class="col">		
					<label>representar_campo</label><select name="representar_campo">
						<option value="elegir" selected>-elegir-</option>			
					</select>
				</div>
				
				<div class="col">	
					<label>representar_val_max</label><input name="representar_val_max" type='text'>
				</div>
								
				<div class="col">	
					<label>representar_val_min</label><input name="representar_val_min" type='text'>
				</div>
			</div>
		
			<h4>Configuraci�n particular en la plataforma</h4>
			
			<div class="row">
				
				<div class="col">	
					<label>Capa a la cual se superpondr�</label><select name="calc_superp">
						<option value="elegir" selected>-elegir-</option>
						<option value="mensual">Mensual</option>
						<option value="anual">Anual</option>
					</select>	
				</div>
			
				<div class="col">		
					<label>Distancia de cobertura</label><input name="calc_buffer" type='calc_buffer'>
				</div>
				
				<div class="col">	
					<label>Campo de valoraci�n de la cobertura</label><select name="calc_superp_campo">
						<option value="elegir" selected>-elegir-</option>
						<option value="mensual">Mensual</option>
						<option value="anual">Anual</option>
					</select>			
				</div>
				
				<div class="col">		
					<label>Capa de zonas de agregaci�n</label><select name="calc_zonificacion">
						<option value="elegir" selected>-elegir-</option>
						<option value="mensual">Mensual</option>
						<option value="anual">Anual</option>
					</select>	
				</div>
						
				<div class="col">
					<label>fechadesde</label><input name="fechadesde" type='date'>
				</div>
				
				<div class="col">
					<label>fechahasta</label><input name="fechahasta" type='date'>
				</div>				
			</div>			
		</div>
		
		<div id='matriz_clasif'>
			<h4>Categorizaci�n del modelo</h4>
			<div id='categorias' class="row">
			</div>
		</div>
		
		<div id='requerimientos'>
			<h4>Requerimientos</h4>
			<div id='requerimientos_app' class="row">
			</div>
		</div>		
	</div>
	</div>
</div>



<div class='formcent' id='form_cruce'>
	<div class='borde-contenido'>
	<div class='contenido'>
		<div class="cajaacciones">
			<a class='' onclick='$("#form_cruce").attr("estado","inactivo")'>Cerrar</a>
		</div>
		<div id='esmodelo' class='cajaacciones'>
			<a class='administra' onclick='generarGrafico()'>Guardar Cambios</a>
			<a onclick='generarCruce("grafico");'>Generar gr�fico de an�lisis cruzado</a>
		</div>
		
		<div>			
			
			<input name="id" type='hidden'>
			
			<h4>Definici�n te�rica</h4>
			
			
			<h2>Variable principal</h2>
			<i>* obligatorio</i>
			<p>El indicador elegido definir� las unidades de salida (geogr�ficas y temporales)</p>
			<p>Se aceptan geometr�as de tipo punto y pol�gono</p>
			<select name='inicador_1' onchange='actualizarFormCruceSi1()'></select>
			<select name='campo_i_1'></select>
			
			
			<h2>Variable secundaria</h2>
			<i>* opcional</i>
			<p>El indicador elegido se aplicar� a la variable principal deacuedoa su superposici�n</p>
			<p>se aceptan puntos y pol�gonos (deber superponerse a la variable principal y ser de tipo de geometr�a diferente)</p>
			<p>si la temporalidad es menor a la principal, se promediar�n los valores</p>
			
			<select name='inicador_2' onchange='actualizarFormCruceSi1Campos2()'></select>
			<select name='campo_i_2'></select>
			
			
			<h2>Caficiaci�n por factor</h2>
			<i>* opcional</i>
			<p>Se puede incoroprar una capa con simbolog�a, en la medida que las unidades principales est�n contenidas en un pol�gono de esta capa se el aplicar� es c�digo de color correspondiente</p>
			<p>Solo acepta pol�gonos</p>
			<p>Solo se acepta para indicadore principales de tipo punto</p>
			
			<select name='capa_3'></select>
			
						
		</div>		
	</div>
	</div>
</div>


<div id='ventanagrafico'>
<a id='botoncerrar' class='boton' onclick="this.parentNode.setAttribute('estado','inactivo')">cerrar</a>
<a id='botondescargar' class='boton' onclick="descargarImagen()">descargar imagen</a>
<canvas height="800" width="1000">

</canvas>
	

</div>			

<script type="text/javascript">
	
	var _IdUsu='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';
	var _Acc = "capa";
	
	<?php if(!isset($_GET["idr"])){$_GET["idr"]='';} ?>		
	var _idInd = '<?php echo $_GET["idr"];?>';

    //Variable de filtro en b�squedas de datos.
    <?php if(!isset($_SESSION['geogec']['usuario']['recorte'])){$_SESSION['geogec']['usuario']['recorte']='';};?>
    
	_RecorteDeTrabajo=JSON.parse('<?php echo json_encode($_SESSION['geogec']['usuario']['recorte']);?>');

  	
  	<?php if(!isset($_GET['id'])){$_GET['id']='';} ?>	
  	<?php if(!isset($_GET['cod'])){$_GET['cod']='';} ?>	
  	var _IdMarco = '<?php echo $_GET['id'];?>';
	var _CodMarco = '<?php echo $_GET['cod'];?>';
	var _DataUsuaries={};
	
  	var _DataIndicador={};
	var _DataPeriodo=Array();
	var _DataCapa=Array();
	
	var _DataListaIndicadores={};
	var _DataListaModelos={};
	
	
	var _Data_reles={}; //variable para almacenar informaci�n de relevamientos (otro m�dulo) en caso de quere importar datos de un relevamiento.
	var _DataCapas={}; //variable para almacenar informaci�n de capas (otro m�dulo) en caso de quere cruzar indicadores con capas
	
	var _Acciones={}; //completada por "./sistema/sis_acciones.js"
	
	
	var _Id_Modelo_Editando=''; // si est� definido lo muestra tras una consulta general.

	
	_fechaHoy = new Date();
	var _Select_Fecha={
		'ano':_fechaHoy.getFullYear(),
		'mes':_fechaHoy.getMonth()+1,
		'dia':_fechaHoy.getDate()
	}
	//console.log(_Select_Fecha);
	
</script>


<script type="text/javascript" src="./comun_interac/comun_interac.js?t=<?php echo time();?>"></script> <!-- definicion de funcions comunes como la interpretacion de respuestas ajax-->

<script type="text/javascript" src="./sistema/sistema_marco.js?t=<?php echo time();?>"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./sistema/sis_acciones.js?t=<?php echo time();?>"></script> <!-- carga funcion de consulta de acciones y ejecuci�n, completa _Acciones -->

<script type="text/javascript" src="./comun_mapa/comun_mapa_inicia.js?t=<?php echo time();?>"></script> <!-- definicion de variables comunes para mapas en todos los m�dulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_recorte.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de recorte para mapas en todos los m�dulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_selector_capas.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de selector de capa base y extras para mapas en todos los m�dulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_localiz.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de localizacion de direcciones para mapas en todos los m�dulos-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_tamano.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de definicion de variables y funciones de agrandar mapa-->
<script type="text/javascript" src="./comun_mapa/comun_mapa_descarga.js?t=<?php echo time();?>"></script> <!-- definicion de variables y funciones de descarga del mapa activo-->


<script type="text/javascript" src="./app_ind/app_ind_mapa.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript" src="./app_ind/app_ind_queries.js?t=<?php echo time();?>"></script> <!-- carga funciones de consulta de base de datos -->
<script type="text/javascript" src="./app_ind/app_ind_pagina.js?t=<?php echo time();?>"></script> <!-- carga funciones de operacion de la pagina -->
<script type="text/javascript" src="./comunes_consultas.js?t=<?php echo time();?>"></script> <!-- carga funciones de interaccion con el mapa -->




<script type="text/javascript">
	
	baseMapaaIGN();//cargar mapa base IGN
	
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
	
	
	if(_RecorteDeTrabajo!=''){
		cargaRecorteSession();
	}

	if(_idInd!=''){
		accionIndicadorPublicadoSeleccionado(this,_idInd);
	}
	
 
</script>

</body>
