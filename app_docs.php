<?php 
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gestión de documento
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
                <img src='./img/app_docs_hd.png' style='float:left;'>
                <h2 id='titulo'>Gestor de documentos de referencia</h2>
                <div id='descripcion'>espacio para cargar y gestionar documentos de trabajo de cada proyecto de investigación</div>
            </div>	
        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de proyecto -</h1>
            <p id='descripcion'>- descripcion de proyecto -</p>
        </div>	
        <div id='portamapa'>
            <div id='titulomapa'>
                <p id='tnombre'></p>
                <h1 id='tnombre_humano'></h1>
                <p id='tdescripcion'></p>
                <b><p id='tseleccion'></p></b>
            </div>
        </div>
        <div id='modelos'>
            <div class='item'
                 idit='nn'
                 draggable="true"
                 ondragstart="dragcaja(event);bloquearhijos(event,this);"
                 ondragleave="limpiarAllowFile()"
                 ondragover="allowDropFile(event,this)"
                 ondrop='dropFile(event,this)'>
                <h3 onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>nombre de la caja</h3>
                <p onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>descipcion del contenido de la caja</p>
                <div class='documentos'>
                </div>
                <div class='hijos'
                     ondrop="drop(event,this)"
                     ondragover="allowDrop(event,this)"
                     ondragleave="limpiarAllow()">
                </div>
            </div>
        </div>

        <div id="archivos">
        	<div id='menuacciones'>
				<div id='lista'></div>	
			</div>
            <form action='' enctype='multipart/form-data' method='post' id='uploader' ondragover='resDrFile(event)' ondragleave='desDrFile(event)'>
                <div id='contenedorlienzo'>									
                    <div id='upload'>
                        <label>Arrastre todos los archivos aquí.</label>
                        <input multiple='' id='uploadinput' type='file' name='upload' value='' onchange='cargarCmp(this);'>
                    </div>
                </div>
                <div id='contenedorlienzo'>									
                    <div id='upload'>
                        <label>O cree un link aquí.</label>
                        <a id='uploadinputlink' name='uploadlink' onclick='formcrearlink(event,this)'>O cree un link aquí.</a>
                    </div>
                </div>
            </form>
            <div id="listadosubiendo">
                <label>archivos subiendo...</label>
            </div>
            <div id="listadoaordenar">
                <label>archivos subidos.</label>
            </div>

            <div id="eliminar"
                 ondragover="allowDropFile(event,this)"
                 ondragleave="limpiarAllowFile()"
                 ondrop='dropTacho(event,this)'>
                <br>X
                <span>tacho de basura</span>
            </div>		
            
            <a id='botonanadir' onclick='anadirItem("0")'>+ <br><span>nueva <br> caja</span></a>
        </div>	

        <div id="contenidoextenso" idit='0'>
            
            <div class='hijos'
                 nivel="0"
                 ondrop="drop(event,this)" 
                 ondragover="allowDrop(event,this);resaltaHijos(event,this)" 
                 ondragleave="desaltaHijos(this)">
            </div>
        </div>
    </div>	
</div>


<form id="editordoc" onsubmit="guardarD(event,this)">
    <input name='id' type='hidden'>
    <label id='nombre'>--</label>
    <textarea name='descripcion'></textarea>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a style='display:none' id='botonelimina' onclick='eliminarD(event,this)'>eliminar</a>
</form>

<form id="editoritem" onsubmit="guardarI(event,this)">
    <label>Nombre de la caja</label>
    <input name='nombre'>
    <input name='id' type='hidden'>
    <label>Descripcion del contenido de la caja</label>
    <textarea name='descripcion'></textarea>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a id='botonelimina' onclick='eliminarI(event,this)'>eliminar</a>
    <a id='botonanadir' onclick="anadirItem(this.parentNode.querySelector('input[name=\'id\']').value)">+ <br><span>nueva <br> caja</span></a>
</form>

