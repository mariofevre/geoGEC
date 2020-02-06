/**
*
* funciones de operacion de la pagina 
*  
* @package    	geoGEC
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
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
*/

function accionCreaCancelar(_this){
    limpiarFormularioCampas();
    limpiarFormularioCapa();
    
    document.getElementById('formEditarCampa').style.display='none';
    document.getElementById('formSeleccionCampa').style.display='none';
    document.getElementById('divListaCampaCarga').style.display='none';
    document.querySelector('#divListaCampaCarga #botonSeleccionarCampaCambio').removeAttribute('idcampa');
    document.getElementById('divMenuAccionesCrea').style.display='none';
    document.getElementById('botonCrearCampa').style.display='block';
    
    accionCancelarSeleccionCapa(_this);
}


function editarCampa(){
	
	document.querySelector('#carga.formCargaCampaCuerpo #campaNombre').value=_DataRele.nombre;
	document.querySelector('#carga.formCargaCampaCuerpo #campaDescripcion').value=_DataRele.descripcion;
	
	document.querySelector('#carga.formCargaCampaCuerpo').style.display='block';
}


function accionCreaEliminar(_this){
    //console.log('eliminarCandidatoIndicador()');

    if(confirm("�Confirma que quiere eliminar este candidato a versi�n? \n Si lo hace se guardar� registro en la papelera de los datos cargados en el formulario.")){
        //console.log('o');
        eliminarCandidatoIndicador(_this);
    }
}


function activacargarGeometrias(){
	
	document.querySelector('#cargarGeometrias').style.display='block';
	
}


function accionCreaGuardar(){

	_form=document.querySelector('#formEditarCampa');
    if (_form.querySelector('#campaNombre').value != ''){
        editarCampoInd('campaNombre');
    }
    if (_form.querySelector('#campaDescripcion').value != ''){
        editarCampoInd('campaDescripcion');
    }
    
    
    var idcapa = document.getElementById('capaseleccionada').getAttribute('idcapa');
    if (idcapa != '' && idcapa > 0){
        editarCampoIndParametros('idcapa', idcapa);
    }
    
    var divColumnasValores = document.getElementById('columnasValores');
    for (var child in divColumnasValores.children){
         if (divColumnasValores.children[child].nodeType == 1 &&
                divColumnasValores.children[child].getAttribute('id') == 'nuevacolumna'){
           guardarColumna(divColumnasValores.children[child]);
        }
    }

    editarRepresentacionCampo();
    editarRepresentacionValorMaximo();
    editarRepresentacionValorMinimo();
}

function accionCreaPublicar(_this){
    accionCreaGuardar();
    
    if (confirm('Esta seguro que desea publicar este indicador? \nUna vez publicado no se pueden hacer cambios')) {
        publicarIndicador();
    }
}

function accionCargaCancelar(_this){
    limpiarFormularioIndicadores();
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    limpiarFormularioIndPublicados();
    
    document.getElementById('formEditarCampa').style.display='none';
    document.getElementById('formSeleccionCampa').style.display='none';
    document.getElementById('divListaCampaCarga').style.display='none';
    document.querySelector('#divListaCAmpaCarga #botonSeleccionarCampaCambio').removeAttribute('idind');
    //document.getElementById('divMenuAccionesCarga').style.display='none';
    document.getElementById('botonCrearIndicador').style.display='block';
    
   _source_ind.clear();
   _source_ind_buffer.clear();
   _source_ind_superp.clear();
    accionCargarCampar();
    
    
    //accionCancelarSeleccionCapa(_this);
}

function accionCrearCampa(_this){
	alert('Funci�n en desarrollo');
	return;
    generarNuevaCampa();
    document.getElementById('formEditarCampa').style.display='inline-block';
    document.getElementById('divMenuAccionesCrea').style.display='inline-block';
    document.getElementById('botonCrearCampa').style.display='none';
    document.getElementById('divSeleccionCampaCuerpo').style.display='none';   
    document.getElementById('divListaCampaCarga').style.display='none';   
}

function accionCargarCampa(){
	
    limpiarFormularioCampa();
    
    
    document.getElementById('formSeleccionInd').style.display='inline-block';
    //document.getElementById('divMenuAccionesCarga').style.display='inline-block';
    document.getElementById('botonCrearIndicador').style.display='block';
}
//accionCargarCampa();

cargarListadoCampa();
    
    
function accionModificarIndicador(_this){
    if(confirm("Editar Indicadores ya publicados puede causar errores en el sistema. \n Esta seguro que desea continuar con esta operacion?")){
        limpiarFormularioIndPublicados();
        cargarListadoIndicadoresPublicadosAModificar();

        document.getElementById('formSeleccionInd').style.display='inline-block';
        document.getElementById('divMenuAccionesCrea').style.display='inline-block';
        document.getElementById('botonCrearIndicador').style.display='none';
        
    }
}

function accionSeleccionarCapa(_this){
    cargarListadoCapasPublicadas();
    
    document.getElementById('formSeleccionCapa').style.display='block';
    //document.getElementById('AccionesSeleccionCapa').style.display='none';
    document.getElementById('botonSeleccionarCapa').style.display='none';
    document.getElementById('botonCancelarSeleccionarCapa').style.display='block';
}

function accionCancelarSeleccionCapa(_this){
    limpiarFormularioSeleccionCapa();
    
    document.getElementById('formSeleccionCapa').style.display='none';
    //document.getElementById('AccionesSeleccionCapa').style.display='block';
    document.getElementById('botonSeleccionarCapa').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapa').style.display='none';
    
    //limpiarFormularioCapa();
    //document.getElementById('capaseleccionada').style.display='none';
}

function accionSeleccionarCapaCambio(_this){
    cargarListadoCapasPublicadas();
    
    document.getElementById('formSeleccionCapa').style.display='block';
    //document.getElementById('AccionesSeleccionCapaCambio').style.display='none';
    document.getElementById('botonSeleccionarCapaCambio').style.display='none';
    document.getElementById('botonCancelarSeleccionarCapaCambio').style.display='block';
}

function accionCancelarSeleccionCapaCambio(_this){
    limpiarFormularioSeleccionCapa();
    
    document.getElementById('formSeleccionCapa').style.display='none';
    //document.getElementById('AccionesSeleccionCapaCambio').style.display='block';
    document.getElementById('botonSeleccionarCapaCambio').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapaCambio').style.display='none';
    
    //limpiarFormularioCapa();
    //document.getElementById('capaseleccionada').style.display='none';
}

function cargarValoresCapasPublicadas(_res){
    if (_res.data != null){
        for (var elemCapa in _res.data){
            var divRoot = document.getElementById('listacapaspublicadas');
            var filaCapa = document.createElement('a');
            filaCapa.setAttribute('idcapa', _res.data[elemCapa]["id"]);
            filaCapa.setAttribute('class', 'filaCapaLista');
            filaCapa.setAttribute('onclick', "accionCapaPublicadaSeleccionada(this,"+_res.data[elemCapa]["id"]+")" );
            var capaId = document.createElement('div');
            capaId.setAttribute('id','capaIdLista');
            capaId.innerHTML = "ID <span class='idn'>" + _res.data[elemCapa]["id"]+"</span>";
            var capaNombre = document.createElement('div');
            capaNombre.setAttribute('id','capaNombreLista');
            capaNombre.innerHTML = _res.data[elemCapa]["nombre"];
            var capaDescripcion = document.createElement('div');
            capaDescripcion.setAttribute('id','capaDescripcionLista');
            capaDescripcion.innerHTML = _res.data[elemCapa]["descripcion"];
            filaCapa.appendChild(capaId);
            filaCapa.appendChild(capaNombre);
            filaCapa.appendChild(capaDescripcion);
            divRoot.appendChild(filaCapa);
        }
    } else {
        console.log('no hay capas publicadas para este usuario');
    }   
}

function mostrarListadoCapasPublicadas(){
    document.querySelector('#divSeleccionCapaCuerpo #txningunacapa').style.display='none';
    document.getElementById('divSeleccionCapaCuerpo').style.display='block';
}

function accionCapaPublicadaSeleccionada(_this, idcapa){
    editarCampoIndParametros('idcapa', idcapa);
    accionCapaPublicadaCargar(_this, idcapa);
}

function accionCapaPublicadaCargar(_this, idcapa){
    limpiarFormularioSeleccionCapa();
    limpiarFormularioCapa();
    
    cargarDatosCapaPublicada(idcapa);
    
    document.getElementById('capaseleccionada').style.display='block';
    document.getElementById('AccionesSeleccionCapa').style.display='none';
    document.getElementById('botonSeleccionarCapa').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapa').style.display='none';
    document.getElementById('botonSeleccionarCapaCambio').style.display='block';
    document.getElementById('botonCancelarSeleccionarCapaCambio').style.display='none';
}

