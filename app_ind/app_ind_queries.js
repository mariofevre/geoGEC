/**
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
*/

function consultarPermisos(){
    var _IdMarco = getParameterByName('id');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco	
    };
    $.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            for(_na in _res.acc){
            	console.log(_res.acc[_na])
	          	procesarAcc(_res.acc[_na]);
            }
            if(_res.res!='exito'){
                alert('error al consultar la base de datos');
            }
        }
    });	
}
consultarPermisos();

function cargarListadoCapasPublicadas(){
    var _this = _this;
    
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_ind/app_ind_capa_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    cargarValoresCapasPublicadas(_res);
                    mostrarListadoCapasPublicadas();
                }else{
                    alert('error asf0jg434ff0gh');
                }
            }
    });
}

function cargarDatosCapaPublicada(idcapa){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': '1',
        'idcapa': idcapa
    };
    
    $.ajax({
            url:   './app_ind/app_ind_capa_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    cargarValoresCapaExist(_res);
                }else{
                    alert('error tf0jg44ff0gh');
                }
            }
    });
}

function cargarValoresCapaExistQuery(){
    var idCapa = document.getElementById('capaseleccionada').getAttribute('idcapa'); 
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'idcapa': idCapa
    };
    $.ajax({
        url:   './app_capa/app_capa_consultar_registros.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            
            _Features=_res.data;
            
            if(_res.res == 'exito'){
            	cargarFeatures();
            }
        }
    });
}


function  editarIndPublicado(_this){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    if (_res.data != null){
                        cargarValoresIndicadorExist(_res);
                    } else {
                        generarNuevoIndicadorQuery();
                    }
                }else{
                    alert('error asf0jg44f9ytfgh');
                }
            }
    });
}


function generarNuevoIndicador(){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    if (_res.data != null){
                        cargarValoresIndicadorExist(_res);
                    } else {
                        generarNuevoIndicadorQuery();
                    }
                }else{
                    alert('error asf0jg44f9ytfgh');
                }
            }
    });
}

function generarNuevoIndicadorQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    $.ajax({
            url:   './app_ind/app_ind_generar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    asignarIdIndicador(_res.data.id);
                }else{
                    alert('error asf0jg3444ffgh');
                }
            }
    });
}

function consultarIndicadorParaModificar(idindicador){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'id': idindicador
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    if (_res.data != null){
                        //TODO
                        cargarValoresIndicadorExist(_res);
                    }
                }else{
                    alert('error asf0jg44f9ytfgh');
                }
            }
    });
}

function editarInd(parametros){
    $.ajax({
            url:   './app_ind/app_ind_editar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    //Hacer algo luego de editar?
                }else{
                    alert('error al ejecutar la edición');
                }
            }
    });
}

function eliminarCandidatoIndicador(_this){
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    var _parametros = {
        'id': idindicador,
        'codMarco':_CodMarco
    };

    $.ajax({
    url:   './app_ind/app_ind_eliminar.php',
    type:  'post',
    data: _parametros,
    success:  function (response){
        var _res = $.parseJSON(response);
            //console.log(_res);	
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){
            	//cargarMapa();
                accionCreaCancelar();
            }
        }
    });
}

function publicarIndicador(_this){
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'id': idindicador
    };
    $.ajax({
        url:   './app_ind/app_ind_consultar_publicable.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            
            if(_res.res == 'exito'){
                publicarIndicadorQuery(_this);
            } else {
                var mensajeError = "El indicador no cumple los requisitos para publicar:\n";
                for(var _nm in _res.mg)
                {
                    mensajeError += _res.mg[_nm];
                }
                
                mensajeError = mensajeError.replace("\\n","\n").slice(0, -2);
                
                alert (mensajeError);
            }
        }
    });
}

function publicarIndicadorQuery(_this){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('formEditarIndicadores').getAttribute('idindicador'),
        'tipogeometria': document.querySelector('#tipodeometriaNuevaGeometria #inputTipoGeom').value
    };
    
    $.ajax({
            url:   './app_ind/app_ind_publicar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res == 'exito'){
                    accionCreaCancelar(_this);
                    alert("Indicador publicado");
                } else {
                    alert('error asf0jofvg24fcn02h');
                }
            }
    });
}