<form id="formcrearlink" onsubmit="cargarCmpLink(event,this)">
    <label>Ingresar Link</label>
    <label>Nombre del Link</label>
    <input name='linkName' type='text'>
    <label>URL</label>
    <input name='linkUrl' type='url'>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a id='botonelimina' onclick='eliminarLink(event,this)'>eliminar</a>
</form>

<form id="editarlink" onsubmit="guardarLink(event,this)">
    <input name='id' type='hidden'>
    <label id='nombre'>--</label>
    <label id='linkUrl'>URL</label>
    <textarea name='descripcion'></textarea>
    <a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
    <input type='submit' value='guardar'>
    <a style='display:none' id='botonelimina' onclick='eliminarLink(event,this)'>eliminar</a>
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
    var _Docs=Array();
    var _DocLinks=Array();
    var _Orden=Array();

    function cargaBase(){

            document.querySelector('#contenidoextenso > .hijos').innerHTML='';			
            document.querySelector('#listadosubiendo').innerHTML='<label>archivos subiendo...</label>';
            document.querySelector('#listadoaordenar').innerHTML='<label>archivos subidos.</label>';

            _parametros = {
                    'idMarco': _IdMarco,
                    'codMarco': _CodMarco
            };

            $.ajax({
                    data: _parametros,
                    url:   './app_docs/app_docs_consulta.php',
                    type:  'post',
                    success:  function (response){
                            var _res = $.parseJSON(response);
                            console.log(_res);
                            for(_nm in _res.mg){
                                    alert(_res.mg[_nm]);
                            }
                            if(_res.res=='exito'){		
                                    _Items=_res.data.psdir;
                                    _Orden=_res.data.orden;

                                    generarItemsHTML();		
                                    generarArchivosHTML();
                                    generarArchivoLinksHTML();
                            }else{
                                    alert('error dsfg');
                            }
                    }
            });
    }

    cargaBase();

    function generarArchivosHTML(){

            if(Object.keys(_Items[0].archivos).length>0){
                    for(_na in _Items[0].archivos){
                            _dat=_Items[0].archivos[_na];
                            _Docs[_dat.id]=_dat;
                            console.log(_dat);
                            _aaa=document.createElement('a');
                            _aaa.innerHTML=_dat.nombre;
                            _aaa.setAttribute('href',_dat.archivo);
                            _aaa.setAttribute('download',_dat.nombre);
                            _aaa.setAttribute('draggable',"true");
                            _aaa.setAttribute('ondragstart',"dragFile(event)");
                            _aaa.setAttribute('idfi',_dat.id);
                            _aaa.setAttribute('class','archivo');					
                            document.getElementById('listadoaordenar').appendChild(_aaa);
                            _aasub=document.createElement('a');
                            _aasub.innerHTML='.!.';
                            _aasub.setAttribute('onclick','editarD(event,this)');
                            _aaa.appendChild(_aasub);
                    }			
            }
    }
    
    function generarArchivoLinksHTML(){

            if(Object.keys(_Items[0].archivolinks).length>0){
                    for(_na in _Items[0].archivolinks){
                            _dat=_Items[0].archivolinks[_na];
                            _DocLinks[_dat.id]=_dat;
                            console.log(_dat);
                            _aaa=document.createElement('a');
                            _aaa.innerHTML=_dat.nombre;
                            _aaa.setAttribute('href',_dat.url);
                            _aaa.setAttribute('target','_blank');
                            _aaa.setAttribute('download',_dat.nombre);
                            _aaa.setAttribute('draggable',"true");
                            _aaa.setAttribute('ondragstart',"dragLinkurl(event)");
                            _aaa.setAttribute('idfi',_dat.id);
                            _aaa.setAttribute('class','archivo');					
                            document.getElementById('listadoaordenar').appendChild(_aaa);
                            _aasub=document.createElement('a');
                            _aasub.innerHTML='.!.';
                            _aasub.setAttribute('onclick','editarLink(event,this)');
                            _aaa.appendChild(_aasub);
                    }			
            }
    }

    function generarItemsHTML(){
            //genera un elemento html por cada instancia en el array _Items
            for(_nO in _Orden.psdir){

                    _ni=_Orden.psdir[_nO];

                    _dat=_Items[_ni];
                    _clon=document.querySelector('#modelos .item').cloneNode(true);

                    _clon.setAttribute('idit',_dat.id);

                    if(_dat.nombre==null){_dat.nombre='- caja sin nombre -';}

                    _clon.querySelector('h3').innerHTML=_dat.nombre;
                    if(_dat.descripcion==null){_dat.descripcion='- caja sin descripción -';}
                    _clon.querySelector('p').innerHTML=_dat.descripcion;
                    _clon.setAttribute('nivel',"1");

                    for(_na in _dat['archivos']){
                            _dar=_dat['archivos'][_na];
                            _Docs[_dar.id]=_dar;
                            _aa=document.createElement('a');

                            _aa.innerHTML=_dar.nombre;
                            _aa.setAttribute('href',_dar.archivo);
                            _aa.setAttribute('download',_dar.nombre);
                            _aa.setAttribute('draggable',"true");
                            _aa.setAttribute('ondragstart',"dragFile(event)");
                            _aa.setAttribute('idfi',_dar.id);
                            _aa.setAttribute('class','archivo');
                            _clon.querySelector('.documentos').appendChild(_aa);
                            _aasub=document.createElement('a');
                            _aasub.innerHTML='.!.';
                            _aasub.setAttribute('onclick','editarD(event,this)');
                            _aa.appendChild(_aasub);
                    }
                    
                    for(_na in _dat['archivolinks']){
                            _dar=_dat['archivolinks'][_na];
                            _DocLinks[_dar.id]=_dar;
                            _aa=document.createElement('a');

                            _aa.innerHTML=_dar.nombre;
                            _aa.setAttribute('href',_dar.url);
                            _aa.setAttribute('target','_blank');
                            _aa.setAttribute('download',_dar.nombre);
                            _aa.setAttribute('draggable',"true");
                            _aa.setAttribute('ondragstart',"dragLinkurl(event)");
                            _aa.setAttribute('idfi',_dar.id);
                            _aa.setAttribute('class','archivo');
                            _clon.querySelector('.documentos').appendChild(_aa);
                            _aasub=document.createElement('a');
                            _aasub.innerHTML='.!.';
                            _aasub.setAttribute('onclick','editarLink(event,this)');
                            _aa.appendChild(_aasub);
                    }

                    document.querySelector('#contenidoextenso > .hijos').appendChild(_clon);
            }

            //anida los itmes genereados unos dentro de otros
            for(_nO in _Orden.psdir){
                _ni=_Orden.psdir[_nO];
                _el=document.querySelector('#contenidoextenso > .hijos > .item[idit="'+_Items[_ni].id+'"]');
				
                if(_Items[_ni].id_p_ref_02_pseudocarpetas!='0'){
                    //alert(_Items[_ni].id_p_ESPitems_anidado);
                    _dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_Items[_ni].id_p_ref_02_pseudocarpetas+'"] > .hijos');
                    _niv=_dest.parentNode.getAttribute('nivel');
                    _niv++;
                    _el.setAttribute('nivel',_niv);
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
    }
</script>
	
		
<script type='text/javascript'>
    ///funciones para editar documentos	
    function editarD(_event,_this){				
            //abre el formulario para edittar item
            _event.preventDefault();
            _event.stopPropagation();
            _iddoc=_this.parentNode.getAttribute('idfi');
            _form=document.querySelector('#editordoc');
            _form.style.display='block';
            _form.querySelector('#nombre').innerHTML=_Docs[_iddoc].nombre;
            _form.querySelector('input[name="id"]').value=_Docs[_iddoc].id;
            _form.querySelector('[name="descripcion"]').value=_Docs[_iddoc].descripcion;
    }
    
    function guardarD(_event,_this){// ajustado geogec
            _event.preventDefault();
            console.log(_this);
            var _this=_this;
            _parametros = {
                    "idMarco":_IdMarco,
                    'codMarco': _CodMarco,
                    "id": _this.querySelector('input[name="id"]').value,
                    "descripcion": _this.querySelector('[name="descripcion"]').value
            };
            $.ajax({
                    url:   './app_docs/app_docs_cambiardoc.php',
                    type:  'post',
                    data: _parametros,
                    success:  function (response){
                            var _res = $.parseJSON(response);
                                    console.log(_res);
                            if(_res.res=='exito'){	
                                    cerrar(_this.querySelector('#botoncierra'));
                                    cargaBase();
                            }else{
                                    alert('error asdfdasf');
                            }
                    }
            });
            //envía los datos para editar el ítem
    }
</script>

<script type='text/javascript'>
    ///funciones para editar links	
    function editarLink(_event,_this){				
            //abre el formulario para editar link
            _event.preventDefault();
            _event.stopPropagation();
            _iddoc=_this.parentNode.getAttribute('idfi');
            _form=document.querySelector('#editarlink');
            _form.style.display='block';
            _form.querySelector('#nombre').innerHTML=_DocLinks[_iddoc].nombre;
            _form.querySelector('#linkUrl').innerHTML=_DocLinks[_iddoc].url;
            _form.querySelector('input[name="id"]').value=_DocLinks[_iddoc].id;
            _form.querySelector('[name="descripcion"]').value=_DocLinks[_iddoc].descripcion;
    }
    
    function guardarLink(_event,_this){
            _event.preventDefault();
            console.log(_this);
            var _this=_this;
            _parametros = {
                    "idMarco":_IdMarco,
                    'codMarco': _CodMarco,
                    "id": _this.querySelector('input[name="id"]').value,
                    "descripcion": _this.querySelector('[name="descripcion"]').value
            };
            $.ajax({
                    url:   './app_docs/app_docs_cambiarlink.php',
                    type:  'post',
                    data: _parametros,
                    success:  function (response){
                            var _res = $.parseJSON(response);
                                    console.log(_res);
                            if(_res.res=='exito'){	
                                    cerrar(_this.querySelector('#botoncierra'));
                                    cargaBase();
                            }else{
                                    alert('error asdfdasfguardarLink');
                            }
                    }
            });
            //envía los datos para editar el ítem
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
            //abre el formulario para edittar item
            _idit=_this.parentNode.getAttribute('idit');
            _form=document.querySelector('#editoritem');
            _form.style.display='block';
            _form.querySelector('input[name="nombre"]').value=_Items[_idit].nombre;
            _form.querySelector('input[name="id"]').value=_Items[_idit].id;
            _form.querySelector('[name="descripcion"]').value=_Items[_idit].descripcion;
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
                            url:   './app_docs/app_docs_borraritem.php',
                            type:  'post',
                            data: _parametros,
                            success:  function (response){
                                    var _res = $.parseJSON(response);
                                            console.log(_res);
                                    if(_res.res=='exito'){	
                                            cerrar(_this);
                                            cargaBase();
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
            _parametros = {
                    "idMarco":_IdMarco,
                    'codMarco': _CodMarco,
                    "id": _this.querySelector('input[name="id"]').value,
                    "nombre": _this.querySelector('input[name="nombre"]').value,
                    "descripcion": _this.querySelector('[name="descripcion"]').value
            };
            $.ajax({
                    url:   './app_docs/app_docs_cambiaritem.php',
                    type:  'post',
                    data: _parametros,
                    success:  function (response){
                            var _res = $.parseJSON(response);
                                    console.log(_res);
                            if(_res.res=='exito'){	
                                    cerrar(_this.querySelector('#botoncierra'));
                                    cargaBase();
                            }else{
                                    alert('error asdfdasf');
                            }
                    }
            });
            //envía los datos para editar el ítem

    }

    function anadirItem(_iditempadre){//ajustado a geogec
            _parametros = {
                    "idMarco":_IdMarco,
                    'codMarco': _CodMarco,
                    'iditempadre':_iditempadre
            };

            $.ajax({
                    url:   './app_docs/app_docs_crearitem.php',
                    type:  'post',
                    data: _parametros,
                    success:  function (response){
                            var _res = $.parseJSON(response);
                            console.log(_res);
                            for(_nm in _res.mg){
                                    alert(_res.mg[_nm]);
                            }
                            if(_res.res=='exito'){
                            	if(_iditempadre>0){
                            		cerrar(document.querySelector('#editoritem #botoncierra'));
                        		}	
                                cargaBase();
                            }
                    }
            });
    }
</script>
	
<script type='text/javascript'>
        ///funciones para gestionar drop en el tacho
        function dropTacho(_event,_this){//ajustado geogec

                _event.stopPropagation();
        _event.preventDefault();    		

        limpiarAllowFile();

        if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){

                if(confirm('¿Confirma que quiere eliminar el archivo del panel?')==true){

                        _parametros={
                                "idfi":JSON.parse(_event.dataTransfer.getData("text")).id,
                                "tipo":JSON.parse(_event.dataTransfer.getData("text")).tipo,
                                "idMarco":_IdMarco,
                                'codMarco': _CodMarco,
                                "accion":'borrar'
                            };

                                $.ajax({
                                        url:   './app_docs/app_docs_borrararchivo.php',
                                        type:  'post',
                                        data: _parametros,
                                        success:  function (response){
                                                var _res = $.parseJSON(response);
                                                        console.log(_res);
                                                if(_res.res=='exito'){	
                                                        cargaBase();
                                                }else{
                                                        alert('error asffsvrrfgh');
                                                }
                                        }
                                });

                }
                return;

        }else if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='linkurl'){

                if(confirm('¿Confirma que quiere eliminar el link del panel?')==true){

                        _parametros={
                                "idfi":JSON.parse(_event.dataTransfer.getData("text")).id,
                                "tipo":JSON.parse(_event.dataTransfer.getData("text")).tipo,
                                "idMarco":_IdMarco,
                                'codMarco': _CodMarco,
                                "accion":'borrar'
                            };

                                $.ajax({
                                        url:   './app_docs/app_docs_borrarlink.php',
                                        type:  'post',
                                        data: _parametros,
                                        success:  function (response){
                                                var _res = $.parseJSON(response);
                                                        console.log(_res);
                                                if(_res.res=='exito'){	
                                                        cargaBase();
                                                }else{
                                                        alert('error asffsvrrfgh');
                                                }
                                        }
                                });

                }
                return;

        }else if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='item'){

                if(confirm('¿Confirma que quiere eliminar el Item y todo su contenido?')==true){



                }
                return;

        }

            var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
            console.log(_DragData);
            _el=document.querySelector('.archivo[idfi="'+_DragData+'"]');

            _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idfi');
            _em=_el.nextSibling;
            _idit=_this.getAttribute('idit');
            _ref=document.querySelector('.item[idit="'+_idit+'"] .documentos');
            _ref.appendChild(_el);

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
		console.log("v");
		console.log(typeof _event.dataTransfer.getData("text"));
		console.log("z");
		
                if(_event.dataTransfer.getData("text")
                    && JSON.parse(_event.dataTransfer.getData("text")).tipo
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo') 
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='linkurl')){
                        return;
                }

		console.log("y");
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

                if(_event.dataTransfer.getData("text")
                    && JSON.parse(_event.dataTransfer.getData("text")).tipo
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo')
                    && (JSON.parse(_event.dataTransfer.getData("text")).tipo!='linkurl')){
                    return;
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

                if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){
                    localizarArchivo(_parametros);
                } else if (JSON.parse(_event.dataTransfer.getData("text")).tipo=='linkurl'){
                    localizarLinkurl(_parametros);
                }
          }
          
          function localizarArchivo(_parametros){
                $.ajax({
                        url:   './app_docs/app_docs_localizararchivo.php',
                        type:  'post',
                        data: _parametros,
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                        console.log(_res);
                                if(_res.res=='exito'){	
                                        cargaBase();
                                }else{
                                        alert('error asdfydsf');
                                }
                        }
                });
          }
          
          function localizarLinkurl(_parametros){
                $.ajax({
                        url:   './app_docs/app_docs_localizarlink.php',
                        type:  'post',
                        data: _parametros,
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                        console.log(_res);
                                if(_res.res=='exito'){	
                                        cargaBase();
                                }else{
                                        alert('error asdrfdsf');
                                }
                        }
                });
          }