function limpiarFormularioIndicadores(){
    document.getElementById('formEditarIndicadores').setAttribute('idindicador', 0);
    document.getElementById('indNombre').value = '';
    document.getElementById('indDescripcion').value = '';
    document.getElementById('periodicidadSelector').value = 'elegir';
    document.getElementById('funcionalidadSelector').value = 'elegir';
    document.getElementById('inputFechaDesde').value = '';
    document.getElementById('inputFechaHasta').value = '';
    
    //Limpiar Columna Valores
    document.getElementById('columnasValores').innerHTML = '';
    inicializarColumnas();
    document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value = null;
    document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value = null;
}

function limpiarFormularioSeleccionCapa(){
    document.querySelector('#formSeleccionCapa #txningunacapa').style.display='block';
    document.querySelector('#formSeleccionCapa #listacapaspublicadas').innerHTML='';
    document.getElementById('formSeleccionCapa').style.display='none';
}

function limpiarFormularioCapa(){
    document.getElementById('capaseleccionada').setAttribute('idcapa', 0);
    document.getElementById('AccionesSeleccionCapa').style.display='block';
    document.getElementById('capaNombre').innerHTML = '';
    document.getElementById('capaDescripcion').innerHTML = '';
}

function limpiarFormularioIndPublicados(){
    document.getElementById('indicadorActivo').setAttribute('idindicador', 0);
    document.querySelector('#divSeleccionIndCuerpo #txningunind').style.display='block';
    document.getElementById('listaindpublicadas').innerHTML = '';
    document.getElementById('indCargaNombre').innerHTML = '';
    document.getElementById('indCargaDescripcion').innerHTML = '';
}

function asignarIdIndicador(idIndicador){
    document.getElementById('formEditarIndicadores').setAttribute('idindicador', idIndicador);
}

function cambiarModoSoloLectura(readonly){
    document.getElementById('indNombre').setAttribute('disabled', readonly);
    document.getElementById('indDescripcion').setAttribute('disabled', readonly);
    document.getElementById('periodicidadSelector').setAttribute('disabled', readonly);
    document.getElementById('funcionalidadSelector').setAttribute('disabled', readonly);
    document.getElementById('inputFechaDesde').setAttribute('disabled', readonly);
    document.getElementById('inputFechaHasta').setAttribute('disabled', readonly);
}

var _Features={};
function cargarFeatures(){
    _lyrElemSrc.clear();
    for(var elem in _Features){

        var format = new ol.format.WKT();	
        var _feat = format.readFeature(_Features[elem].geotx, {
            dataProjection: 'EPSG:3857',
            featureProjection: 'EPSG:3857'
        });

        _feat.setId(_Features[elem].id);

        _feat.setProperties({
            'id':_Features[elem].id
        });
        _c= hexToRgb(document.getElementById('inputcolorrelleno').value);
        _n=(1 - (document.getElementById('inputtransparenciarellenoNumber').value) * 1.0 / 100);
        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';

        _st= new ol.style.Style({
          fill: new ol.style.Fill({
            color: _rgba

          }),
          stroke: new ol.style.Stroke({
            color: document.getElementById('inputcolortrazo').value,
            width: document.getElementById('inputanchotrazoNumber').value
          })
        });
        _feat.setStyle (_st);

        _lyrElemSrc.addFeature(_feat);

        _MapaCargado='si';
    }

    _ext= _lyrElemSrc.getExtent();

    setTimeout(function(){
        mapa.getView().fit(_ext, { duration: 1000 });
    }, 50);
}


function cargarListadoCampas(_res){
    if (_res.data != null){
        for (var elemCampa in _res.data){
        	document.getElementById('txningunacampa').style.display='none';
            var divRoot = document.getElementById('listacampaspublicadas');
            var filaCampa = document.createElement('a');
            filaCampa.setAttribute('idind', _res.data[elemCampa]["id"]);
            filaCampa.setAttribute('class', 'filaCampaLista');
            filaCampa.setAttribute('onclick', 'cargarDatosCampa('+_res.data[elemCampa]["id"]+')');
            var campaId = document.createElement('div');
            campaId.setAttribute('id','campaIdLista');
            campaId.innerHTML = "ID <span class='idn'>" + _res.data[elemCampa]["id"]+"</span>";
            var indNombre = document.createElement('div');
            indNombre.setAttribute('id','campaNombreLista');
            indNombre.innerHTML = _res.data[elemCampa]["nombre"];
            var indAu = document.createElement('div');
            indAu.setAttribute('id','campaAutoriaLista');
            indAu.innerHTML ='por: '+ _res.data[elemCampa]["autornom"]+' '+_res.data[elemCampa]["autorape"];
            var indDescripcion = document.createElement('div');
            indDescripcion.setAttribute('id','campaDescripcionLista');
            indDescripcion.innerHTML = _res.data[elemCampa]["descripcion"];
            filaCampa.appendChild(campaId);
            filaCampa.appendChild(indNombre);
            filaCampa.appendChild(indDescripcion);
            filaCampa.appendChild(indAu);
            divRoot.appendChild(filaCampa);
        }
    } else {
        console.log('no hay indicadores publicados para este usuario');
    }
}