function cargarListadoIndicadoresPublicados(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    cargarValoresIndicadoresPublicados(_res, "accionIndicadorPublicadoSeleccionado");
                    mostrarListadoIndicadoresPublicados();
                }else{
                    alert('error asf0jg44ff0gh');
                }
            }
    });
}
/*
function cargarListadoIndicadoresPublicadosAModificar(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'nivelPermiso': '3'
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                if(_res.res=='exito'){
                    cargarValoresIndicadoresPublicados(_res, "accionIndicadorPublicadoSeleccionadoModificar");
                    mostrarListadoIndicadoresPublicados();
                }else{
                    alert('error asf0jg44ff0gh');
                }
            }
    });
}
*/
function refrescarIndicadorActivo(){
	
	
    var idindicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    cargarInfoIndicador(idindicador, ano, mes, 'false');
}

function refrescarDatosIndicadorActivo(_res){
	if(ano!=_res.data.ano){return;}
	if(mes!=_res.data.mes){return;}
	if(idIndicador!=_res.data.idIndicador){return;}
	
	_inps = document.querySelectorAll('#listaUnidadesInd unidad[idgeom="'+_res.data.id_p_ref_capas_registros+'"] input');
	for(_ni in _inps){
		if(typeof _inps[_ni] != 'obejct'){continue;}
		_inps[_ni].removeAttribute('cambiado');
	}
	
}

function cargarIndicadorPublicado(idIndicador, seleccionarFechaAno, seleccionarFechaMes){
    cargarInfoIndicador(idIndicador, seleccionarFechaAno, seleccionarFechaMes, 'true');
}


