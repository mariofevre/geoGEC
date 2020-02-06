/**
*
* funciones js para ejecutar consultas
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
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


//funciones para consultar datos y mostrarlos
var _Tablas={};
var _TablasConf={};
var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);


var _IdMarco = getParameterByName('id');
var _CodMarco = getParameterByName('cod');
	
function consultarPermisos(){
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
	$.ajax({
        url:   './sistema/sis_consulta_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            }
        }
 	});	
}
consultarPermisos();

var _DataUsuaries={};
function consultarUsuaries(){
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
	$.ajax({
        url:   './usuarios/acc_consulta_compas.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            }
            _DataUsuaries=_res.data;
        }
 	});	
}
consultarUsuaries();



function cargarListadoCampa(){
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    $.ajax({
            url:   './app_rele/app_rele_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    cargarListadoCampas(_res);
                }else{
                    alert('error asf0jg434ff0gh');
                }
            }
    });
}

function generarNuevaCampaQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    $.ajax({
        url:   './app_rele/app_rele_generar.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            if(_res.res=='exito'){
                asignarIdCampa(_res.data.id);
            }else{
                alert('error asf0jg3444ffgh');
            }
        }
    });
}

function asignarIdCampa(_idCampa){
    document.getElementById('formEditarCampa').setAttribute('idcampa', _idCampa);
}


var _DataRele={};

function cargarDatosCampa(_idcampa){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
  
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idcampa': _idcampa
    };
    
    $.ajax({
        url:   './app_rele/app_rele_consultar.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            _res = $.parseJSON(response);
            if(_res.res!='exito'){alert('error asf0jg44f9ytfgh');return;}
            
            _DataRele=_res.data;
            cargarCamposFormulario();
            cargarRegistrosCampa(_res.data.id);
            cargarUnidadesdeAnalisis(_res.data.id);   
            document.querySelector('#FormularioNuevaUA').style.display='block';
            document.querySelector('#divCargaCampa').style.display='block';
        }
    });
}



function consultarCampaDefinicion(idcampa){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idcampa
    };
    
    $.ajax({
        url:   './app_rele/app_rele_consultar.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            if(_res.res!='exito'){
            	alert('error asf0jg44f9ytfgh');
            	return;
            }
            
            if (_res.data != null){
            	_DataRele=_res.data;
                //TODO
                cargarUnidadesdeAnalisis(_res.data.id);   
            }   
        }
    });
}


function cargarRegistrosCampa(_idcampa){
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcampa': _idcampa,
            'zz_superado':'0'
    };
    $.ajax({
        url:   './app_rele/app_rele_consultar_registros.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg){
				alert(_res.mg[_nm]);
			}
			
			document.querySelector('#divCargaCampa').style.display='block';
			//document.querySelector('#divCargaCampa .accionesCampa').style.display='none';
			_Features=_res.data;
			
            if(_res.res == 'exito'){
            	cargarFeatures();
            }
        }
    });
}

function consultarRegistroGeom(_idregcapa){
	var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcampa': _DataRele.id,
            'idregistrocapa':_idregcapa
    };
    $.ajax({
        url:   './app_rele/app_rele_consultar_registros_campos.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
        	try {
		        JSON.parse(response);
		    }catch(_err){
		        console.log(_err);
		        alert('el servidor entregó un texto de formato inesperado');
		        document.querySelector('body').innerHTML+=response;
		        return;
		    }
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg){
				alert(_res.mg[_nm]);
			}
			if(_res.res!='exito'){
				alert('error al intentar consultar la UA');
			}
			
			if(_res.data==null){
				cargarCamposFormulario();//limpia el formulario
				return;	
			}else{
				cargarCamposFormulario();//limpia el formulario
			}
			
			
			
			document.querySelector('#FormularioRegistro #autoria #usu').innerHTML='';
			document.querySelector('#FormularioRegistro #autoria #fecha').innerHTML=''; 
		    
			
		    _idusu= _res.data.registro.zz_auto_crea_usu;
		    if(_DataUsuaries.usu[_idusu]!=undefined){
			    _du=_DataUsuaries.usu[_idusu];
			    document.querySelector('#FormularioRegistro #autoria #usu').innerHTML=_du.nombre+' '+_du.apellido+' ('+_idusu+')';
			
				var date = new Date(_res.data.registro.zz_auto_crea_fechau*1000);
				var hours = date.getHours();
				var minutes = "0" + date.getMinutes();
				var seconds = "0" + date.getSeconds();
				var formattedTime =  ' '+date.getDate()+'/'+(1+date.getMonth())+'/'+date.getFullYear()+' ('+hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2)+')';
    
			    document.querySelector('#FormularioRegistro #autoria #fecha').innerHTML= formattedTime; 
		    }
					
			
			for(_idcampo in _res.data.campos){
				_tipo=_DataRele.campos[_idcampo].tipo;
				if(_tipo=='texto'){
					_valor=	_res.data.campos[_idcampo].data_texto;
				}else if(_tipo=='numero'){
					_valor=	_res.data.campos[_idcampo].data_numero;
				}else if(_tipo=='coleccion_imagenes'){
					_valor=	_res.data.campos[_idcampo].data_documento;
					
					document.querySelector('#listadoDocumentos[idcampo="'+_idcampo+'"]').innerHTML='';
					if(_valor!=''){
						_docs=$.parseJSON(_valor);
						for(_dn in _docs){
							_ruta=_docs[_dn].ruta;
							_nombre=_docs[_dn].nombre;
							_iddoc=_docs[_dn].iddoc;
							cargaDeFoto(_nombre,'',_idcampo,_iddoc,_ruta);
						}	
					}
					
				}
				document.querySelector('#campospersonalizados [name="'+_idcampo+'"]').value=_valor;
				$('#campospersonalizados [name="'+_idcampo+'"]').trigger('change');//esto genera el efeco onchange que de otra forma no ocurre por cambiar dinamicametne el valor de un input.
			}
        }
    });	
}

function cargaDeFoto(_nombre,_con,_idcampo,_iddoc,_ruta){
	
	_lista=document.querySelector('#listadoDocumentos[idcampo="'+_idcampo+'"]');
	
    _ppp=document.createElement('p');
    _ppp.innerHTML='<span id="nombre">'+_nombre+'</span>';
    _ppp.setAttribute('ncont',_con);
    _ppp.setAttribute('class','carga');
    _ppp.setAttribute('nombre',_nombre);
    _ppp.setAttribute('iddoc','');
    
    _e=_nombre.split('.');
    _ppp.setAttribute('extension',_e[(_e.length)-1]);
    
    
    if(_ruta!=''){
    	_img=document.createElement('img');
    	_img.setAttribute('src',_ruta);
    	_ppp.appendChild(_img);
    }
	
	_lista.appendChild(_ppp);
	
}	



var _DataCapa=Array();
function cargarUnidadesdeAnalisis(_idCampa){
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'idcampa': _idCampa
    };
    
    $.ajax({
            url:   './app_rele/app_rele_consultar_geom.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                
                _DataCapa=_res.data.capa;
                _DataGeom=_res.data.geom;
                
               	for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
	            
	            
                if(_res.res=='exito'){
                	
                    dibujarReleMapa(_res);
                    
                    
                    cargarFormularioNuevasGeometrias(_res);
                    
                    
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}


           
function borrarGeometrias(_modo,_idgeom){
	
	if(_modo=='todos'){
		if(!confirm('esto va a borrar TODAS las geometrías de este relevamiento, cargada por cualquier usuarie.')){
			return;
		}
	}else if(_modo=='propios'){
		if(!confirm('esto va a borrar las geometrías que vos cargagaste en este relevamiento.')){
			return;
		}
	}else if(_modo=='registro'){
		if(_idgeom==null){alert('error');return;}
		_source_rel_sel.clear();
		if(!confirm('esto va a borrar la geometría seleccionada. (iddb:'+_idgeom+')')){
			return;
		}
	}else{
		alert('error');
		return;
	}
	
	 var parametros = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'modo': _modo,
        'idcampa': _DataRele.id
    };
    
    if(_modo=='registro'){
		 parametros['idgeom'] = _idgeom
	}
    
    $.ajax({
        url:   './app_rele/app_rele_borrar_geom.php',
        type:  'post',
        data: parametros,
        success:  function (response){   
            var _res = $.parseJSON(response);
           	for(var _nm in _res.mg){alert(_res.mg[_nm]);}            
            for(var _na in _res.acc){procesarAcc(_res.acc[_na]);}
            if(_res.res!='exito'){alert('error al procesar la base de datos');return;}
            
            cargarUnidadesdeAnalisis(_DataRele.id);                
        }
    });
	
}

function enviarDatosRegistro(){
	
	_idgeom=document.querySelector('#FormularioRegistro [name="idgeom"]').value;	
	_t1=document.querySelector('#FormularioRegistro [name="t1"]').value;
	_n1=document.querySelector('#FormularioRegistro [name="n1"]').value;
	
	
	_inputs=document.querySelectorAll('#FormularioRegistro #campospersonalizados input');
	
	_personalizados={};
	for(_in in _inputs){
		if(typeof _inputs[_in] != 'object'){continue;}
		_name=_inputs[_in].getAttribute('name');
		if(_name==null){continue;}
		_personalizados[_name]=_inputs[_in].value;
	}
	
	_ta=document.querySelectorAll('#FormularioRegistro #campospersonalizados textarea');
	for(_tan in _ta){
		if(typeof _ta[_tan] != 'object'){continue;}
		_name=_ta[_tan].getAttribute('name');
		if(_name==null){continue;}
		_personalizados[_name]=_ta[_tan].value;
	}
		
	var parametros = {
        'codMarco': _CodMarco,
        'idMarco': '',
        'idcampa': _DataRele.id,
        'idgeom':_idgeom,
        't1':_t1,
        'n1':_n1,
        'personalizados':_personalizados
    };
	
    
    $.ajax({
        url:   './app_rele/app_rele_cargar_registro.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            
            
           	for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            
            for(var _na in _res.acc){
                procesarAcc(_res.acc[_na]);
            }
            
            
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            	return;
            }
            
            //TODO remplazar la carga total por la incorporación del dato editado.    
			cargarRegistrosCampa();
            cargarDatosCampa(_DataRele.id);
            document.querySelector('#FormularioRegistro').style.display='none';
			_source_rel_sel.clear();
        }
    });
    
    
	_idnuevageom=document.querySelector('#FormularioRegistro #nuevageometria').getAttribute('idgeom');
	if(_idnuevageom==_idgeom){
		var parametros = {
	        'codMarco': _CodMarco,
	        'idMarco': '',
	        'idcapa': _DataCapa.id,
	        'idgeom':_idgeom,
	        'tipogeom':'Polygon',//TODO corregir este hardcoded
	        'geomtx': document.querySelector('#FormularioRegistro #nuevageometria').value
	    };
	     $.ajax({
	        url:   './app_capa/app_capa_editar_registro.php',
	        type:  'post',
	        data: parametros,
	        success:  function (response)
	        {   
	            var _res = $.parseJSON(response);
	            //console.log(_res);
	            
	            if(_res.res!='exito'){
	            	alert('error al guardar la nueva geometría');
	            }
	            
	        }
	    });
	}	
}


function enviarCreaRegistroCapa(){

	var parametros = {
	        'codMarco': _CodMarco,
	        'idMarco': '',
	        'idcapa': _DataCapa.id,
	        'tipogeom':'Polygon',//TODO corregir este hardcoded
    };
     $.ajax({
        url:   './app_capa/app_capa_crear_registro.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            
            if(_res.res!='exito'){
            	alert('error al guardar la nueva geometría');
            	return;
            }
            
            _DataGeom[_res.data.idgeom]={
            		'id': _res.data.idgeom, 
            		'geotx': "", 
            		'estadocarga': "sin dibujar",
            		't1':'',
            		'n1':''
            };
            accionGeomSeleccionada(_res.data.idgeom);
            cambiarGeometria(_res.data.idgeom);            
        }
    });
}
     
function accionEditarValorGuardar(_this){
	
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    
    var idindval = document.getElementById('divPeriodoSeleccionado').getAttribute('idindval');
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
    
    var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindval,
        'idrele':_IdRele        
    };
    
    
    _registros=document.querySelectorAll('#listaVaraiblesRele input[cambiado="si"]');
    _envios=Array();
    for(_rn in _registros){
    	if(typeof _registros[_rn] != 'object'){continue;}
    	
    	_uni=_registros[_rn].parentNode.parentNode;
    	_idg=_uni.getAttribute('idgeom');
    	
    	_imps=_uni.querySelectorAll('input');
    	
    	_envios[_idg]=Array();
    	_envios[_idg]['id_p_ref_campa_registros']=_idg;
    	for(_in in _imps){
    		if(typeof _imps[_in] != 'object'){continue;}
    		
    		_campo = _imps[_in].getAttribute('id');
    		_campo = _campo.replace('indCarga','col_');
    		_campo = _campo.replace('Texto','texto');
    		_campo = _campo.replace('Numero','numero');
    		_campo = _campo.replace('Dato','_dato');
    		
    		_envios[_idg][_campo]=_imps[_in].value;
    	}
    }
    
    
    for(_idg in _envios){    
    	
    	parametros = _paramGral;
    	for(_campo in  _envios[_idg]){
    		parametros[_campo]=_envios[_idg][_campo];    	
    	}

	    $.ajax({
            url:   './app_rele/app_rele_val_editar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res!='exito'){
                	alert('error al procesas rl pedido');
                	return;
                }
                	
            	accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
                
            }
	    });	    
    }
}


function guardarDXFfondo(_imgs){
	
	alert("Generaremos un dxf con un raster georreferenciado con el mapa visible en este momento.\n Al estar georeferenciado, se pueden dibujar sobre este polígonos como UA para cargarlos directamente.");
	
	var parametros = _imgs
    parametros['idcampa']=_DataRele.id;
     $.ajax({
        url:   './app_rele/app_rele_generar_dxf_captura.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            
            if(_res.res!='exito'){
            	alert('error al guardar captura de pantalla');
            	return;
            }
            
            _if=document.createElement('iframe');
			_if.style.display='none';
			_if.src=_res.data.descarga;
			document.querySelector('body').appendChild(_if);
            
        }
    });
}