var _DataFormAgurp = {};
function cargarCamposFormulario(){
	
	_form=document.querySelector('#FormularioRegistro #campospersonalizados');
	_form.innerHTML='';
	_DataFormAgurp={};	
		
	for(_cn in _DataRele.camposOrden){
		_cid=_DataRele.camposOrden[_cn];
		_cdat = _DataRele.campos[_cid];
		
		_att={};
		
		//console.log(_cdat.inputattributes);
		if(_cdat.inputattributes!=null){
			_att=$.parseJSON(_cdat.inputattributes);
		}
			
		_div=document.createElement('div');
		_div.setAttribute('class','campo');
		_form.appendChild(_div);
		
		_la=document.createElement('h3');
		_la.innerHTML=_cdat.nombre;
		_div.appendChild(_la);
		
		_ay=document.createElement('div');
		_ay.setAttribute('class','divayuda');
		_la.appendChild(_ay);
		
		_aay=document.createElement('a');
		_aay.innerHTML='?';
		if(_cdat.ayuda==''){_aay.setAttribute('vacio','si');}
		if(_cdat.ayuda==null){_aay.setAttribute('vacio','si');}
		_ay.appendChild(_aay);
		
		_aytx=document.createElement('div');
		_aytx.innerHTML=_cdat.ayuda;
		_ay.appendChild(_aytx);
		
		
		
		
				
		if(_cdat.tipo=='texto'){
			_tag='input';			
			if(_att.tag!=null){
				_tag=_att.tag;
			}
			
			_type='text';			
			if(_att.type!=undefined){
				_type=_att.type;
			}
						
			if(_tag=='textarea'){
				
				_in=document.createElement(_tag);
				_in.setAttribute('name',_cdat.id);
				_div.appendChild(_in);
				
			}else if(_tag=='input'){
				
				if(_type=='text'){
					
					_in=document.createElement(_tag);
					_in.setAttribute('name',_cdat.id);
					_in.setAttribute('type',_type);
					_div.appendChild(_in);
					
				}else if(_type=='checkbox'){
					
					_op=_cdat.opciones.split(/\n/);
					//console.log(_op);
					
					if(_op[0]==undefined){_op[0]='1';}
					if(_op[1]==undefined){_op[1]='0';}
					
					_in=document.createElement(_tag);
					_in.setAttribute('for',_cdat.id);	
					_in.setAttribute('type',_type);
					_in.setAttribute('valorsi',_op[0]);
					_in.setAttribute('valorno',_op[1]);
					_in.setAttribute('onclick','toglevalorSiNo(this)');
					_div.appendChild(_in);
					
					_in=document.createElement(_tag);
					_in.setAttribute('name',_cdat.id);	
					_in.setAttribute('type','hidden');
					_in.setAttribute('onchange','toglevalorSiNoRev(this)');
					_div.appendChild(_in);
					
					_aclara=document.createElement('span');
					_aclara.innerHTML=_op[0];
					_div.appendChild(_aclara);
				}
			}
		}else if(_cdat.tipo=='numero'){
			
			_tag='input';			
			if(_att.tag!=null){
				_tag=_att.tag;
			}
			
			_type='text';			
			if(_att.type!=undefined){
				_type=_att.type;
			}
						
			if(_tag=='input'){
				if(_type=='text'){
					_in=document.createElement(_tag);
					_in.setAttribute('type',_type);
					_in.setAttribute('class','numero');
					_in.setAttribute('name',_cdat.id);
					_div.appendChild(_in);
					
					_un=document.createElement('span');
					_un.setAttribute('class','unidad');
					_un.innerHTML=_cdat.unidaddemedida;
					_div.appendChild(_un);
					
				}
			}
		}else if(_cdat.tipo=='coleccion_imagenes'){
			_d0=document.createElement('br');
			_div.appendChild(_d0);
			
			_d1=document.createElement('form');
			_div.appendChild(_d1);
			_d1.setAttribute('id','uploader');
			_d1.setAttribute('idcampo',_cdat.id);
			_d1.setAttribute('ondragover','resInputUpload(event,this)');
			_d1.setAttribute('ondragleave','desInputUpload(event,this)');
	                									
	            _d11=document.createElement('div');
				_d1.appendChild(_d11);
				_d11.setAttribute('id','contenedorlienzo');
	        
		        	_d111=document.createElement('div');
					_d11.appendChild(_d111);
					_d111.setAttribute('id','upload');
					
						_d1111=document.createElement('label');
						_d111.appendChild(_d1111);
						_d1111.innerHTML='Arrastre foto aqu�';
						
						_d1112=document.createElement('input');
						_d111.appendChild(_d1112);
	       				_d1112.setAttribute('id','uploadinput');
	       				_d1112.setAttribute('type','file');
	       				_d1112.setAttribute('onchange','cargarFotosCampo(event,this)');
	       				
	       				
	       	_d2=document.createElement('div');
			_d2.setAttribute('id','listadoDocumentos');
			_d2.setAttribute('idcampo',_cdat.id);
			_div.appendChild(_d2);
			
			_d3=document.createElement('input');
			_d3.setAttribute('type','hidden');
			_d3.setAttribute('name',_cdat.id);
			_div.appendChild(_d3);	
		}
		
		
		
		//agrupaci�n para inputs en matriz o (tabla)
		if(_att.agrupacion!=undefined){
						
			if(_DataFormAgurp[_att.agrupacion.nombre]==undefined){
				_DataFormAgurp[_att.agrupacion.nombre]={
					"nombre":_att.agrupacion.nombre,
					"columnas":{},
					"filas":{}
				};
				
				_grupo=document.createElement('div');
				_grupo.setAttribute('agrupacion',_att.agrupacion.nombre);
				_grupo.setAttribute('class','agrupacion');
				_form.appendChild(_grupo);
				
				_h3=document.createElement('h3');
				_h3.innerHTML=_att.agrupacion.nombre;
				_grupo.appendChild(_h3);
				
				_ta=document.createElement('table');			
				_grupo.appendChild(_ta);
				
				_tr0=document.createElement('tr');
				_tr0.setAttribute('nombre','0');
				_ta.appendChild(_tr0);
				
				_td=document.createElement('td');
				_td.setAttribute('nombre','0');
				_tr0.appendChild(_td);
						
			}
			
			_grupo=document.querySelector('#FormularioRegistro #campospersonalizados [agrupacion="'+_att.agrupacion.nombre+'"');
			_ta=_grupo.querySelector('table');
			
			_tr0=_ta.querySelector('tr');
			
			
			if(_DataFormAgurp[_att.agrupacion.nombre]["filas"][_att.agrupacion.fila]==undefined){
				_DataFormAgurp[_att.agrupacion.nombre]["filas"][_att.agrupacion.fila]='';
				
				_ff=_ta.querySelector('tr');
				
				_trN=_ff.cloneNode(true);
				_ta.appendChild(_trN);
				_trN.setAttribute('nombre',_att.agrupacion.fila);
				
				_td0=_trN.querySelector('td');
				//console.log(_td0);
				
				
				_ay=document.createElement('div');
				_ay.setAttribute('class','divayuda');
				_td0.appendChild(_ay);
				
				_aay=document.createElement('a');
				_aay.innerHTML='?';
				if(_cdat.ayuda==''){_aay.setAttribute('vacio','si');}
				if(_cdat.ayuda==null){_aay.setAttribute('vacio','si');}
				_ay.appendChild(_aay);
				
				_aytx=document.createElement('div');
				_aytx.innerHTML=_cdat.ayuda;
				_ay.appendChild(_aytx);
				
				_td0.innerHTML+=_att.agrupacion.fila;	
			}
			
			_trN=_ta.querySelector('td[nombre="'+_att.agrupacion.fila+'"]');			
			_ffilas=_ta.querySelectorAll('tr');
			
			if(_DataFormAgurp[_att.agrupacion.nombre]["columnas"][_att.agrupacion.columna]==undefined){
				_DataFormAgurp[_att.agrupacion.nombre]["columnas"][_att.agrupacion.columna]='';
				
				//console.log('filas:');
				for(_nf in _ffilas){
					//console.log(_nf);
					if(typeof _ffilas[_nf] != 'object'){continue;}					
					_tdN=document.createElement('td');
					_ffilas[_nf].appendChild(_tdN);
					if(_ffilas[_nf].getAttribute('nombre')=='0'){
						_tdN.innerHTML=_att.agrupacion.columna;
					}
					_tdN.setAttribute('nombre',_att.agrupacion.columna);					
				}
			}
			_loc=_grupo.querySelector('tr[nombre="'+_att.agrupacion.fila+'"] td[nombre="'+_att.agrupacion.columna+'"]');
			
			if(_loc==undefined){
				//console.log('tr[nombre="'+_att.agrupacion.fila+'"] td[nombre="'+_att.agrupacion.columna+'"]');
				//console.log('error al definir matriz tabla');
			}else{
				_loc.innerHTML='';
				_loc.appendChild(_div);
				_h3=_div.querySelector('h3');
				_h3.parentNode.removeChild(_h3);
			}
		}
		
	}
	$('textarea').each(function () {
		
	  this.setAttribute('style', 'height:' + Math.max(41,this.scrollHeight) + 'px;overflow-y:hidden;');
	}).on('input', function () {
	  this.style.height = 'auto';
	  this.style.height = (this.scrollHeight) + 'px';
	});
}


function cargarValoresCapaExist(_res){

	
    if(_res.data == null){
    	alert('error, no se encontr� la capa');
    	return;
    }
    
    var capaQuery = _res.data;

    document.getElementById('capaseleccionada').setAttribute('idcapa', capaQuery["id"]);
    document.getElementById('capaNombre').innerHTML = capaQuery["nombre"];
    document.getElementById('capaDescripcion').innerHTML = capaQuery["descripcion"];

    //Operaciones para leer del xml los valores de simbologia
    var xmlSld = capaQuery["sld"];

    if (xmlSld && xmlSld != ''){
        var colorRelleno = '';
        var transparenciaRelleno = '';
        var colorTrazo = '';
        var anchoTrazo = '';

        var xmlDoc;
        if (window.DOMParser)
        {
            parser = new DOMParser();
            xmlDoc = parser.parseFromString(xmlSld, "text/xml");
        }
        else // Internet Explorer
        {
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async = false;
            xmlDoc.loadXML(xmlSld);
        }

        var xmlFill = xmlDoc.getElementsByTagName("Fill")[0];
        for(var node in xmlFill.childNodes){
            if (xmlFill.childNodes[node].nodeName == "CssParameter" 
                    && xmlFill.childNodes[node].getAttribute("name") == "fill"){
                colorRelleno = xmlFill.childNodes[node].textContent;
            }
            if (xmlFill.childNodes[node].nodeName == "CssParameter"
                    && xmlFill.childNodes[node].getAttribute("name") == "fill-opacity"){
                transparenciaRelleno = xmlFill.childNodes[node].textContent;
            }
        }

        var xmlStroke = xmlDoc.getElementsByTagName("Stroke")[0];
        for(var node in xmlStroke.childNodes){
            if (xmlStroke.childNodes[node].nodeName == "CssParameter"
                    && xmlStroke.childNodes[node].getAttribute("name") == "stroke"){
                colorTrazo = xmlStroke.childNodes[node].textContent;
            }
            if (xmlStroke.childNodes[node].nodeName == "CssParameter"
                    && xmlStroke.childNodes[node].getAttribute("name") == "stroke-width"){
                anchoTrazo = xmlStroke.childNodes[node].textContent;
            }
        }

        document.getElementById('inputcolorrelleno').value = colorRelleno;
        document.getElementById('inputtransparenciarellenoNumber').value = transparenciaRelleno * 100;
        document.getElementById('inputtransparenciarellenoRange').value = transparenciaRelleno * 100;
        document.getElementById('inputcolortrazo').value = colorTrazo;
        document.getElementById('inputanchotrazoNumber').value = anchoTrazo;
        document.getElementById('inputanchotrazoRange').value = anchoTrazo;
    }
    
    cargarValoresCapaExistQuery();
}