var _DataIndicador;
var _DataPeriodo=Array();
function cargarInfoIndicador(idIndicador, seleccionarFechaAno, seleccionarFechaMes, seleccionarDefault){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': '1',
        'id': idIndicador
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_estado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                
                for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
                //console.log(_res);
                if(_res.res=='exito'){
                	_DataIndicador=_res.data.indicador;
                    accionIndicadorPublicadoCargar(idIndicador, _res, seleccionarFechaAno, seleccionarFechaMes, seleccionarDefault);
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}

var _DataCapa=Array();
function cargarPoligonosIndicadorPublicado(idIndicador, ano, mes, seleccionarDefault){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var paramMes = 1;
    if (mes != null && mes > 0){
        paramMes = mes;
    }
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'id': idIndicador,
        'ano': ano,
        'mes': paramMes
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_indicador_geom.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                
                _DataCapa=_res.data.capa;
                
               	for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
	            
	            if(_DataIndicador.calc_buffer>0){
	            	consultarBuffer(_res.data.indicador.id,_res.data.periodo.ano,_res.data.periodo.mes);
	            }
	            
	            
                if(_res.res=='exito'){
                    dibujarPoligonosMapa(_res);
                    
                    if(_res.data.indicador.funcionalidad=='nuevaGeometria'){
                    	cargarFormularioNuevasGeometrias(_res);
                    }else{
                    	
                    	cargarFormularioValoresMultiple(_res);
                    }
                    
                    if(_res.data.indicador.funcionalidad=='geometriaExistente'){
                    	
                    	if(obtenerNombreMes(_res.data.periodo.mes-1)==undefined){_m='';}else{_m=obtenerNombreMes(_res.data.periodo.mes-1);}
                		_selector='#periodo > #selectorPeriodo #periodoFecha'+_m+_res.data.periodo.ano+' #valor';
                		console.log(_selector);
                		_divValor=document.querySelector(_selector); 
                		
                		
						_val=_res.data.resumen.sum_numero1;
						if(_val>100){
		        			_v=formatearNumero(_val,0);	
		        		}else{
		        			_v=formatearNumero(_val,2);	
		        		}
						_divValor.innerHTML='s: '+_v;
						
						_selector='#periodo > #selectorPeriodo #periodoFecha'+_m+_res.data.periodo.ano+' #porc';
                		console.log(_selector);
                		_divPorc=document.querySelector(_selector);
                		console.log(_divPorc);
                		
                		_val=_res.data.resumen.prom_numero1;
						if(_val>100){
	        				_v=formatearNumero(_val,0);	
		        		}else{
		        			_v=formatearNumero(_val,2);	
		        		}
						_divPorc.innerHTML='m: '+_v;
						
						
					}
                    /*
                    if (seleccionarDefault == 'true'){                    	
                        seleccionarGeomDefault(_res);
                    } else {
                        var idgeom = document.getElementById('divPeriodoSeleccionado').getAttribute('idgeom');
                        accionSeleccionarGeom(idgeom, _res);
                    }*/
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}


function consultarBuffer(idIndicador, ano, mes){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var paramMes = 1;
    if (mes != null && mes > 0){
        paramMes = mes;
    }
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada,
        'id': idIndicador,
        'ano': ano,
        'mes': paramMes
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_indicador_geom_buffer.php',
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
				
				
				
                if(_res.res=='exito'){
                	
                    dibujarBufferMapa(_res);
                
                
                	if(_res.data.capa_superp!=undefined){
                		dibujarCapaSuperp(_res);	
                		if(obtenerNombreMes(_res.data.periodo.mes-1)==undefined){_m='';}else{_m=obtenerNombreMes(_res.data.periodo.mes-1);}
                		_selector='#periodo > #selectorPeriodo #periodoFecha'+_m+_res.data.periodo.ano+' #valor';
                		console.log(_selector);
                		_divValor=document.querySelector(_selector);             
                		
                		_val=_res.data.intersec_sum;
                		
						if(_val>100){
		        			_v=formatearNumero(_val,0);	
		        		}else{
		        			_v=formatearNumero(_val,2);	
		        		}
						_divValor.innerHTML=_v;
						
						
						_selector='#periodo > #selectorPeriodo #periodoFecha'+_m+_res.data.periodo.ano+' #porc';
                		console.log(_selector);
                		_divPorc=document.querySelector(_selector);
                		console.log(_divPorc);
                		
						
						if(Number(_res.data.geom_superp_max.superp_max_numero1)>0){
							_val=Number(_res.data.intersec_sum*100/_res.data.geom_superp_max.superp_max_numero1);
							if(_val>10){
			        			_v=formatearNumero(_val,0);	
			        		}else{
			        			_v=formatearNumero(_val,2);
			        		}
							_divPorc.innerHTML=_v+'%';
						}
						
                	}    
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}
/*
function consultarFormularioValores(idIndicador, id_p_ref_capas_registros){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_superado': '0',
        'idIndicador': idIndicador,
        'id_p_ref_capas_registros': id_p_ref_capas_registros,
        'ano': ano,
        'mes': mes
    };
    
    $.ajax({
            url:  './app_ind/app_ind_val_consultar.php',
            type: 'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                if(_res.res=='exito'){
                    cargarFormularioValores(_res);
                }else{
                    alert('error asf89d0jg44f8f0d7gh');
                }
            }
    });
}

*/

function eliminarValorIndicador(idindval){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindval
    };
    
    $.ajax({
        url:   './app_ind/app_ind_val_eliminar.php',
        type:  'post',
        data: parametros,
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            if(_res.res=='exito'){
                limpiarValorIndicador();
                
                if (_res.data != null){
                    document.getElementById('divPeriodoSeleccionado').setAttribute('idindval', _res.data['id']);
                }
                
                var idgeom = document.getElementById('divPeriodoSeleccionado').getAttribute('idgeom');
                var idindicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
                
                consultarFormularioValores(idindicador, idgeom);
            }else{
                alert('error asf89d0j2jg44f8f0d7gh');
            }
        }
    });
}

function accionEditarValorGuardar(_this){
	
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    var idindval = document.getElementById('divPeriodoSeleccionado').getAttribute('idindval');
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
    var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idindval,
        'idIndicador': idIndicador,
        'ano': ano,
        'mes': mes,
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };
    
    _registros=document.querySelectorAll('#listaUnidadesInd input[cambiado="si"]');
    _envios=Array();
    for(_rn in _registros){
    	if(typeof _registros[_rn] != 'object'){continue;}
    	
    	_uni=_registros[_rn].parentNode.parentNode;
    	_idg=_uni.getAttribute('idgeom');
    	
    	_imps=_uni.querySelectorAll('input');
    	
    	_envios[_idg]=Array();
    	_envios[_idg]['id_p_ref_capas_registros']=_idg;
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
            url:   './app_ind/app_ind_val_editar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                	accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
                    //refrescarIndicadorActivo();
                    //refrescarDatosIndicadorActivo(_res);
                    
                }else{
                    alert('error asf89d0j2jg4u96d7gh');
                }
            }
	    });	    
    }
}