</script>

<script type='text/javascript'>
        ///funciones para gestionar drag y drop de links

        function dragLinkurl(_event){
                //alert(_event.target.getAttribute('idit'));
                _event.stopPropagation();
                _arr=Array();
                _arr={
                        'id':_event.target.getAttribute('idfi'),
                        'tipo':'linkurl'
                    };
                _arb = JSON.stringify(_arr);
                _event.dataTransfer.setData("text", _arb);
        }
        
        
</script>

<script type='text/javascript'>
        ///funciones para gestjionar drag y drop de items

        function allowDrop(_event,_this){
                //console.log(_this.parentNode.getAttribute('idit'));

                console.log(_event.dataTransfer);

		if(_event.dataTransfer.getData("text")!=''){
			if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='item'){
				return;
			}
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

            if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='linkurl'){
                dropFile(_event,_this);
                return;
            }

            var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
            console.log('u');
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

            _niv=_dest.parentNode.getAttribute('nivel');
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
                        url:   './app_docs/app_docs_anidaritem.php',
                        type:  'post',
                        data: _parametros,
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                        console.log(_res);
                                if(_res.res=='exito'){	
                                        cargaBase();
                                }else{
                                        alert('error asfffgh');
                                }
                        }
                });
                //envía los datos para editar el ítem
        }