function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function cargarValoresIndicadorExist(_res){
    if (_res.data != null){
        var indicadorQuery = _res.data;

		_form=document.querySelector('#formEditarIndicadores');
        _form.setAttribute('idindicador', indicadorQuery["id"]);
        
        if(indicadorQuery["zz_publicada"]=='1'){
        	document.querySelector('#divMenuAccionesCrea #botonPublicar').style.display='none';
        }else{
        	document.querySelector('#divMenuAccionesCrea #botonPublicar').style.display='inline-block';
        }
        
        _form.querySelector('#indNombre').value = indicadorQuery["nombre"];
        _form.querySelector('#indDescripcion').value = indicadorQuery["descripcion"];
        _form.querySelector('#inputFechaDesde').value = indicadorQuery["fechadesde"];
        _form.querySelector('#inputFechaHasta').value = indicadorQuery["fechahasta"];
        
        _form.querySelector('#calc_buffer').value = indicadorQuery["calc_buffer"];
        _form.querySelector('#calc_superp').value = indicadorQuery["calc_superp"];
        _form.querySelector('#calc_zonificacion').value = indicadorQuery["calc_zonificacion"];
        
        
        if (indicadorQuery["funcionalidad"] != null && indicadorQuery["funcionalidad"] != ''){
            document.getElementById('funcionalidadSelector').value = indicadorQuery["funcionalidad"];
            if(indicadorQuery["funcionalidad"]=='nuevaGeometria'){
            	document.querySelector('#formcalculo').style.display='block';
            }else{
            	document.querySelector('#formcalculo').style.display='none';
            }
        } else {
            document.getElementById('funcionalidadSelector').value = 'elegir';
        }
        
        if (indicadorQuery["periodicidad"] != null && indicadorQuery["periodicidad"] != ''){
            document.getElementById('periodicidadSelector').value = indicadorQuery["periodicidad"];
        } else {
            document.getElementById('periodicidadSelector').value = 'elegir';
        }
        
        if (indicadorQuery["id_p_ref_capasgeo"] != null &&  indicadorQuery["id_p_ref_capasgeo"] > 0){
            accionCapaPublicadaCargar(this, indicadorQuery["id_p_ref_capasgeo"]);
        }
        
        document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value = indicadorQuery['representar_val_max'];
        document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value = indicadorQuery['representar_val_min'];
        
        for (var i = 1; i <= 5; i++) { 
            if (indicadorQuery['col_texto'+i+'_nom'] != null || indicadorQuery['col_texto'+i+'_unidad'] != null){
                var columnaValorNueva = accionAnadirNuevaColumnaValorInit(null, 0, i);
                columnaValorNueva.querySelector('.indValoresTipo').value = 'texto';
                //accionSelectTipoColumna(null, )
                columnaValorNueva.querySelector('.indValoresNombre').value = indicadorQuery['col_texto'+i+'_nom'];
                columnaValorNueva.querySelector('.indValoresUnidad').value = indicadorQuery['col_texto'+i+'_unidad'];
                columnaValorNueva.querySelector('.indValoresUnidad').placeholder = 'Categorias separadas por coma';
                
                if (indicadorQuery['representar_campo'] == 'col_texto'+i){
                    columnaValorNueva.querySelector('.indValoresRep').checked = true;
                    actualizarEscala();
                }
            }
        }
        for (var i = 1; i <= 5; i++) { 
            if (indicadorQuery['col_numero'+i+'_nom'] != null || indicadorQuery['col_numero'+i+'_unidad'] != null){
                var columnaValorNueva = accionAnadirNuevaColumnaValorInit(null, i, 0);
                columnaValorNueva.querySelector('.indValoresTipo').value = 'numerico';
                //accionSelectTipoColumna(null, )
                columnaValorNueva.querySelector('.indValoresNombre').value = indicadorQuery['col_numero'+i+'_nom'];
                columnaValorNueva.querySelector('.indValoresUnidad').value = indicadorQuery['col_numero'+i+'_unidad'];
                columnaValorNueva.querySelector('.indValoresUnidad').placeholder = 'Unidad de medida';
                
                if (indicadorQuery['representar_campo'] == 'col_numero'+i){
                    columnaValorNueva.querySelector('.indValoresRep').checked = true;
                    actualizarEscala();
                }
            }
        }
    } else {
        alert('error otjsf0jg44ffgh');
    }
}

function accionEditarIndCampo(_event,_this){
    //console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarCampoInd(_this.id);
    }
}

function editarCampoInd(idcampo){
    var nuevoValor = document.getElementById(idcampo).value;
    
    var campo = '';
    switch (idcampo){
        case 'indNombre':
            campo = 'nombre';
            break;
        case 'indDescripcion':
            campo = 'descripcion';
            break;
        case 'idcapa':
            campo = 'idcapa';
            break;
        case 'inputFechaDesde':
            campo = 'fechadesde';
            break;
        case 'inputFechaHasta':
            campo = 'fechahasta';
            break;
        case 'calc_buffer':
            campo = 'calc_buffer';
            break;
        case 'calc_superp':
            campo = 'calc_superp';
            break;
        case 'calc_zonificacion':
            campo = 'calc_zonificacion';
            break;                
                
            
        default:
            alert('error mvr20fh');
    }
    
    editarCampoIndParametros(campo, nuevoValor);
}

function editarCampoIndParametros(nombreCampo, nuevoValor){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindicador
    };
    parametros[nombreCampo] = nuevoValor;
    
    editarInd(parametros);
}

function editarCampoIndParametrosColumna(nombreColumna, nuevoValor){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindicador,
        'columna': nombreColumna,
        'columnavalor': nuevoValor
    };
    
    editarInd(parametros);
}

function editarIndPeriodicidad(_event, _this){
    var nuevaPeriodicidad = _this.options[_this.selectedIndex].value;
    
    if (nuevaPeriodicidad == 'mensual' || nuevaPeriodicidad == 'anual'){
        editarCampoIndParametros('periodicidad', nuevaPeriodicidad);
    } else {
        editarCampoIndParametros('periodicidad', 'NULL');
    }
}

function editarIndFuncionalidad(_event, _this){
    var nuevoValor = _this.options[_this.selectedIndex].value;
    
    document.querySelector('#formcalculo').style.display='none';
    
    
    
    if (nuevoValor == '' || nuevoValor == 'elegir'){
        editarCampoIndParametros('funcionalidad', 'NULL');
    } else {
        editarCampoIndParametros('funcionalidad', nuevoValor);
    }
    if(nuevoValor=='nuevaGeometria'){
    	document.querySelector('#formcalculo').style.display='block';
    	document.querySelector('#tipodeometriaNuevaGeometria').style.display='block';
    }else{
    	document.querySelector('#formcalculo').style.display='none';
    	document.querySelector('#tipodeometriaNuevaGeometria').style.display='none';
    }
}

var _ColumnasNumericasUsadas = [];
var _ColumnasTextoUsadas = [];

function inicializarColumnas(){
    _ColumnasNumericasUsadas[1] = false;
    _ColumnasNumericasUsadas[2] = false;
    _ColumnasNumericasUsadas[3] = false;
    _ColumnasNumericasUsadas[4] = false;
    _ColumnasNumericasUsadas[5] = false;
    
    _ColumnasTextoUsadas[1] = false;
    _ColumnasTextoUsadas[2] = false;
    _ColumnasTextoUsadas[3] = false;
    _ColumnasTextoUsadas[4] = false;
    _ColumnasTextoUsadas[5] = false;
}
inicializarColumnas();

function accionAnadirNuevaColumnaValor(_this){
    accionAnadirNuevaColumnaValorInit(_this, 0, 0);
}

function accionAnadirNuevaColumnaValorInit(_this, camponumerico, campotexto){
    var divColumnasValores = document.getElementById('columnasValores');
    var columnaValorNueva = document.createElement('div');
    columnaValorNueva.setAttribute('id', 'nuevacolumna');
    columnaValorNueva.setAttribute('camponumerico', camponumerico);
    columnaValorNueva.setAttribute('campotexto', campotexto);
    
    var inputRepColumna = document.createElement('input');
    inputRepColumna.setAttribute('class', 'indValoresRep');
    inputRepColumna.setAttribute('type', 'radio');
    inputRepColumna.setAttribute('name', 'representar');
    inputRepColumna.setAttribute('onchange', 'accionSeleccionarRepColumna()');
    columnaValorNueva.appendChild(inputRepColumna);    
    
    var _sel = document.querySelector('#columnasValores input[type="radio"]:checked');
    if(_sel == undefined){
    	inputRepColumna.checked=true;
    }
    
    var inputNombreColumna = document.createElement('input');
    inputNombreColumna.setAttribute('class', 'indValoresNombre');
    inputNombreColumna.setAttribute('type', 'text');
    inputNombreColumna.setAttribute('onchange', 'accionInputNombreColumna(event, this)');
    columnaValorNueva.appendChild(inputNombreColumna);
    
    var selectTipoColumna = document.createElement('select');
    selectTipoColumna.setAttribute('class', 'indValoresTipo');
    selectTipoColumna.setAttribute('onchange', 'accionSelectTipoColumna(event, this);actualizarEscala();');
    var optionTexto = document.createElement('option');
    optionTexto.setAttribute('value', 'texto');
    optionTexto.innerHTML = 'Texto';
    var optionNumerico = document.createElement('option');
    optionNumerico.setAttribute('value', 'numerico');
    optionNumerico.innerHTML = 'Num�rico';
    var optionElegir = document.createElement('option');
    optionElegir.setAttribute('value', 'elegir');
    optionElegir.innerHTML = '-elegir-';
    
    selectTipoColumna.appendChild(optionElegir);
    selectTipoColumna.appendChild(optionTexto);
    selectTipoColumna.appendChild(optionNumerico);
    optionElegir.selected = true;
    columnaValorNueva.appendChild(selectTipoColumna);
    
    var inputUnidadColumna = document.createElement('input');
    inputUnidadColumna.setAttribute('class', 'indValoresUnidad');
    inputUnidadColumna.setAttribute('type', 'text');
    inputUnidadColumna.setAttribute('onkeyup', 'actualizarEscala()');
    inputUnidadColumna.setAttribute('onchange', 'accionInputUnidadColumna(event, this)');
    columnaValorNueva.appendChild(inputUnidadColumna);
    
    var botonEliminarColumna = document.createElement('a');
    botonEliminarColumna.setAttribute('class', 'indValoresEliminar');
    botonEliminarColumna.setAttribute('onclick', 'accionEliminarNuevaColumnaValor(event, this)');
    botonEliminarColumna.innerHTML = 'elim';
    columnaValorNueva.appendChild(botonEliminarColumna);
    
    divColumnasValores.appendChild(columnaValorNueva);
    return columnaValorNueva;
}

