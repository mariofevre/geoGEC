<?php 
/**
* aplicación de visualización y gestion de la planificación de marcos academicos; procedos de producción de datos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);
ini_set('display_errors', '1');
// verificación de seguridad 
//include('./includes/conexion.php');
if(!isset($_SESSION)) {
	 session_start(); 

	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

if(!isset($_POST['modo'])){
	$_POST['modo']='enumeracion';
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
	<link href="./css/BaseSonido.css" rel="stylesheet" type="text/css">
	<link href="./css/ad_navega.css" rel="stylesheet" type="text/css">	
	<link href="./css/tablarelev.css" rel="stylesheet" type="text/css">
	<link rel="manifest" href="pantallahorizontal.json">
	<link href="./css/BA_salidarelevamiento.css" rel="stylesheet" type="text/css">
	<link href="./css/geogecindex.css" rel="stylesheet" type="text/css">
	<link href="./css/geogec_app_docs.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_plan.css" rel="stylesheet" type="text/css">
    	
</head>

<body>
	
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
			
			<div id='elemento'>
				<img src='./img/app_plan_hd.png' style='float:left;'>
				<h2 id='titulo'>Gestor de la Planificación del Proyecto</h2>
				<div id='descripcion'>
					Espacio para cargar y gestionar planificaciónes de proyecto.
					Cada Proyecto puede ser desagreado en actividades y estas ser definidas según su descripción, su fecha prevista de finalización, su estado de avance y sus responsables.
					
				</div>
			</div>	
		</div>
		<div id='menutablas'>
			<h1 id='titulo'>- nombre de proyecto -</h1>
			<p id='descripcion'>- descripcion de proyecto -</p>
		</div>	
		
		<div id='portamapa'>
			<div id='titulomapa'><p id='tnombre'></p><h1 id='tnombre_humano'></h1><p id='tdescripcion'></p><b><p id='tseleccion'></p></b></div>
		</div>
		
		<div id="contenidoextenso" idit='0' nivel='0'>
			<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
			<a id='botonanadir' onclick='anadirItem(event,0)'>+ nueva actividad</a>
			<div 
				class='hijos'
				nivel="0"
				ondrop="drop(event,this)" 
				ondragover="allowDrop(event,this);resaltaHijos(event,this)" 
				ondragleave="desaltaHijos(this)" 
				ondblclick="anadirItem(event,this.parentNode.getAttribute('idit'));"
			></div>
		</div>
		
		<div id='modelos'>
			
			<div
				class='item'
				idit='nn'
				draggable="true"
				ondragstart="dragcaja(event);bloquearhijos(event,this);"
				ondragleave="limpiarAllowFile()"
				ondragover="allowDropFile(event,this)"
				ondrop='dropFile(event,this)'
			>
				<div id='max'>
					<h1 id='num'>N</h1>
					<div id='resp'><div class='pack'><a title='añadir un nuevo responsable' onclick='sumarResp(this)'>+</a></div></div>
                                        <div id='estadoActividad' class="estadoActividad">
                                            <div class="barraProgresoPropio">
                                                <div class="progressbar-container barraProgresoPropioBar" style="width:0%">0%</div>   
                                            </div>
                                        </div>
				</div>
				<h3 onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>nombre de esta unidad de planificación</h3>
				<p onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>descripción de la unidad de planificación</p>
				<div class='documentos'>
				</div>
				<div 
					class='hijos'
					ondrop="drop(event,this)"
					ondragover="allowDrop(event,this)"
					ondragleave="limpiarAllow()" 
					ondblclick="anadirItem(event,this.parentNode.getAttribute('idit'));"
				></div>
			</div>
		</div>

	</div>	
</div>
	

<form id="editoritem" onsubmit="guardarI(event,this)">
        <label>Nombre de la Unidad de Planificación</label>
        <input name='nombre'>
        <input name='id' type='hidden'>
        <label>Descripcion de la Unidad de Planificación</label>
        <textarea name='descripcion'></textarea>
        <label>Progreso de la Unidad de Planificación</label>
        <input type="number" name="progresoNumber" id="progresoNumber" min="0" max="100" oninput="progresoRange.value=progresoNumber.value">
        <input type="range" name="progresoRange" id="progresoRange" min="0" max="100" oninput="progresoNumber.value=progresoRange.value">
        <label class="fechaPropuesta">Finalización: </label>
        <input type="date" name="fechaPropuesta" id="fechaPropuesta">
        <label class="progresoCambiadoPor">Cambiado por: </label>
        <div class="autorCambioItem" name="autorCambioItem">Autor</div>
        <label>Documentos Asociados a la Unidad de Planificación</label>
        <div class="listaDocumentosAsociados"></div>
        <a class="editarListaDocs" onclick="consultarDocumentosAsociados('abrirEditorDocs',this)">Editar Listado</a>

        <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
        <input type='submit' value='guardar'>
        <a id='botonelimina' onclick='eliminarI(event,this)'>eliminar</a>
</form>

<form id="editorresp">
        <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
        <h1>Nombre de la Unidad de Planificación</h1>
        <div id="tituloexcluidos">usuarios sin <br>responsabilidades asignadas.</div>
        <div id="tituloincluidos">usuarios con <br>responsabilidades asignadas.</div>
        <div id="tituloresponsabilidad">responsabilidad <br> asignada</div>
        <div id="excluidos"></div>
        <div id="incluidos"></div>
        <input name='idit' type='hidden'>
</form>

<form id="editorlistadocs">
        <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
        <h1>Documentos Asociados a la Unidad de Planificación</h1>
        <div id="tituloexcluidos">Documentos disponibles</div>
        <div id="tituloincluidos">Documentos asociados</div>
        <div id="documentocomentario">Comentario</div>
        <div id="excluidos"></div>
        <div id="incluidos"></div>
        <input name='idit' type='hidden'>
</form>

<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>

<script type='text/javascript'>
	///funciones para cargar información base
        var _IdMarco='<?php echo $ID;?>';
        var _CodMarco='<?php echo $COD;?>';	
        var _Items=Array();
        var _Orden=Array();
        var _UsuId ='<?php echo $_SESSION["geogec"]["usuario"]['id'];?>';

        function cargaBase(_accion){
                var _accion=_accion;

                if(_accion=='cargainicial'){
                        document.querySelector('#contenidoextenso > .hijos').innerHTML='';
                }

                _parametros = {
                        'idMarco': _IdMarco,
                        'codMarco': _CodMarco
                };

                $.ajax({
                        data: _parametros,
                        url:   './app_plan/app_plan_consulta.php',
                        type:  'post',
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                //console.log(_res);
                                for(_nm in _res.mg){
                                        alert(_res.mg[_nm]);
                                }
                                if(_res.res=='exito'){		
                                        _Items=_res.data.psdir;
                                        _Orden=_res.data.orden;
                                        if(_accion=='cargainicial'){
                                                generarItemsHTML();
                                                cargaDocumentos();
                                        }else if(_accion=='actualizaresponsables'){
                                                mostrarResponsables();
                                        }else if(_accion=='actualizarprogreso'){
                                                mostrarProgreso();
                                        }

                                }else{
                                        alert('error dsfg');
                                }
                        }
                });
        }

        cargaBase('cargainicial');

        function cargaElim(_res){
                //Actualiza la carga al eliminar un registro;
                _contelim=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_res.data.idit+'"]');
                _niv=_contelim.getAttribute('nivel');
                _nnn=parseInt(_niv)+1;
                _itemes=_contelim.querySelectorAll('.item[nivel="'+_nnn+'"]');
                //alert(_nnn);
                for(_ni in _itemes){
                        //console.log(_itemes[_ni]);

                        if(typeof _itemes[_ni]!='object'){continue;}
                        _dest=document.querySelector('#contenidoextenso > .hijos');
                        console.log(_dest);
                        _dest.appendChild(_itemes[_ni].previousSibling);
                        _dest.appendChild(_itemes[_ni]);
                }
                _contelim.parentNode.removeChild(_contelim.previousSibling);
                _contelim.parentNode.removeChild(_contelim);
                nivelar();
                numerar();
        }
        
        function nivelar(){
                //actualiza el valor de nivel de cada item
                _itemes=document.querySelectorAll('#contenidoextenso > .hijos .item');
                for(_ni in _itemes){
                        if(typeof _itemes[_ni]!='object'){continue;}
                        _nn=parseInt(_itemes[_ni].parentNode.parentNode.getAttribute('nivel'))+1;
                        _itemes[_ni].setAttribute('nivel',_nn);
                }

        }

        function mostrarResponsables(){
                //muestra los responsables en cada item cargado
                _itemes=document.querySelectorAll('#contenidoextenso > .hijos .item');
                for(_ni in _itemes){

                        if(typeof _itemes[_ni]!='object'){continue;}

                         _idit=_itemes[_ni].getAttribute('idit');
                         _dat=_Items[_idit];
                         _itemhtml=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"]');
                         _itemhtml.querySelector('#resp').innerHTML='';

                         _pck=document.createElement('div');
                         _pck.setAttribute('class','pack');
                         _itemhtml.querySelector('#resp').appendChild(_pck);
                         if(Object.keys(_dat.responsables).length==0){
                                _aaa=document.createElement('a');
                                _aaa.setAttribute('class','vacio');
                                _aaa.title='asignar responsables';
                                _aaa.setAttribute('onclick',"sumarResp(this)");
                                _aaa.innerHTML="+";
                                _pck.appendChild(_aaa);
                         }else{
                                _pckcount=0;
                                 for(_nr in _dat.responsables){
                                        _pckcount++;
                                        if(_pckcount==3){
                                                _pck=_pck.cloneNode(false);
                                                 _itemhtml.querySelector('#resp').appendChild(_pck);
                                                _pckcount=0;
                                        }
                                        _aaa=document.createElement('a');
                                        if(_UsuId==_dat.responsables[_nr].id_p_sis_usu_registro){
                                                _aaa.setAttribute('class','vos');
                                        }
                                        _aaa.title=_dat.responsables[_nr].nombre+' '+_dat.responsables[_nr].apellido+' \n '+_dat.responsables[_nr].responsabilidad;
                                        _aaa.setAttribute('onclick',"sumarResp(this)");
                                        _aaa.innerHTML=_dat.responsables[_nr].nombre.substring(0, 1)+_dat.responsables[_nr].apellido.substring(0, 1);
                                        _pck.appendChild(_aaa);
                                }
                        }
                }
        }
        
        function mostrarProgreso(){
            //muestra el progreso en cada item cargado
            _itemes=document.querySelectorAll('#contenidoextenso > .hijos .item');
            for(_ni in _itemes){
                if(typeof _itemes[_ni]!='object'){continue;}

                _idit=_itemes[_ni].getAttribute('idit');
                _dat=_Items[_idit];
                _itemhtml=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"]');

                _itemhtml.querySelector('#estadoActividad').innerHTML='';
                var divProgreso = _itemhtml.querySelector('#estadoActividad');
                if(Object.keys(_dat.estados).length==0){
                    var porcentajeProgreso = 0;
                    var divProgresoPropio =document.createElement('div');
                    divProgresoPropio.setAttribute('class','barraProgresoPropio');
                    var divProgresoPropioBar =document.createElement('div');
                    divProgresoPropioBar.setAttribute('class','progressbar-container barraProgresoPropioBar');
                    divProgresoPropioBar.setAttribute('style','width:'+porcentajeProgreso+'%');                
                    divProgresoPropioBar.innerHTML = porcentajeProgreso + '%';
                    divProgresoPropio.appendChild(divProgresoPropioBar);
                    divProgreso.appendChild(divProgresoPropio);
                } else {
                    var porcentajeProgreso = obtenerProgresoActividad(_idit);
                    var divProgresoPropio =document.createElement('div');
                    divProgresoPropio.setAttribute('class','barraProgresoPropio');
                    var divProgresoPropioBar =document.createElement('div');
                    divProgresoPropioBar.setAttribute('class','progressbar-container barraProgresoPropioBar');
                    divProgresoPropioBar.setAttribute('style','width:'+porcentajeProgreso+'%');                
                    divProgresoPropioBar.innerHTML = porcentajeProgreso + '%';
                    divProgresoPropio.appendChild(divProgresoPropioBar);
                    divProgreso.appendChild(divProgresoPropio);
                }
                
                var actividadesHijas = obtenerItemActividadesHijas(_idit);
                if (actividadesHijas && actividadesHijas.length > 0){
                    var porcentajeProgreso = calcularProgresoActividadNumerico(_idit,_Items);
                    var divProgresoHerencia =document.createElement('div');
                    divProgresoHerencia.setAttribute('class','barraProgresoHerencia');
                    var divProgresoHerenciaBar =document.createElement('div');
                    divProgresoHerenciaBar.setAttribute('class','progressbar-container barraProgresoHerenciaBar');
                    divProgresoHerenciaBar.setAttribute('style','width:'+porcentajeProgreso+'%');                
                    divProgresoHerenciaBar.innerHTML = porcentajeProgreso + '%';
                    divProgresoHerencia.appendChild(divProgresoHerenciaBar);
                    divProgreso.appendChild(divProgresoHerencia);
                }
            }
        }

        function actualizarItem(_res){
                var _preres = _res;
                _parametros = {
                        'idMarco': _IdMarco,
                        'codMarco': _CodMarco
                };

                $.ajax({
                        data: _parametros,
                        url:   './app_plan/app_plan_consulta.php',
                        type:  'post',
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                //console.log(_res);
                                for(_nm in _res.mg){
                                        alert(_res.mg[_nm]);
                                }
                                if(_res.res=='exito'){		
                                        _Items=_res.data.psdir;
                                        _Orden=_res.data.orden;
                                        document.querySelector('.item[idit="'+_preres.data.idit+'"] > h3').innerHTML=_res.data.psdir[_preres.data.idit].nombre;
                                        document.querySelector('.item[idit="'+_preres.data.idit+'"] > p').innerHTML=_res.data.psdir[_preres.data.idit].descripcion;
                                }else{
                                        alert('error dsfg');
                                }
                        }
                });
        }

        function calcularProgresoActividad(actividadId){
            var progresoActividadTotal = calcularProgresoActividadNumerico(actividadId);
            return "Progreso: " + progresoActividadTotal + '%';
        }

        function calcularProgresoActividadNumerico(actividadId){
            //Esto asume que _Items es global y siempre es correcta
            var progresoActividadTotal = parseInt(0);

            //if no hijos -> devolver estado de actividad propio
            //if si hijos -> calcularProgresoActividad (hijo) pesado
            var actividadesHijas = obtenerItemActividadesHijas(actividadId);
            if (actividadesHijas.length === 0){
                var estadoProgreso = obtenerEstadoProgreso(actividadId);
                if (!estadoProgreso){
                    progresoActividadTotal = parseInt(0);
                } else {
                    progresoActividadTotal = estadoProgreso.porcentaje_progreso;
                }
            } else {
                var progresoActividadesHijas = new Array();
                for(actividad in actividadesHijas){
                    var progresoHijaNumerico = calcularProgresoActividadNumerico(actividadesHijas[actividad]);
                    progresoActividadesHijas.push(progresoHijaNumerico);
                }

                var cantidadHijas = progresoActividadesHijas.length;
                for(progresoHija in progresoActividadesHijas){
                    progresoActividadTotal += (progresoActividadesHijas[progresoHija] / cantidadHijas);
                }
            }
            return Math.round(progresoActividadTotal);
        }
        
        function obtenerProgresoActividad(actividadId){
            var progresoActividad = 0;
            var estadoProgreso = obtenerEstadoProgreso(actividadId);
            if (estadoProgreso === null){ 
                progresoActividad = parseInt(0);
            } else {
                progresoActividad = estadoProgreso.porcentaje_progreso;
            }
            
            return progresoActividad;
        }

        function obtenerItemActividadesHijas(actividadId){
            var actividadesHijas = new Array();
            for(actividad in _Items){
                if (_Items[actividad].id_p_sis_planif_plan == actividadId){
                    actividadesHijas.push(actividad);
                }
            }

            return actividadesHijas;
        }

        function cargaNuevoItem(_res){
                var _preres = _res;
                _parametros = {
                        'idMarco': _IdMarco,
                        'codMarco': _CodMarco
                };

                $.ajax({
                        data: _parametros,
                        url:   './app_plan/app_plan_consulta.php',
                        type:  'post',
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                //console.log(_res);
                                for(_nm in _res.mg){
                                        alert(_res.mg[_nm]);
                                }
                                if(_res.res=='exito'){

                                        _Items=_res.data.psdir;
                                        _Orden=_res.data.orden;

                                        _dat=_Items[_preres.data.nid];
                                        _clon=document.querySelector('#modelos .item').cloneNode(true);
                                        _clon.setAttribute('idit',_preres.data.nid);
                                        _clon.querySelector('h3').innerHTML=_dat.nombre;
                                        if(_dat.descripcion==null){_dat.descripcion='- planificación sin descripción -';}
                                        _clon.querySelector('p').innerHTML=_dat.descripcion;

                                        _dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_dat.id_p_sis_planif_plan+'"] > .hijos');
                                        if(_dest==null){
                                                _dest=document.querySelector('#contenidoextenso > .hijos');
                                        }
                                        _niv=_dest.parentNode.getAttribute('nivel');
                                        console.log('_dat.nombre:'+_niv);
                                        _niv++;
                                        console.log('>>'+_niv);
                                        _clon.setAttribute('nivel',_niv);
                                        _dest.appendChild(_clon);

                                        _esp=document.createElement('div');				
                                        _esp.setAttribute('class','medio');
                                        _esp.innerHTML='<div class="submedio"></div>';
                                        _esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
                                        _esp.setAttribute('ondragleave',"desaltaHijos(this)");
                                        _esp.setAttribute('ondrop',"drop(event,this)");  

                                        _dest.insertBefore(_esp,_clon);


                                        $('html, body').animate({
                                scrollTop: $("div[idit='"+_preres.data.nid+"']").offset().top
                                }, 2000);



                                        nivelar();
                                        numerar();

                                }else{
                                        alert('error dsfg');
                                }
                        }
                });
        }		

        function generarItemsHTML(){
                //genera un elemento html por cada instancia en el array _Items
                for(_nO in _Orden.psdir){

                        _ni=_Orden.psdir[_nO];

                        _dat=_Items[_ni];
                        _clon=document.querySelector('#modelos .item').cloneNode(true);				
                        _clon.setAttribute('idit',_dat.id);				
                        if(_dat.nombre==null){_dat.nombre='- planificación sin nombre -';}

                        _clon.querySelector('h3').innerHTML=_dat.nombre;
                        if(_dat.descripcion==null){_dat.descripcion='- planificación sin descripción -';}
                        _clon.querySelector('p').innerHTML=_dat.descripcion;
                        _clon.setAttribute('nivel',"1");

                        document.querySelector('#contenidoextenso > .hijos').appendChild(_clon);
                }

                //anida los itmes genreados unos dentro de otros
                for(_nO in _Orden.psdir){
                        _ni=_Orden.psdir[_nO];
                        _el=document.querySelector('#contenidoextenso > .hijos > .item[idit="'+_Items[_ni].id+'"]');

                        if(_Items[_ni].id_p_sis_planif_plan!='0'){
                                //alert(_Items[_ni].id_p_ESPitems_anidado);

                                _dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_Items[_ni].id_p_sis_planif_plan+'"] > .hijos');
                                if(_dest==null){
                                        _dest=document.querySelector('#contenidoextenso > .hijos');
                                }
                                _dest.appendChild(_el);
                        }
                }

                _itemscargados=document.querySelectorAll('#contenidoextenso > .hijos .item');

                for(_nni in _itemscargados){
                        if(typeof _itemscargados[_nni]=='object'){
                                _esp=document.createElement('div');				
                                _esp.setAttribute('class','medio');
                                _esp.innerHTML='<div class="submedio"></div>';
                                _esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
                                _esp.setAttribute('ondragleave',"desaltaHijos(this)");
                                _esp.setAttribute('ondrop',"drop(event,this)");  

                                _itemscargados[_nni].parentNode.insertBefore(_esp, _itemscargados[_nni]);
                        }
                }

                numerar();
                mostrarResponsables();
                mostrarProgreso();
        }
</script>

<script type='text/javascript'>
///funciones para asignar documentos
var _DocItems = new Array();
var _DocOrden = new Array();
var _DocAsoc = new Array();

function cargaDocumentos(){
    var parametros = {
        'idMarco': _IdMarco,
        'codMarco': _CodMarco
    };

    $.ajax({
        data: parametros,
        url:   './app_docs/app_docs_consulta.php',
        type:  'post',
        success:  function (response){
            var respuesta = $.parseJSON(response);
            console.log(respuesta);
            if(respuesta.res=='exito'){		
                _DocItems=respuesta.data.psdir;
                _DocOrden=respuesta.data.orden;
            }else{
                alert('error dsfg');
            }
        }
    });
}

function consultarDocumentosAsociados(accion, _this){
    var parametros = {
        'idMarco': _IdMarco,
        'codMarco': _CodMarco,
        'idactividad': _idit
    };
    $.ajax({
        url:  './app_plan/app_plan_doc_asociados_consulta.php',
        type: 'post',
        data: parametros,
        success:  function (response)
        {
            var respuesta = $.parseJSON(response);
                console.log(respuesta);
            if(respuesta.res=='exito') {
                _DocAsoc = respuesta.data.psdir;
                resolverDocumentosAsociados(accion, _this);
            } else {
                alert('error asfocueffgh');
            }
        }
    });
}

function resolverDocumentosAsociados(accion, _this){
    if (accion == ''){
        alert('error asfocuefftygh');
    } else if (accion == 'abrirEditorDocs') {
        formEditorListaDocs(_this);
    } else if (accion == 'actualizarDocumentosAsociados') {
        listarDocumentosAsociados();
    }
}

function formEditorListaDocs(_this){
    console.log(_this);

    var form = document.querySelector('#editorlistadocs');
    form.querySelector('#excluidos').innerHTML='';
    form.querySelector('#incluidos').innerHTML='';			
    form.style.display='block';
    form.querySelector('input[name="idit"]').value = _idit;
    for(docItem in _DocItems){
        if ((_DocItems[docItem].archivos && Object.keys(_DocItems[docItem].archivos).length > 0)  ||
                (_DocItems[docItem].archivolinks && Object.keys(_DocItems[docItem].archivolinks).length > 0)){    
            var carpeta = document.createElement('span');
            carpeta.setAttribute('class','CarpetaDocs');
            var tituloCarpeta = document.createElement('span');
            tituloCarpeta.innerHTML = _DocItems[docItem].nombre;
            carpeta.appendChild(tituloCarpeta);
            form.querySelector('#excluidos').appendChild(carpeta);
        }
        
        for (archivo in _DocItems[docItem].archivos){
            if (_DocItems[docItem].archivos[archivo].zz_borrada == '0'){
                _div = document.createElement('div');
                _div.setAttribute('iddoc', _DocItems[docItem].archivos[archivo].id);
                _div.setAttribute('tipoDoc', 'documento');
                _div.setAttribute('estado','excluido');
                _span = document.createElement('span');
                _span.setAttribute('onclick','togleDocAsoc(this.parentNode)');
                _span.innerHTML = _DocItems[docItem].archivos[archivo].nombre;
                _div.appendChild(_span);

                _input=document.createElement('input');
                _input.setAttribute('id','asociacion');
                _input.setAttribute('estado','ok');
                _input.setAttribute('onkeypress','cargaDocumentosAsociados(event,this)');
                _div.appendChild(_input);
                form.querySelector('#excluidos').appendChild(_div);

                _clon=_div.cloneNode(true);
                form.querySelector('#incluidos').appendChild(_clon);
            }
        }
        
        for (archivoLink in _DocItems[docItem].archivolinks){
            if (_DocItems[docItem].archivolinks[archivoLink].zz_borrada == '0'){
                _div = document.createElement('div');
                _div.setAttribute('iddoc', _DocItems[docItem].archivolinks[archivoLink].id);
                _div.setAttribute('tipoDoc', 'url');
                _div.setAttribute('estado','excluido');
                _span = document.createElement('span');
                _span.setAttribute('onclick','togleDocAsoc(this.parentNode)');
                _span.innerHTML = _DocItems[docItem].archivolinks[archivoLink].nombre;
                _div.appendChild(_span);

                _input=document.createElement('input');
                _input.setAttribute('id','asociacion');
                _input.setAttribute('estado','ok');
                _input.setAttribute('onkeypress','cargaDocumentosAsociados(event,this)');
                _div.appendChild(_input);
                form.querySelector('#excluidos').appendChild(_div);

                _clon=_div.cloneNode(true);
                form.querySelector('#incluidos').appendChild(_clon);
            }
        }
    }
    
    for(docAsoc in _DocAsoc.archivos){
        var iddoc = _DocAsoc.archivos[docAsoc].id_ref_01_documentos;
        document.querySelector('#incluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');
        document.querySelector('#excluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');

        document.querySelector('#incluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivos[docAsoc].comentario;
        document.querySelector('#excluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivos[docAsoc].comentario;
    }
    for(docAsoc in _DocAsoc.archivolinks){
        var iddoc = _DocAsoc.archivolinks[docAsoc].id_ref_doc_links;
        document.querySelector('#incluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');
        document.querySelector('#excluidos div[iddoc="'+iddoc+'"]').setAttribute('estado','incluido');

        document.querySelector('#incluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivolinks[docAsoc].comentario;
        document.querySelector('#excluidos div[iddoc="'+iddoc+'"] input').value = _DocAsoc.archivolinks[docAsoc].comentario;
    }
}

function togleDocAsoc(_this){
    var _nuevoestado;
    if(_this.parentNode.getAttribute('id')=='incluidos'){
        _nuevoestado='excluido';
    }else{
        _nuevoestado='incluido';
    }
    _this.parentNode.parentNode.querySelector('#excluidos div[iddoc="'+_this.getAttribute('iddoc')+'"]').setAttribute('estado',_nuevoestado);
    _this.parentNode.parentNode.querySelector('#incluidos div[iddoc="'+_this.getAttribute('iddoc')+'"]').setAttribute('estado',_nuevoestado);

    _idit=_this.parentNode.parentNode.querySelector('input[name="idit"]').value;
    var comentario=_this.parentNode.parentNode.querySelector('#incluidos div[iddoc="'+_this.getAttribute('iddoc')+'"] input').value;

    guardarCambiosDocs(_this.getAttribute('tipoDoc'), _this.getAttribute('iddoc'), _idit, _nuevoestado, comentario);
}

function guardarCambiosDocs(tipoDoc,iddoc,idit,nuevoestado,comentario){
        _parametros = {
            "codMarco":_CodMarco,
            "iddoc":iddoc,
            "idit":idit,
            "nuevoestado":nuevoestado,
            "comentario":comentario
        };
        
        var postUrl = '';
        if(tipoDoc != ''){
            if(tipoDoc == 'documento'){
                postUrl = './app_plan/app_plan_doc_asociados_guardar.php';
            } else if(tipoDoc == 'url'){
                postUrl = './app_plan/app_plan_doc_asociados_url_guardar.php';
            }
        }
        
        $.ajax({
                data: _parametros,
                url:   postUrl,
                type:  'post',
                success:  function (response){
                    var _res = $.parseJSON(response);
                    console.log(_res);

                    for(_nn in _res.mg){
                        alert(_res.mg[_nn]);
                    }
                    if(_res.res=='exito'){						
                        document.querySelector('#incluidos div[iddoc="'+_res.data.iddoc+'"] input').setAttribute('estado','ok');
                        consultarDocumentosAsociados('actualizarDocumentosAsociados', this);
                    }
                }
            });			
}

function cargaDocumentosAsociados(_event,_this){
        console.log(_event.keyCode);
        if(_event.keyCode==9){return;}//tab
        if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
        _this.setAttribute('estado','editando');
        if(_event.keyCode==13){
                _this.setAttribute('estado','guardando');
                _iddoc=_this.parentNode.getAttribute('iddoc');
                var tipoDoc=_this.parentNode.getAttribute('tipoDoc');
                _idit=_this.parentNode.parentNode.parentNode.querySelector('input[name="idit"]').value;
                _nuevoestado='incluido';
                var comentario=_this.value;

                guardarCambiosDocs(tipoDoc,_iddoc,_idit,_nuevoestado,comentario);
        }
}
</script>
	
	<script type='text/javascript'>
	///funciones para asignar responsables	
	function sumarResp(_this){

		var _this=_this;
		
		_parametros = {
			"idMarco": _IdMarco,
			'codMarco': _CodMarco
		};
		$.ajax({
			url:   './usuarios/usu_consulta_ajax.php',
			type:  'post',
			data: _parametros,
			success:  function (response){
				var _res = $.parseJSON(response);
					console.log(_res);
				if(_res.res=='exito'){
					formResponsables(_this,_res);
				}else{
					alert('error asfffgh');
				}
			}
		});	

	}
			 
	function formResponsables(_this,_usuarios){
                //abre el formulario para edittar item
                console.log(_this);

                _idit=_this.parentNode.parentNode.parentNode.parentNode.getAttribute('idit');
                _num=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"] #num').innerHTML;
                _nom=document.querySelector('#contenidoextenso .item[idit="'+_idit+'"] h3').innerHTML;

                _form=document.querySelector('#editorresp');
                _form.querySelector('#excluidos').innerHTML='';
                _form.querySelector('#incluidos').innerHTML='';			
                _form.style.display='block';
                _form.querySelector('input[name="idit"]').value=_idit;			
                _form.querySelector('h1').innerHTML=_num+' '+_nom;	
                for(_nu in _usuarios.data){
                        _div=document.createElement('div');
                        _div.setAttribute('idusu',_usuarios.data[_nu].id);
                        _div.setAttribute('estado','excluido');
                        _span=document.createElement('span');
                        _span.setAttribute('onclick','togleResp(this.parentNode)');
                        _span.innerHTML=_usuarios.data[_nu].apellido+', '+_usuarios.data[_nu].nombre+' <span id="log">'+_usuarios.data[_nu].log+'</span>';
                        _div.appendChild(_span);

                        _input=document.createElement('input');
                        _input.setAttribute('id','responsabilidad');
                        _input.setAttribute('estado','ok');
                        _input.setAttribute('onkeypress','cargaResponsabilidad(event,this)');
                        _div.appendChild(_input);	

                        _form.querySelector('#excluidos').appendChild(_div);

                        _clon=_div.cloneNode(true);
                        _form.querySelector('#incluidos').appendChild(_clon);
                }	

                cargarResponsablesenForm(_idit);
	}

	function cargarResponsablesenForm(_idit){
            _parametros={
                    "codMarco":_CodMarco,
                    "idit":_idit
            };
            console.log(_parametros);
            $.ajax({
                    data: _parametros,
                    url:   './app_plan/app_plan_consulta_resp.php',
                    type:  'post',
                    success: function (response){
                                var _res = $.parseJSON(response);
                                console.log(_res);

                                for(_nn in _res.mg){
                                    alert(_res.mg[_nn]);
                                }
                                if(_res.res=='exito'){		
                                    for(_idusu in _res.data.resp){
                                        _dat=_res.data.resp[_idusu];

                                        if(_dat.zz_borrada=='0'){
                                            document.querySelector('#incluidos div[idusu="'+_idusu+'"]').setAttribute('estado','incluido');
                                            document.querySelector('#excluidos div[idusu="'+_idusu+'"]').setAttribute('estado','incluido');
                                        } else {
                                            document.querySelector('#incluidos div[idusu="'+_idusu+'"]').setAttribute('estado','excluido');
                                            document.querySelector('#excluidos div[idusu="'+_idusu+'"]').setAttribute('estado','excluido');
                                        }

                                        document.querySelector('#incluidos div[idusu="'+_idusu+'"] input').value=_dat.responsabilidad;
                                        document.querySelector('#excluidos div[idusu="'+_idusu+'"] input').value=_dat.responsabilidad;
                                    }
                                }			
                            }
                    });		
	}	

	function togleResp(_this,_usuarios){
		
		if(_this.parentNode.getAttribute('id')=='incluidos'){
			_nuevoestado='excluido';
		}else{
			_nuevoestado='incluido';
		}
		_this.parentNode.parentNode.querySelector('#excluidos div[idusu="'+_this.getAttribute('idusu')+'"]').setAttribute('estado',_nuevoestado);
		_this.parentNode.parentNode.querySelector('#incluidos div[idusu="'+_this.getAttribute('idusu')+'"]').setAttribute('estado',_nuevoestado);
		
		_idit=_this.parentNode.parentNode.querySelector('input[name="idit"]').value;
		_responsabilidad=_this.parentNode.parentNode.querySelector('#incluidos div[idusu="'+_this.getAttribute('idusu')+'"] input').value;
		
		guardarCambiosResp(_this.getAttribute('idusu'),_idit,_nuevoestado,_responsabilidad);
	}
	
	function guardarCambiosResp(_idusu,_idit,_nuevoestado,_responsabilidad){
		var _idusu=_idusu;
		_parametros={
			"codMarco":_CodMarco,
			"idusu":_idusu,
			"idit":_idit,
			"nuevoestado":_nuevoestado,
			"responsabilidad":_responsabilidad
		};
		$.ajax({
				data: _parametros,
				url:   './app_plan/app_plan_guardarresp.php',
				type:  'post',
				success:  function (response){
					var _res = $.parseJSON(response);
					console.log(_res);
					
					for(_nn in _res.mg){
						alert(_res.mg[_nn]);
					}
					if(_res.res=='exito'){						
						document.querySelector('#incluidos div[idusu="'+_res.data.idusu+'"] input').setAttribute('estado','ok');
						//_Items[_res.data.idit]['responsables'][_res.data.idusu]=_res.data;
						cargaBase('actualizaresponsables');
					}
				}
			});			
	}
	
	function cargaResponsabilidad(_event,_this){
		console.log(_event.keyCode);
		if(_event.keyCode==9){return;}//tab
		if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
		_this.setAttribute('estado','editando');
		if(_event.keyCode==13){
			_this.setAttribute('estado','guardando');
			_idusu=_this.parentNode.getAttribute('idusu');
			_idit=_this.parentNode.parentNode.parentNode.querySelector('input[name="idit"]').value;
			_nuevoestado='incluido';
			_responsabilidad=_this.value;
			
			guardarCambiosResp(_idusu,_idit,_nuevoestado,_responsabilidad);
			
			
		}
	}
	</script>
		
	<script type='text/javascript'>
	///funciones para editar y crear items
		function resaltar(_this){
			//realta el div del item al que pertenese un título o una descripcion
			
			_dests=document.querySelectorAll('[resaltado="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('resaltado');
				}
			}
			_this.parentNode.setAttribute('resaltado','si');
			
		}
		function desaltar(_this){
			//realta el div del item al que pertenese un título o una descripcion
			_dests=document.querySelectorAll('[resaltado="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('resaltado');
				}
			}
			
		}
		function editarI(_this){
                    //abre el formulario para editar item
                    _idit=_this.parentNode.getAttribute('idit');
                    _form=document.querySelector('#editoritem');
                    _form.style.display='block';
                    _form.querySelector('input[name="nombre"]').value=_Items[_idit].nombre;
                    _form.querySelector('input[name="id"]').value=_Items[_idit].id;
                    _form.querySelector('[name="descripcion"]').value=_Items[_idit].descripcion;
                    var estadoMasNuevo = obtenerEstadoProgreso(_idit);
                    if (estadoMasNuevo !== null){
                        porcentajeProgreso = estadoMasNuevo.porcentaje_progreso;
                        _form.querySelector('input[name="progresoNumber"]').value = porcentajeProgreso;
                        _form.querySelector('input[name="progresoRange"]').value = porcentajeProgreso;
                        _form.querySelector('.autorCambioItem').innerText = estadoMasNuevo.nombre + " " + estadoMasNuevo.apellido;
                        _form.querySelector('input[name="fechaPropuesta"]').value = estadoMasNuevo.fecha_propuesta;
                    } else {
                        _form.querySelector('input[name="progresoNumber"]').value = 0;
                        _form.querySelector('input[name="progresoRange"]').value = 0;
                        _form.querySelector('.autorCambioItem').innerText = " ";
                        _form.querySelector('input[name="fechaPropuesta"]').value = null;
                    }

                    consultarDocumentosAsociados('actualizarDocumentosAsociados', _this)
		}
                
                function listarDocumentosAsociados(){
                    var listaDocumentosAsociados = _form.querySelector('.listaDocumentosAsociados');
                    listaDocumentosAsociados.innerHTML = '';
                    for (var asoc in _DocAsoc.archivos) {
                        var archivoLista = document.createElement('a');
                        archivoLista.setAttribute('class','archivoLista has-tooltip');
                        archivoLista.innerHTML = _DocAsoc.archivos[asoc].nombre;
                        archivoLista.setAttribute('href',_DocAsoc.archivos[asoc].archivo);
                        archivoLista.setAttribute('download', _DocAsoc.archivos[asoc].nombre);
                        if (_DocAsoc.archivos[asoc].comentario && _DocAsoc.archivos[asoc].comentario != ''){
                            archivoLista.setAttribute('data-tooltip', _DocAsoc.archivos[asoc].comentario);
                        }
                        listaDocumentosAsociados.appendChild(archivoLista);
                    }
                    for (var asoc in _DocAsoc.archivolinks) {
                        var archivoLista = document.createElement('a');
                        archivoLista.setAttribute('class','archivoLista has-tooltip');
                        archivoLista.innerHTML = _DocAsoc.archivolinks[asoc].nombre;
                        archivoLista.setAttribute('href',_DocAsoc.archivolinks[asoc].url);
                        archivoLista.setAttribute('download', _DocAsoc.archivolinks[asoc].nombre);
                        if (_DocAsoc.archivolinks[asoc].comentario && _DocAsoc.archivolinks[asoc].comentario != ''){
                            archivoLista.setAttribute('data-tooltip', _DocAsoc.archivolinks[asoc].comentario);
                        }
                        listaDocumentosAsociados.appendChild(archivoLista);
                    }
                }
                
                function obtenerEstadoProgreso(idit){
                        var estadoMasNuevo = null;
                        if ((idit in _Items) && (_Items[idit].estados !== null)){
                            var fecha_cambio = new Date();
                            fecha_cambio.setYear(1);
                            for (estadoId in _Items[idit].estados){
                                var estadoFecha = new Date(_Items[idit].estados[estadoId].fecha_cambio);
                                if (estadoFecha.getTime() > fecha_cambio.getTime()){
                                    fecha_cambio = estadoFecha;
                                    estadoMasNuevo = _Items[idit].estados[estadoId];
                                }
                            }
                        }
                        
                        return estadoMasNuevo;
                }
		
		function cerrar(_this){
			//cierra el formulario que lo contiene
			_this.parentNode.style.display='none';
		}
		
		function eliminarI(_event,_this){
			if (confirm("¿Eliminar item y sus archivos asociados? \n (los ítems anidados quedarán en la raiz)")==true){
				
				_event.preventDefault();
				
				var _this=_this;
				
				_parametros = {
					"id": _this.parentNode.querySelector('input[name="id"]').value,
					"accion": "borrar",
					"tipo": "item",
					"idMarco": _IdMarco,
					'codMarco': _CodMarco
				};
				$.ajax({
					url:   './app_plan/app_plan_borraritem.php',
					type:  'post',
					data: _parametros,
					success:  function (response){
						var _res = $.parseJSON(response);
							console.log(_res);
						if(_res.res=='exito'){	
							cerrar(_this);
							//cargaBase();
							cargaElim(_res);
						}else{
							alert('error asfffgh');
						}
					}
				});
				//envía los datos para editar el ítem		
			}
		}
		
		function guardarI(_event,_this){// ajustado geogec
                    _event.preventDefault();
                    console.log(_this);
                    var _this=_this;
                        
                    //Guardar datos del item generales
                    _parametros = {
                            "idMarco":_IdMarco,
                            'codMarco': _CodMarco,
                            "id": _this.querySelector('input[name="id"]').value,
                            "nombre": _this.querySelector('input[name="nombre"]').value,
                            "descripcion": _this.querySelector('[name="descripcion"]').value
                    };
                    $.ajax({
                            url:   './app_plan/app_plan_cambiaritem.php',
                            type:  'post',
                            data: _parametros,
                            success:  function (response){
                                    var _res = $.parseJSON(response);
                                    console.log(_res);
                                    if(_res.res=='exito'){	
                                            cerrar(_this.querySelector('#botoncierra'));
                                            actualizarItem(_res);
                                    }else{
                                            alert('error asdfdasf');
                                    }
                            }
                    });
                    //envía los datos para editar el ítem
                        
                    var estadoMasNuevo = obtenerEstadoProgreso(_idit);
                    //Solo guardar estado si fue cambiado
                    if (estadoMasNuevo !== null){
                        porcentajeProgreso = estadoMasNuevo.porcentaje_progreso;
                        nuevoProgreso = _this.querySelector('input[name="progresoNumber"]').value;
                        
                        fecha_propuesta = estadoMasNuevo.fecha_propuesta;
                        nuevaFechaPropuesta = _this.querySelector('input[name="fechaPropuesta"]').value;
                        
                        if ((porcentajeProgreso == nuevoProgreso) && (fecha_propuesta == nuevaFechaPropuesta)){
                            return;
                        }
                    }
                    
                    //Guardar Estado de la actividad
                    _parametros = {
                            "idMarco":_IdMarco,
                            'codMarco': _CodMarco,
                            "id": _this.querySelector('input[name="id"]').value,
                            "progresoNumber": _this.querySelector('input[name="progresoNumber"]').value,
                            "fechaPropuesta": _this.querySelector('input[name="fechaPropuesta"]').value
                    };
                    $.ajax({
                            url:   './app_plan/app_plan_cambiaritemestado.php',
                            type:  'post',
                            data: _parametros,
                            success:  function (response){
                                    var _res = $.parseJSON(response);
                                    console.log(_res);
                                    if(_res.res=='exito'){	
                                            cerrar(_this.querySelector('#botoncierra'));
                                            actualizarItem(_res);
                                            cargaBase('actualizarprogreso');
                                    }else{
                                            alert('error asdfdasf');
                                    }
                            }
                    });
		}
		
		function anadirItem(_ev,_idit){//ajustado a geogec
			_ev.stopPropagation();
			_parametros = {
				"idMarco":_IdMarco,
				'codMarco': _CodMarco,
				'idit': _idit
			};
			
			$.ajax({
				url:   './app_plan/app_plan_crearitem.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
					console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){	
						//cargaBase();
						cargaNuevoItem(_res);

					}
				}
			});	
		}
		
		function numerar(){
			_oitems=document.querySelectorAll('#contenidoextenso .item');
			_nivel=0;
			_Num=Array();
			
			_ultNiv=0;
			
			//define el nivel de anidamietno de cada item
			for(_ni in _oitems){				
				if(typeof _oitems[_ni] != 'object'){continue;}				
				_padre=_oitems[_ni].parentNode.parentNode;
				_nivelP=_padre.getAttribute('nivel');
				_saltos=1;
				while(_nivelP==undefined){
					_saltos++;
					_padre=_padre.parentNode.parentNode;
					_nivelP=_padre.getAttribute('nivel');
					if(_saltos>10){break;}
				}	
				_nivel= parseInt(_nivelP)+_saltos;				
				//alert(_nivel);
				_oitems[_ni].setAttribute('nivel',_nivel);
				delete _nivelP; 
			}
			
			for(_ni in _oitems){
				if(typeof _oitems[_ni] != 'object'){continue;}
												
				_nivel= _oitems[_ni].getAttribute('nivel');
				//alert(_nivel);
				if(_nivel>_ultNiv){_Num[_nivel]=0;};
				_ultNiv=_nivel;
				//if(_Num[_nivel]==undefined){_Num[_nivel]=0;}
				_Num[_nivel]++;
				_oitems[_ni].setAttribute('num',_Num[_nivel]);
				_nivstr='';
				_nivstr=_Num[_nivel];
				_nn=_nivel-1;
				while(_nn>0){
					_nivstr=_Num[_nn]+'.'+_nivstr;
					_nn--;
				}
				_oitems[_ni].querySelector('#num').innerHTML=_nivstr;
			}

		}
			
		function numerarB(){
			_cont=document.querySelector('#contenidoextenso > .hijos');
			_cc=0;
			_nivel=0;
			_Num=Array();
			_Num[_nivel]=0;
			
			explorarSubitem(_cont,_nivel);

		}
		
		function explorarSubitem(_cont,_denivel){
			_nivel=_denivel+1;
			_oitems=_cont.querySelectorAll('.item[nivel="'+_nivel+'"]');
			
			if(_Num[_nivel]==undefined){_Num[_nivel]=0;}
			console.log(_oitems);
			for(_ni in _oitems){
				
				if(typeof _oitems[_ni] != 'object'){continue;}
				_Num[_nivel]++;
				alert(_nivel+" : "+_Num[_nivel]);
				_oitems[_ni].setAttribute('num',_Num[_nivel]);
				
				_nivstr='';
				_nivstr=_Num[_nivel];
				_nn=_nivel-1;
				while(_nn>0){
					_nivstr=_Num[_nn]+'.'+_nivstr;
					_nn--;
				}
				_oitems[_ni].querySelector('#num').innerHTML=_nivstr;
				
				
				_hijos=_oitems[_ni].querySelector('.hijos');
				console.log(_nivel);
				alert('hola');
				if(_nivel==2){continue;}
				explorarSubitem(_hijos,_nivel);
				
			}
		}
	</script>
		
	<script type='text/javascript'>
		///funciones para gestionar drag y drop de archivos
			
		function dragFile(_event){
			//alert(_event.target.getAttribute('idit'));
			_event.stopPropagation();
    		_arr=Array();
			_arr={
				'id':_event.target.getAttribute('idfi'),
				'tipo':'archivo'
			};
			_arb = JSON.stringify(_arr);
    		_event.dataTransfer.setData("text", _arb);
		}
		
		function allowDropFile(_event,_this){
			_event.stopPropagation();
			//console.log(_this.parentNode.getAttribute('idit'));
			//console.log(_event.dataTransfer);
			if(_event.dataTransfer.items[0].kind=='file'){return;}
			if(_event.dataTransfer.getData("text")!=''){
				if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo'){
					return;
				}
			}
			
			limpiarAllowFile();
			_event.stopPropagation();
			_this.setAttribute('destinof','si');
			_event.preventDefault();
		}
		
		function limpiarAllowFile(){
			_dests=document.querySelectorAll('[destinof="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('destinof');
				}
			}
		}
                
		function dropFile(_event,_this){// ajustado a geogec
                    _event.stopPropagation();
                    _event.preventDefault();

                    if(_event.dataTransfer.getData("text")!=''){		
                            if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo'){
                                            return;
                                    }
                    }
    		
		    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
		    
		    _el=document.querySelector('.archivo[idfi="'+_DragData+'"]');
		    
		    //console.log(_DragData);
		   
		    if(_event.target.getAttribute('class')=='hijos'){	
		    	_tar=_event.target;
		    	 _idit=_this.parentNode.getAttribute('idit');
		    	_dest=_tar.parentNode.querySelector('.item[idit="'+_idit+'"] .documentos'); 
		    }else{
		    	 _idit=_this.getAttribute('idit');
		    	 _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idfi');
		    	_dest=document.querySelector('.item[idit="'+_idit+'"] .documentos');
		    }
		    
		    _dest.appendChild(_el);
		    		    			    
		    _parametros={
		    	"idMarco":_IdMarco,
		    	'codMarco': _CodMarco,
		    	"id":_DragData,
		    	"id_anidadoen":_idit
		    };
		    
	 		$.ajax({
				url:   './app_plan/app_plan_localizararchivo.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
						console.log(_res);
					if(_res.res=='exito'){	
						cargaBase();
					}else{
						alert('error asdfdsf');
					}
				}
			});
		    
		  }
	</script>
		
	<script type='text/javascript'>
		///funciones para gestjionar drag y drop de items
		
		function allowDrop(_event,_this){
			//console.log(_this.parentNode.getAttribute('idit'));
			
			console.log(_event.dataTransfer);
			
			if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='item'){
				return;
			}
			
			limpiarAllow();
			
			_event.stopPropagation();
			_this.setAttribute('destino','si');
			_event.preventDefault();
			
		}
		
		function limpiarAllow(){
			_dests=document.querySelectorAll('[destino="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('destino');
				}
			}
		}
		
		function resaltaHijos(_event,_this){
			//realta el div del item al que pertenese un título o una descripcion
			//_this.style.backgroundColor='lightblue';
			_dests=document.querySelectorAll('[destino="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('destino');
				}
			}
			_this.setAttribute('destino','si');
			_event.stopPropagation();
			
		}
		function desaltaHijos(_this){
			//realta el div del item al que pertenese un título o una descripcion
			//_this.style.backgroundColor='#fff';
			_this.removeAttribute('destino');
			_this.parentNode.removeAttribute('destino');
		}
		
		
		function dragcaja(_event){			
			//alert(_event.target.getAttribute('idit'));
			_arr=Array();
			_arr={
				'id':_event.target.getAttribute('idit'),
				'tipo':'item'
			};		
			_arb = JSON.stringify(_arr);

    		_event.dataTransfer.setData("text", _arb);
		}
		
		function bloquearhijos(_event,_this){			
			_idit=JSON.parse(_event.dataTransfer.getData("text")).id;
    		_negados = _this.querySelectorAll('.item[idit="'+_idit+'"] .hijos, .item[idit="'+_idit+'"] .medio');   
    		 		
    		for(_nn in _negados){
    			if(typeof _negados[_nn] == 'object'){
    				_negados[_nn].setAttribute('destino','negado');
    			}
    		}
		}
		
		function desbloquearhijos(_this){
    		_negados=document.querySelectorAll('[destino="negado"]');
    		for(_nn in _negados){
    			if(typeof _negados[_nn] == 'object'){
    				_negados[_nn].removeAttribute('destino');
    			}
    		}
		}	
		
			
		function drop(_event,_this){//ajustado a geogec	
                    _event.stopPropagation();
                    _this.removeAttribute('style');
                    _this.removeAttribute('destino');

                    _event.preventDefault();

                    if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){
    			dropFile(_event,_this);
				return;
			}
    		
		    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
		    console.log(_event.dataTransfer.getData("text"));
		    
		    _el=document.querySelector('.item[idit="'+_DragData+'"]');
		    _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idit');
		    _em=_el.previousSibling;
		        
		    _evitar='no';//evita destinos erronos por jerarquia.

		    
		    if(_event.target.getAttribute('class')=='medio'||_event.target.getAttribute('class')=='submedio'){
		    	
		    	if(_event.target.getAttribute('class')=='submedio'){
		    		_tar=_event.target.parentNode;
		    	}else{
		    		_tar=_event.target;
		    	}
		    	
		    	_dest=_tar.parentNode; 
			    _dest.insertBefore(_el,_tar);			    
			    _dest.insertBefore(_em,_el);
			    
		    }else if(_event.target.getAttribute('class')=='hijos'){
		    	
		    _dest=_event.target;

			   _dest.appendChild(_el);
			   _dest.insertBefore(_em,_el);
		    	
		    	
		    }else{
		    	alert('destino inesperado');
		    	
		    	return;
		    	
		    }
		  
		    _niv=_el.parentNode.parentNode.getAttribute('nivel');
		    _niv++;
		    _el.setAttribute('nivel',_niv.toString());
		    		    
		    _NuevoIdIt=_dest.parentNode.getAttribute('idit');
		    
		    _enviejo=document.querySelectorAll('[idit="'+_ViejoIdIt+'"] > .hijos > .item');
		    _serieviejo='';
		    for(_ni in _enviejo){
		    	if(typeof _enviejo[_ni]=='object'){
		    		_serieviejo+=_enviejo[_ni].getAttribute('idit')+',';
		    	}
		    }
		    
		    console.log(_NuevoIdIt);
		    _ennuevo=document.querySelectorAll('[idit="'+_NuevoIdIt+'"] > .hijos > .item');
		    _serienuevo='';
		    for(_ni in _ennuevo){
		    	console.log(_ennuevo[_ni]);
		    	if(typeof _ennuevo[_ni]=='object'){
		    		_serienuevo+=_ennuevo[_ni].getAttribute('idit')+',';
		    	}
		    }
		   
		    _parametros={
		    	"idMarco":_IdMarco,
		    	"codMarco":_CodMarco,
		    	"id":_DragData,
		    	"id_anidado":_NuevoIdIt,
		    	"viejoAnidado":_ViejoIdIt,
		    	"viejoAserie":_serieviejo,
		    	"nuevoAnidado":_NuevoIdIt,
		    	"nuevoAserie":_serienuevo
		    };
		    
	 		$.ajax({
				url:   './app_plan/app_plan_anidaritem.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
						console.log(_res);
					if(_res.res=='exito'){	
						//cargaBase();
						nivelar();
						numerar();
					}else{
						alert('error asfffgh');
					}
				}
			});
			//envía los datos para editar el ítem
                        cargaBase('actualizarprogreso');
		}
		
	</script>
        
	<script type='text/javascript'>
		function toogle(_elem){
		    _nombre=_elem.parentNode.parentNode.getAttribute('class');
	
		    elementos = document.getElementsByName(_nombre);
		    for (x=0;x<elementos.length;x++){			
				elementos[x].removeAttribute('checked');
			}
		    _elem.previousSibling.setAttribute('checked','checked');		
		}
	</script>

</body>