</script>

<script type='text/javascript'>
///funciones para guardar archivos

        function resDrFile(_event){
                //console.log(_event);
                document.querySelector('#archivos #contenedorlienzo').style.backgroundColor='lightblue';
        }	

        function desDrFile(_event){
                //console.log(_event);
                document.querySelector('#archivos #contenedorlienzo').removeAttribute('style');
        }

        var _nFile=0;
        var _nLink=0;

        var xhr=Array();
        var inter=Array();
        function cargarCmp(_this){

            var files = _this.files;

            for (i = 0; i < files.length; i++) {
                _nFile++;
                console.log(files[i]);
                var parametros = new FormData();
                parametros.append('upload',files[i]);
                parametros.append('nfile',_nFile);
                parametros.append('idMarco',_IdMarco);
                parametros.append('codMarco',_CodMarco);

                var _nombre=files[i].name;
                _upF=document.createElement('a');
                _upF.setAttribute('nf',_nFile);
                _upF.setAttribute('class',"archivo");
                _upF.setAttribute('size',Math.round(files[i].size/1000));
                _upF.innerHTML=files[i].name;
                document.querySelector('#listadosubiendo').appendChild(_upF);

                _nn=_nFile;
                xhr[_nn] = new XMLHttpRequest();
                xhr[_nn].open('POST', './app_docs/app_docs_guardaarchivo.php', true);
                xhr[_nn].upload.li=_upF;
                xhr[_nn].upload.addEventListener("progress", updateProgress, false);


                xhr[_nn].onreadystatechange = function(evt){
                    //console.log(evt);

                    if(evt.explicitOriginalTarget.readyState==4){
                        var _res = $.parseJSON(evt.explicitOriginalTarget.response);
                        //console.log(_res);

                        alert('terminó '+_res.data.nf);

                        if(_res.res=='exito'){							
                            _file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');								
                            document.querySelector('#listadoaordenar').appendChild(_file);
                            _file.setAttribute('href',_res.data.ruta);
                            _file.setAttribute('download',_file.innerHTML);
                            _file.setAttribute('draggable',"true");
                            _file.setAttribute('ondragstart',"dragFile(event)");
                            _file.setAttribute('idfi',_res.data.nid);
                            _aasub=document.createElement('a');
                            _aasub.innerHTML='.!.';
                            _aasub.setAttribute('onclick','editarD(event,this)');
                            _file.appendChild(_aasub);
                        } else {
                            _file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');
                            _file.innerHTML+=' ERROR';
                            _file.style.color='red';
                        }
                        //cargaTodo();
                        //limpiarcargando(_nombre);
                    }
                };
                xhr[_nn].send(parametros);
            }			
        }

        function formcrearlink(_event,_this){				
            //abre el formulario para cargar link
            _event.preventDefault();
            _event.stopPropagation();
            _form=document.querySelector('#formcrearlink');
            _form.style.display='block';
            _form.querySelector('input[name="linkName"]').value=null;
            _form.querySelector('input[name="linkUrl"]').value=null;
        }

        function cargarCmpLink(_event,_this){
            //Guardar Link url en la BD
            
            _event.preventDefault();
            console.log(_this);
            var _this=_this;
            
            var linkName = _this.querySelector('input[name="linkName"]').value;
            var urlLink = _this.querySelector('input[name="linkUrl"]').value;

            _nLink++;
            
            console.log(urlLink);
            var parametros = new FormData();
            parametros.append('urlLink',urlLink);
            parametros.append('linkName',linkName);
            parametros.append('nlink',_nLink);
            parametros.append('idMarco',_IdMarco);
            parametros.append('codMarco',_CodMarco);

            _upF=document.createElement('a');
            _upF.setAttribute('nf',_nLink);
            _upF.setAttribute('class',"archivo");
            _upF.innerHTML=linkName;
            document.querySelector('#listadosubiendo').appendChild(_upF);

            _nn=_nLink;
            xhr[_nn] = new XMLHttpRequest();
            xhr[_nn].open('POST', './app_docs/app_docs_guardalink.php', true);
            xhr[_nn].upload.li=_upF;
            xhr[_nn].upload.addEventListener("progress", updateProgress, false);

            xhr[_nn].onreadystatechange = function(evt){
                //console.log(evt);

                if(evt.explicitOriginalTarget.readyState==4){
                    var _res = $.parseJSON(evt.explicitOriginalTarget.response);
                    //console.log(_res);

                    if(_res.res=='exito'){							
                        _link=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');								
                        document.querySelector('#listadoaordenar').appendChild(_link);
                        _link.setAttribute('href',_res.data.ruta);
                        _link.setAttribute('target','_blank');
                        _link.setAttribute('download',_link.innerHTML);
                        _link.setAttribute('draggable',"true");
                        _link.setAttribute('ondragstart',"dragLinkurl(event)");
                        _link.setAttribute('idfi',_res.data.nid);
                        _aasub=document.createElement('a');
                        _aasub.innerHTML='.!.';
                        _aasub.setAttribute('onclick','editarLink(event,this)');
                        _link.appendChild(_aasub);
                    } else {
                        _link=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');
                        _link.innerHTML+=' ERROR';
                        _link.style.color='red';
                        alert('error cargarCmpLink');
                    }
                }
                
                cerrar(_this.querySelector('#botoncierra'));
            };
            xhr[_nn].send(parametros);
        }

        function updateProgress(evt) {
            if (evt.lengthComputable) {
                var percentComplete = 100 * evt.loaded / evt.total;		   
                this.li.style.width="calc("+Math.round(percentComplete)+"% - ("+Math.round(percentComplete)/100+" * 6px))";
            } else {
                // Unable to compute progress information since the total size is unknown
            } 
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