function accionEliminarNuevaColumnaValor(_event, _this){
    var camponumerico = 0;
    var campotexto = 0;
    if (_this.parentNode.hasAttribute('camponumerico')){
        camponumerico = _this.parentNode.getAttribute('camponumerico');
    }
    if (_this.parentNode.hasAttribute('campotexto')){
        camponumerico = _this.parentNode.getAttribute('campotexto');
    }
    
    eliminarColumna(_this.parentNode);
    
    _this.parentNode.parentNode.removeChild(_this.parentNode);
    
    desasignarColumna(_this.parentNode);
    
    var _sel = document.querySelector('#columnasValores input[type="radio"]:checked');
    if(_sel == undefined){
        var sel = document.querySelector('#columnasValores input[type="radio"]');
        if (sel != undefined){
            sel.checked = true;
        }
    }
}

function accionSelectTipoColumna(_event, _this){
    switch(_this.value){
        case 'elegir':
            desasignarColumna(_this.parentNode);
            break;
        case 'numerico':
            var columnaAsignada = asignarColumnaNumerica();
            if (columnaAsignada == 0){
                alert('Ya ha seleccionado el maximo numero de campos numericos');
                _this.value = 'elegir';
                desasignarColumna(_this.parentNode);
            } else{
                for (var child in _this.parentNode.children){
                    if (_this.parentNode.children[child].nodeType == 1 &&
                            _this.parentNode.children[child].getAttribute('class') == 'indValoresUnidad'){
                       _this.parentNode.children[child].placeholder = 'Unidad de medida';
                    }
                }
                
                guardarColumna(_this.parentNode);
                desasignarColumna(_this.parentNode);
                _this.parentNode.setAttribute('camponumerico', columnaAsignada);
            }
            break;
        case 'texto':
            var columnaAsignada = asignarColumnaTexto();
            if (columnaAsignada == 0){
                alert('Ya ha seleccionado el maximo numero de campos de texto');
                _this.value = 'elegir';
                desasignarColumna(_this.parentNode);
            } else{
                for (var child in _this.parentNode.children){
                    if (_this.parentNode.children[child].nodeType == 1 &&
                            _this.parentNode.children[child].getAttribute('class') == 'indValoresUnidad'){
                       _this.parentNode.children[child].placeholder = 'Categorias separadas por coma';
                    }
                }
                
                guardarColumna(_this.parentNode);
                desasignarColumna(_this.parentNode);
                _this.parentNode.setAttribute('campotexto', columnaAsignada);
            }
            break;
        default:
            alert('error 0vn20vjk3');
    }
}

function asignarColumnaNumerica(){
    for (var col in _ColumnasNumericasUsadas){
        if (_ColumnasNumericasUsadas[col] == false){
            _ColumnasNumericasUsadas[col] = true;
            return col;
        }
    }
    return 0;
}

function asignarColumnaTexto(){
    for (var col in _ColumnasTextoUsadas){
        if (_ColumnasTextoUsadas[col] == false){
            _ColumnasTextoUsadas[col] = true;
            return col;
        }
    }
    return 0;
}

function accionInputNombreColumna(_event, _this){
    var nuevoValor = _this.value;
    var camponumerico = _this.parentNode.getAttribute('camponumerico');
    var campotexto = _this.parentNode.getAttribute('campotexto');
    var campo = '';
    
    if (camponumerico > 0 && campotexto > 0){
        alert('error vi303js0cd');
        return;
    }
    
    if (camponumerico > 0){
        campo = 'col_numero'+camponumerico+'_nom';
    }
    
    if (campotexto > 0){
        campo = 'col_texto'+campotexto+'_nom';
    }
    
    if (campo != ''){
        editarCampoIndParametrosColumna(campo, nuevoValor);
    }
}

function accionInputUnidadColumna(_event, _this){
    var nuevoValor = _this.value;
    var camponumerico = _this.parentNode.getAttribute('camponumerico');
    var campotexto = _this.parentNode.getAttribute('campotexto');
    var campo = '';
    
    if (camponumerico > 0 && campotexto > 0){
        alert('error vi303js0cd');
        return;
    }
    
    if (camponumerico > 0){
        campo = 'col_numero'+camponumerico+'_unidad';
    }
    
    if (campotexto > 0){
        campo = 'col_texto'+campotexto+'_unidad';
    }
    
    if (campo != ''){
        editarCampoIndParametrosColumna(campo, nuevoValor);
    }
}

function guardarColumna(divColumnaNueva){
    //guardar todos los datos de esta columna, leyendo los componentes hijos y disparando una edicion en cada uno
    for (var child in divColumnaNueva.children){
        if (divColumnaNueva.children[child].nodeType == 1 &&
                divColumnaNueva.children[child].getAttribute('class') == 'indValoresNombre'){
            accionInputNombreColumna(null, divColumnaNueva.children[child]);
        }
        
        if (divColumnaNueva.children[child].nodeType == 1 &&
                divColumnaNueva.children[child].getAttribute('class') == 'indValoresUnidad'){
            accionInputUnidadColumna(null, divColumnaNueva.children[child]);
        }
    }
}

function eliminarColumna(divColumnaNueva){
    var camponumerico = 0;
    var campotexto = 0;
    if (divColumnaNueva.hasAttribute('camponumerico')){
        camponumerico = divColumnaNueva.getAttribute('camponumerico');
    }
    if (divColumnaNueva.hasAttribute('campotexto')){
        campotexto = divColumnaNueva.getAttribute('campotexto');
    }
    
    if (camponumerico > 0 && campotexto > 0){
        alert('error vi303js0cd');
        return;
    }
    
    if (camponumerico > 0){
        var campo = '';
        campo = 'col_numero'+camponumerico+'_nom';
        editarCampoIndParametrosColumna(campo, 'NULL');
        
        campo = '';
        campo = 'col_numero'+camponumerico+'_unidad';
        editarCampoIndParametrosColumna(campo, 'NULL');
    }
    
    if (campotexto > 0){
        var campo = '';
        campo = 'col_texto'+campotexto+'_nom';
        editarCampoIndParametrosColumna(campo, 'NULL');
        
        campo = '';
        campo = 'col_texto'+campotexto+'_unidad';
        editarCampoIndParametrosColumna(campo, 'NULL');
    }
    
}

function desasignarColumna(divColumnaNueva){
    var camponumerico = divColumnaNueva.getAttribute('camponumerico');
    if (camponumerico > 0){
        _ColumnasNumericasUsadas[camponumerico] = false;
        divColumnaNueva.setAttribute('camponumerico', 0);
    }
    
    var campotexto = divColumnaNueva.getAttribute('campotexto');
    if (campotexto > 0){
        _ColumnasTextoUsadas[campotexto] = false;
        divColumnaNueva.setAttribute('campotexto', 0);
    }
    
    for (var child in divColumnaNueva.children){
        if (divColumnaNueva.children[child].nodeType == 1 &&
                divColumnaNueva.children[child].getAttribute('class') == 'indValoresUnidad'){
           divColumnaNueva.children[child].placeholder = '';
        }
    }
}

function accionSeleccionarRepColumna(){
    actualizarEscala();
    
    editarRepresentacionCampo();
}