function guardarNuevaGeometria(_geometria,_idm,_elem){
	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'geometria':_geometria,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa': idCapa,        
        'renombre':'',
        'idCapa_registro_duplica': '',
        'idCapa_registro_elimina': '',
        'ano': ano,
        'mes': mes,
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };

	
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_editar.php',
		type:  'post',
		success: function (response){alert('error al consulta el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				mapa.removeInteraction(drawL);
				accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
				//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
			}else{
				alert('la solicitud no fue ejecutada');
			}
		}
	});
}


function eliminarGeom(_this){
	//elimina goemetría vinculada a un períono para un indicador.
	_idgeom=_this.parentNode.getAttribute('idgeom');
	
	if(!confirm('¿Confirma Eliminar esta geometría para este período?')){return;}

	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa_registro_elimina':_idgeom,
        'geometria':'',
        'renombre':'',
        'idCapa_registro_duplica':'',
        'idCapa': idCapa,
        'ano': ano,
        'mes': mes 
    };

	
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_editar.php',
		type:  'post',
		success: function (response){alert('error al consulta el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
				//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
			}else{
				alert('la solicitud no fue ejecutada');
			}
		}
	});	
}    


function guardarNombreGeometria(_idgeom,_nombre){
	//elimina goemetría vinculada a un períono para un indicador.
	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa_registro_elimina':'',
        'geometria':'',
        'renombre':_nombre,
        'idCapa_registro_renombre':_idgeom,
        'idCapa_registro_duplica':'',
        'idCapa': idCapa,
        'ano': ano,
        'mes': mes 
    };

	
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_editar.php',
		type:  'post',
		success: function (response){alert('error al consulta el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				document.querySelector('#listaUnidadesInd .unidad[idgeom="'+_res.data.id_geom_edit+'"] input.renombra').removeAttribute('editando');
				accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
			}else{
				alert('la solicitud no fue ejecutada');
			}
		}
	});	
}    


function accionCopiarGeometriaAnterior(_this){
	//para indicadores de funcionalidad nuevaGeometria, busca geometrías del período anterior al dado y las replica para este período.
	
	var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var idIndicador = document.getElementById('indicadorActivo').getAttribute('idindicador');
    if(_DataIndicador.id!=idIndicador){alert('error al formular envío');}
    var idCapa = _DataIndicador.id_p_ref_capasgeo;
    var ano = document.getElementById('divPeriodoSeleccionado').getAttribute('ano');
    var mes = document.getElementById('divPeriodoSeleccionado').getAttribute('mes');
    var fechaAhora = new Date();
    
	var _paramGral = {
        'codMarco': codMarco,
        'geometria':'',
        'idMarco': idMarco,
        'idIndicador': idIndicador,
        'idCapa': idCapa,
        'ano': ano,
        'mes': mes,
        'fechadecreacion': fechaAhora.toISOString().slice(0,10)        
    };

	
	$.ajax({
		data:_paramGral,
		url:   './app_ind/app_ind_geom_duplicar_periodo.php',
		type:  'post',
		success: function (response){alert('error al consulta el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
				//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);					
			}else{
				alert('la solicitud no fue ejecutada');
			}
		}
	});
}