function editarRepresentacionCampo(){
    var opcionSeleccionada = document.querySelector('#columnasValores input[type="radio"]:checked');
    if (opcionSeleccionada != undefined){
        var _tipo = document.querySelector('#columnasValores input[type="radio"]:checked').parentNode.querySelector('select').value;
        if(_tipo == 'texto' || _tipo == 'numerico'){
            var col_seleccionada = 'NULL';
            var camponumerico = opcionSeleccionada.parentNode.getAttribute('camponumerico');
            var campotexto = opcionSeleccionada.parentNode.getAttribute('campotexto');

            if (camponumerico > 0 && campotexto > 0){
                alert('error vi303js0cd');
                return;
            }

            if (camponumerico > 0){
                col_seleccionada = 'col_numero'+camponumerico;
            }

            if (campotexto > 0){
                col_seleccionada = 'col_texto'+campotexto;
            }

            editarCampoIndParametrosColumna('representar_campo', col_seleccionada);
        } else {
            editarCampoIndParametrosColumna('representar_campo', 'NULL');
        }
    }
}

function actualizarEscala(){
    _tipo = document.querySelector('#columnasValores input[type="radio"]:checked').parentNode.querySelector('select').value;

    document.querySelector('#representacionescalacolor[tipo="numero"]').style.display='none';
    document.querySelector('#representacionescalacolor[tipo="texto"]').style.display='none';

    if(_tipo=='texto'){
        document.querySelector('#representacionescalacolor[tipo="texto"]').style.display='block';
        actualizarEscalaT();
    }
    if(_tipo=='numerico'){		
        document.querySelector('#representacionescalacolor[tipo="numero"]').style.display='block';			
        actualizarEscalaN();
    }
}

function accionRepresentacionValorMaximo(){
    actualizarEscalaN();
    
    editarRepresentacionValorMaximo();
}

function editarRepresentacionValorMaximo(){
    var _max = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value);
    if (_max > 0){
        editarCampoIndParametrosColumna('representar_val_max', _max);
    } else {
        editarCampoIndParametrosColumna('representar_val_max', 'NULL');
    }
}

function accionRepresentacionValorMinimo(){
    actualizarEscalaN();
    
    editarRepresentacionValorMinimo();
}

function editarRepresentacionValorMinimo(){
    var _min = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value);    
    if (_min > 0){
        editarCampoIndParametrosColumna('representar_val_min', _min);
    } else {
        editarCampoIndParametrosColumna('representar_val_min', 'NULL');
    }
}

function actualizarEscalaN(){
    var _max = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#maximo').value);
    var _min = parseFloat(document.querySelector('#representacionescalacolor[tipo="numero"] input#minimo').value);
    //console.log(_max);
    //console.log(_min);
    var _med = (_max+_min)/2;
    var _mm = (_max+_min);
    //console.log(_mm);
    _mm = _mm/2;
    //console.log(_mm);

    var _paso = _max-_med;
    var _sobre = Math.round(100*(_max+_paso))/100;
    var _sub  = Math.round(100*(_min-_paso))/100;		

    //console.log(_med);
    _mm = 100*(_med);
    //console.log(_mm);
    _mm = Math.round(_mm);
    //console.log(_mm);
    _mm = _mm/100;
    //console.log(_mm);

    _med =_mm;

    if(!isNaN(_med)){
        document.querySelector('#representacionescalacolor[tipo="numero"]  #sobre #valorminimo p').innerHTML=_sobre;
    }
    if(!isNaN(_med)){
        document.querySelector('#representacionescalacolor[tipo="numero"]  #alto #valorminimo p').innerHTML=_med;
    }
    if(!isNaN(_sub)){
        document.querySelector('#representacionescalacolor[tipo="numero"]  #minimo #valorminimo p').innerHTML=_sub;
    }
}

function actualizarEscalaT(){
    var _input = document.querySelector('#columnasValores input[type="radio"]:checked').parentNode.querySelector('#columnasValores input.indValoresUnidad').value;
    //console.log(_input);
    var _array = _input.split(',');

    var _rmax = 228;
    var _gmax = 25;
    var _bmax = 55;

    var _rmin = 204;
    var _gmin = 255;
    var _bmin = 204;

    var _cant = _array.length;
    //console.log(_cant);
    var _rpaso = (_rmax-_rmin)/(_cant-1);
    var _gpaso = (_gmax-_gmin)/(_cant-1);
    var _bpaso = (_bmax-_bmin)/(_cant-1);

    var _cont = document.querySelector('#representacionescalacolor[tipo="texto"]');
    _cont.innerHTML='';

    if(_cant<2){return;}
    for(var _np in _array){
        var _red = _rmin+(_np*_rpaso);
        var _gre = _gmin+(_np*_gpaso);
        var _blu = _bmin+(_np*_bpaso);

        //console.log(_red);

        var _fil = document.createElement('div');
        _fil.setAttribute('class','fila');
        _cont.appendChild(_fil);

        var _col = document.createElement('div');
        _col.setAttribute('class','color');
        _col.setAttribute('style','background-color: rgb('+ _red +' '+ _gre +' '+ _blu +');');

        _fil.appendChild(_col);

        var _val = document.createElement('div');
        _val.setAttribute('class','valor');
        _val.innerHTML=_array[_np];
        _fil.appendChild(_val);
    }
}



function mostrarListadoIndicadoresPublicados(){
    document.querySelector('#divSeleccionIndCuerpo #txningunind').style.display='none';
    document.getElementById('divSeleccionIndCuerpo').style.display='block';
}

function accionIndicadorPublicadoSeleccionado(_this, idindicador){
	_encuadrado='no';
    document.getElementById('indicadorActivo').setAttribute('idindicador', idindicador);    
    document.querySelector('#divListaIndicadoresCarga').setAttribute('idindicadorcarga',idindicador);
    document.querySelector('#botonCancelarCarga').style.display='block';
    
    var fechaHoy = new Date();
    cargarIndicadorPublicado(idindicador, fechaHoy.getFullYear(), fechaHoy.getMonth()+1);
}

function accionIndicadorPublicadoSeleccionadoModificar(_this, idindicador){
    consultarIndicadorParaModificar(idindicador);
    document.getElementById('formSeleccionInd').style.display='none';
    document.getElementById('formEditarIndicadores').style.display='inline-block';
}

function accionIndicadorPublicadoCargar(idindicador, _res, seleccionarFechaAno, seleccionarFechaMes, seleccionarDefault){
    if (_res.data != null && _res.data['indicador'] != null){
        document.getElementById('indCargaNombre').innerHTML = _res.data['indicador']['nombre'];
        document.getElementById('indTituloNombre').innerHTML = _res.data['indicador']['nombre'];
        document.getElementById('indCargaDescripcion').innerHTML = _res.data['indicador']['descripcion'];
        document.getElementById('indCargaPeriodicidad').innerHTML = _res.data['indicador']['periodicidad'];
        document.getElementById('indicadorActivo').setAttribute('periodicidad', _res.data['indicador']['periodicidad']);

        document.getElementById('formSeleccionInd').style.display='none';
        document.getElementById('divListaIndicadoresCarga').style.display='block';
        document.querySelector('#divListaIndicadoresCarga #AccionesSeleccionIndCambio').style.display='none';
		document.querySelector('#divListaIndicadoresCarga #botonSeleccionarIndCambio').setAttribute('idind',_res.data.indicador.id);
		
		
        if (_res.data['indicador']['fechadesde'] == null || _res.data['indicador']['fechahasta'] == null){
            alert('error asf0jg434f2f0gh');
        }

        var fechadesde = new Date(_res.data['indicador']['fechadesde']);
        var fechahasta = new Date(_res.data['indicador']['fechahasta']);

        if (fechahasta < fechadesde){
            alert('error 04nf82kd02j5');
        }

        //Listado de todos los a�os
        var years = new Array();
        var i = 0;
        for (var nYear = fechadesde.getFullYear(); nYear <= fechahasta.getFullYear(); nYear++) {
            years[i] = nYear;
            i++;
        }
		
        var periodicidad = _res.data['indicador']['periodicidad'];

        var periodos = new Array();
        if (periodicidad == 'anual'){
            for (var i in years) {
            	if(i == 0){continue;}
                var estadoCarga = 'sincarga';
                
                if (_res.data['periodos'][years[i]].estado == 'incompleto'){
                    estadoCarga = 'cargaincompleta';
                } else if (_res.data['periodos'][years[i]].estado == 'completo'){
                    estadoCarga = 'cargacompleta';
                }
                
                periodos[i] = new Array();
                periodos[i][0] = years[i].toString();
                periodos[i][1] = '';
                var periodoString = years[i].toString();
                periodoString = periodoString+"-01-01";
                periodos[i][2] = periodoString;
                periodos[i][3] = estadoCarga;
                periodos[i][4] = _res.data['periodos'][years[i]].resumen;
            }
        }

        if (periodicidad == 'mensual'){
        	console.log(fechadesde);
        	
            var mesNumero = fechadesde.getUTCMonth();
            console.log(mesNumero);
            var mesNumero = mesNumero+1;
             console.log(mesNumero);
            var indicePeriodos = 0;

            for (var i in years) {
                var anoString = years[i].toString();
                var mesString = '';
                if (mesNumero > 12){
                    mesNumero = 1;
                }
				
                while (mesNumero <= 12) {
                    if (years[i] == fechahasta.getFullYear() && mesNumero > fechahasta.getMonth()){
                        break;
                    }
                    
                    var estadoCarga = 'sincarga';
                    console.log("a�o:"+years[i]);
                    console.log("mes:"+(mesNumero));
                    
                    if (_res.data['periodos'][years[i]][mesNumero].estado == 'incompleto'){
                        estadoCarga = 'cargaincompleta';
                    } else if (_res.data['periodos'][years[i]][mesNumero].estado == 'completo'){
                        estadoCarga = 'cargacompleta';
                    }
					
                    mesString = obtenerNombreMes((mesNumero-1));
                    periodos[indicePeriodos] = new Array();
                    periodos[indicePeriodos][0] = anoString; //A�o
                    periodos[indicePeriodos][1] = mesString; //nombre del mes
                    periodos[indicePeriodos][2] = mesNumero; //numero del mes
                    periodos[indicePeriodos][3] = estadoCarga;
                    periodos[indicePeriodos][4] = _res.data['periodos'][years[i]][mesNumero].resumen;
                    mesNumero++;
                    indicePeriodos++;
                }
            }
        }

        var divRoot = document.getElementById('selectorPeriodo');
        divRoot.innerHTML = '';
		console.log(periodos);
        for (var indice in periodos){
            var divPeriodo = document.createElement('div');
            divPeriodo.setAttribute('id', 'periodoFecha'+periodos[indice][1]+periodos[indice][0]);
            divPeriodo.setAttribute('class', 'card '+periodos[indice][3]);
            divPeriodo.setAttribute('selected', 'false');
           
            

            var h2mes = document.createElement('h2');
            h2mes.innerHTML = periodos[indice][1];
            var h2ano = document.createElement('h2');
            h2ano.innerHTML = periodos[indice][0];

            divPeriodo.appendChild(h2mes);
            divPeriodo.appendChild(h2ano);

			_divValor=document.createElement('div');
			_divValor.setAttribute('id','valor');
			
			_divPorc=document.createElement('div');
			_divPorc.setAttribute('id','porc');
			
			if(periodos[indice][4]!=undefined){
				console.log(periodos[indice][4]);	
				if(_res.data.indicador.funcionalidad=='nuevaGeometria'){
					_val=periodos[indice][4].superp_sum
					if(_val>100){
	        			_v=formatearNumero(_val,0);	
	        		}else{
	        			_v=formatearNumero(_val,2);	
	        		}
					_divValor.innerHTML=_v;
					
					
					if(Number(periodos[indice][4].superp_max_numero1)>0){
						_val=Number(periodos[indice][4].superp_sum*100/Number(periodos[indice][4].superp_max_numero1));
						if(_val>10){
		        			_v=formatearNumero(_val,0);	
		        		}else{
		        			_v=formatearNumero(_val,2);
		        		}
						_divPorc.innerHTML=_v+'%';
					}
				}else if(_res.data.indicador.funcionalidad=='geometriaExistente'){
					_val=periodos[indice][4].sum_numero1
					if(_val>100){
	        			_v=formatearNumero(_val,0);	
	        		}else{
	        			_v=formatearNumero(_val,2);	
	        		}
					_divValor.innerHTML='s: '+_v;
					
		
					_val=periodos[indice][4].prom_numero1;
					if(_val>100){
        				_v=formatearNumero(_val,0);	
	        		}else{
	        			_v=formatearNumero(_val,2);	
	        		}
					_divPorc.innerHTML='m: '+_v;
					
				}
			}
			
			
			divPeriodo.appendChild(_divValor);
			divPeriodo.appendChild(_divPorc);
			
            divRoot.appendChild(divPeriodo);
        }

        
        
       
    } else {
        console.log('Error al cargar indicador');
    }
}

function formatNumberTwoDigits(number) {
     return (number < 10 ? '0' : '') + number;
}

function obtenerNombreMes(mesNumero){
    var month = new Array();
    month[0] = "Enero";
    month[1] = "Febrero";
    month[2] = "Marzo";
    month[3] = "Abril";
    month[4] = "Mayo";
    month[5] = "Junio";
    month[6] = "Julio";
    month[7] = "Agosto";
    month[8] = "Septiembre";
    month[9] = "Octubre";
    month[10] = "Noviembre";
    month[11] = "Diciembre";

    return month[mesNumero];
}

function accionSeleccionarIndCambio(_this){
	
	//pone en edici�n un indicador definido. Como hacer esto es peligroso, una vez ingresados datos, solo se puede con el m�ximo lnivel de acceso y tras verificarlo.
	
	if(confirm("�Confirma que quiere cancelar la carga de valores para este indicador? \n Si lo hace se perderan los cambios no guardados.")){
        _idind=_this.parentNode.parentNode.getAttribute('idindicadorcarga');
        accionCargaCancelar(_this);
                
        if(confirm("�Confirma que quiere modificar un indicador que ya est� publicado? \n Esto puede ser peligros, sobretodo y ya cuenta con valores cargados.")){
        	
	        document.getElementById('formEditarIndicadores').style.display='inline-block';
		    document.getElementById('divMenuAccionesCrea').style.display='inline-block';
		    document.getElementById('botonCrearIndicador').style.display='none';
		    document.getElementById('formSeleccionInd').style.display='none';
	        consultarIndicadorParaModificar(_idind);
	        
        }
    }
}


function renombrarGeometria(_this,_event){
	_event.preventDefault();
	if(_event.keyCode=='13'){
		_id=_this.parentNode.parentNode.getAttribute('idgeom');
		_nombre=_this.value;
		guardarNombreGeometria(_id,_nombre);
	}else{
		_this.setAttribute('editando','si');
	}
	
 }    	



function cargarFormularioNuevasGeometrias(_res){
	return;//esto parece residual

    _form=document.querySelector('#divPeriodoSeleccionado #listaUnidadesInd');
    _form.innerHTML='';
	document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data.campa.id);
    document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', '');
    document.querySelector('#divPeriodoSeleccionado #divMenuAccionesEditarValor #botonCrearGeom').style.display='inline-block';
    document.querySelector('#divPeriodoSeleccionado #divMenuAccionesEditarValor #botonDuplicarGeom').style.display='inline-block';
       
	//console.log( _res.data.geom);
    for(_ng in _res.data.geom){
    	_datg=_res.data.geom[_ng];
    	
    	_divg=document.createElement('div');
    	_divg.setAttribute('class','unidad');
    	_divg.setAttribute('idgeom',_datg.id);
    	_form.appendChild(_divg);
    	
    	_aelim=document.createElement('a');
    	_aelim.setAttribute('class','eliminar');
    	_aelim.setAttribute('onclick','eliminarGeom(this)');
    	_aelim.innerHTML='x';
    	_aelim.title='Elminar esta geometr�a de este per�odo';
    	_divg.appendChild(_aelim);
    	
    	
    	_nombre=document.createElement('h4');
    	_nombre.innerHTML='<input title="renombrar esta geometria" class="renombra" onkeyup="renombrarGeometria(this,event)" value="'+_datg.texto1+'">';
    	_divg.appendChild(_nombre);
    	for(_var in _res.data.indicador){
    		
    		if(
    			_var.substring(0,4)=='col_'
    			&&
    			_var.substring((_var.length-4),_var.length)=='_nom'
    			&&
    			_res.data.indicador[_var]!=null
    			){
    			
    			_campo=document.createElement('div');
		    	//_campo.setAttribute('class','elementoOculto');
		    	_idv=_var.replace('col_','');
		    	_idv=_idv.replace('_nom','');
		    	_idv=_idv.replace('texto','Texto');
		    	_idv=_idv.replace('numero','Numero');
		    	_idTit='indCarga'+_idv+'Nombre';
		    	_idInp='indCarga'+_idv+'Dato';
		    	_idv='carga'+_idv;
		    	_campo.setAttribute('id',_idv);
		    	_divg.appendChild(_campo);
		    	
		    	_titulo=document.createElement('div');
		    	_titulo.setAttribute('id',_idTit);
		    	_titulo.innerHTML=_res.data.indicador[_var];
		    	_campo.appendChild(_titulo);
		    	
		    	_input=document.createElement('input');
		    	_input.setAttribute('id',_idInp);
		    	_input.setAttribute('onkeyup','controlarCambiosinput(this)');
		    	_campo.appendChild(_input);
		    	if(_idInp.substring(8,5)=='Texto'){
		    		_input.setAttribute('type','text');
		    	}else{
		    		_input.setAttribute('class','number');
		    		_input.setAttribute('type','text');
		    		_input.setAttribute('step','any');
		    		
		    		_unimed=document.createElement('div');
		    		_idUnimed=_idInp.replace('Dato','Unidad');
			    	_unimed.setAttribute('id',_idUnimed);
			    	_campo.appendChild(_unimed);
		    	}
		    	
		    	_input.setAttribute('valororiginal','');
		    	if(_datg.valores[0]!=undefined){
			    	if(_datg.valores[0][_var.replace('_nom','_dato')]!=null){
			    		_input.value=_datg.valores[0][_var.replace('_nom','_dato')];
			    		_input.setAttribute('valororiginal',_datg.valores[0][_var.replace('_nom','_dato')]);
			    	}
		    	}
		    			    	
    		}
    	}
    }
    document.getElementById('divPeriodoSeleccionado').style.display='block';
 }
     



function cargarFormularioValoresMultiple(_res){
    _form=document.querySelector('#divPeriodoSeleccionado #listaUnidadesInd');
    _form.innerHTML='';
    
    document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data.indicador["id"]);
    document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', '');
	//console.log( _res.data.geom);
    for(_ng in _res.data.geom){
    	_datg=_res.data.geom[_ng];
    	
    	_divg=document.createElement('div');
    	_divg.setAttribute('class','unidad');
    	_divg.setAttribute('idgeom',_datg.id);
    	_form.appendChild(_divg);
    	
    	
    	_nombre=document.createElement('h4');
    	_nombre.innerHTML=_datg.texto1;
    	_divg.appendChild(_nombre);
    	for(_var in _res.data.indicador){
    		
    		if(
    			_var.substring(0,4)=='col_'
    			&&
    			_var.substring((_var.length-4),_var.length)=='_nom'
    			&&
    			_res.data.indicador[_var]!=null
    			){
    			
    			_campo=document.createElement('div');
		    	//_campo.setAttribute('class','elementoOculto');
		    	_idv=_var.replace('col_','');
		    	_idv=_idv.replace('_nom','');
		    	_idv=_idv.replace('texto','Texto');
		    	_idv=_idv.replace('numero','Numero');
		    	_idTit='indCarga'+_idv+'Nombre';
		    	_idInp='indCarga'+_idv+'Dato';
		    	_idv='carga'+_idv;
		    	_campo.setAttribute('id',_idv);
		    	_divg.appendChild(_campo);
		    	
		    	_titulo=document.createElement('div');
		    	_titulo.setAttribute('id',_idTit);
		    	_titulo.innerHTML=_res.data.indicador[_var];
		    	_campo.appendChild(_titulo);
		    	
		    	_input=document.createElement('input');
		    	_input.setAttribute('id',_idInp);
		    	_input.setAttribute('onkeyup','controlarCambiosinput(this)');
		    	_campo.appendChild(_input);
		    	if(_idInp.substring(8,5)=='Texto'){
		    		_input.setAttribute('type','text');
		    	}else{
		    		_input.setAttribute('class','number');
		    		_input.setAttribute('type','text');
		    		_input.setAttribute('step','any');
		    		
		    		_unimed=document.createElement('div');
		    		_idUnimed=_idInp.replace('Dato','Unidad');
			    	_unimed.setAttribute('id',_idUnimed);
			    	_campo.appendChild(_unimed);
		    	}
		    	
		    	_input.setAttribute('valororiginal','');
		    	if(_datg.valores[0]!=undefined){
			    	if(_datg.valores[0][_var.replace('_nom','_dato')]!=null){
			    		_input.value=_datg.valores[0][_var.replace('_nom','_dato')];
			    		_input.setAttribute('valororiginal',_datg.valores[0][_var.replace('_nom','_dato')]);
			    	}
		    	}
		    			    	
    		}
    	}
    }
    document.getElementById('divPeriodoSeleccionado').style.display='block';
 }
 
 function controlarCambiosinput(_this){
 	if(_this.value!=_this.getAttribute('valororiginal')){
 		_this.setAttribute('cambiado','si');
 	}else{
 		_this.removeAttribute('cambiado');
 	}
 }    	





function limpiarValorIndicador(){
    document.getElementById('indCargaTexto1Dato').value = null;
    document.getElementById('indCargaTexto2Dato').value = null;
    document.getElementById('indCargaTexto3Dato').value = null;
    document.getElementById('indCargaTexto4Dato').value = null;
    document.getElementById('indCargaTexto5Dato').value = null;

    document.getElementById('indCargaNumero1Dato').value = null;
    document.getElementById('indCargaNumero2Dato').value = null;
    document.getElementById('indCargaNumero3Dato').value = null;
    document.getElementById('indCargaNumero4Dato').value = null;
    document.getElementById('indCargaNumero5Dato').value = null;
}

function accionEditarValorEliminar(_this){
    var idindval = document.getElementById('divPeriodoSeleccionado').getAttribute('idindval');
    if (idindval != null && idindval != '' && idindval != 'undefined'){
        if(confirm("�Confirma que quiere eliminar este valor para el indicador? \n Si lo hace se guardar� registro en la papelera de los datos cargados en el formulario.")){
            //console.log('ve');

            eliminarValorIndicador(idindval);
            refrescarIndicadorActivo();
        }
    } else {
       console.log('no hay registro para borrar'); 
    }
}

/*
function seleccionarGeomDefault(_res){
    if (_res.data != null){
        for (var elem in _res.data['geom']){
            accionSeleccionarGeom(elem, _res);
            break; //Agarro el primero que encuentre
        }
    }
}
*/

function accionGeomSeleccionada(_idgeom){
    //document.getElementById('divPeriodoSeleccionado').setAttribute('idgeom', idgeom);
   
   	if(_DataGeom[_idgeom]==undefined){alert('faltan los datos de esa geometr�a, por favor reingrese aeste mapa');return;}
   	   
   	document.querySelector('#divCargaCampa .accionesCampa').style.display='none';
   	
    document.querySelector('#FormularioRegistro').style.display='block';
    document.querySelector('#FormularioRegistro [name="idgeom"]').value=_idgeom;
    document.querySelector('#FormularioRegistro #sect1').style.display="block";
    
    document.querySelector('#FormularioRegistro [name="t1"]').value=_DataGeom[_idgeom].t1;
    
    
    
    _val=_DataGeom[_idgeom].n1;
    if(_DataGeom[_idgeom].n1==null){
    	_val=_DataGeom[_idgeom].n1='0';
    }
    document.querySelector('#FormularioRegistro [name="n1"]').value=_val;
    $('#FormularioRegistro [name="n1"]').trigger('change');
    
    
    document.querySelector('#FormularioRegistro #autoria #usu').innerHTML='';
	document.querySelector('#FormularioRegistro #autoria #fecha').innerHTML=''; 
		    
    consultarRegistroGeom(_idgeom);
    //alert(_DataGeom[_idgeom].t1);
    
     	
}



function toglevalorSiNo(_this){
	_nom=_this.getAttribute('for');	
	if(_this.checked==true){
		_val=_this.getAttribute('valorsi');
	}else{
		_val=_this.getAttribute('valorno');
	}
	_this.parentNode.querySelector("[name='"+_nom+"']").value=_val;
}

function toglevalorSiNoRev(_this){
	_for=_this.getAttribute('name');
	_val=_this.value;
	_inp=_this.parentNode.querySelector('[for="'+_for+'"]');
	if(_inp.getAttribute('valorsi')==_val){
		_inp.checked=true;
	}else{
		_inp.checked=false;
	}
}

function limpiaBarra(_event){
	document.querySelector("#barrabusqueda input").value='';
	actualizarBusqueda(_event);
}

function actualizarBusqueda(_event){
	
	_input=document.querySelector("#barrabusqueda input");
	_str=_input.value;
	if(_str.length>=3){
		_input.parentNode.setAttribute('estado','activo');
	}else{
		_str='';
		_input.parentNode.setAttribute('estado','inactivo');
	}
	_str=_str.toLowerCase();
	console.log('buscando: '+_str);
	
	_lis=document.querySelectorAll('#listaindpublicadas > a.filaIndLista');
	
	for(_ln in _lis){
		if(typeof _lis[_ln] != 'object'){continue;}
		
		_contId=_lis[_ln].querySelector('#indIdLista');
		_contNom=_lis[_ln].querySelector('#indNombreLista');
		_contDes=_lis[_ln].querySelector('#indDescripcionLista');
		
		_cont=_contId.innerHTML+' '+_contNom.innerHTML+' '+_contNom.innerHTML;
		
		_cont=_cont.toLowerCase();
		
		_cont=_cont.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		_str=_str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
		
		if(_cont.toLowerCase().indexOf(_str)==-1){
			_lis[_ln].setAttribute('filtrado','si');
		}else{
			_lis[_ln].setAttribute('filtrado','no');
		}
	}
	
	/*
	buscarContratos();
	buscarWiki();
	buscarAntec();
	buscarTelef();*/
}


