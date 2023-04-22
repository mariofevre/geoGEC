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


	
function consultarPermisos(){
	
	
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
	
    _cn = consultasPHP_nueva('./sistema/sis_consulta_permisos.php');
	$.ajax({
        url:   './sistema/sis_consulta_permisos.php',
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			
			request._data = {'cn':_cn};
		  
		},
        error:  function (request, status, errorThrown){	

			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);		
			
		},
		success:  function (response, status, request){
			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;			
            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
            if(_res.res!='exito'){return;}
            
            
        }
 	});	
}

function consultarUsuaries(){
	_parametros = {
		'codMarco':_CodMarco,	
		'accion':'app_rele'
	}
    _cn = consultasPHP_nueva('./usuarios/acc_consulta_compas.php');
	$.ajax({
        url:   './usuarios/acc_consulta_compas.php',
        type:  'post',
        data: _parametros,
        beforeSend: function(request, settings) { 
			
			request._data = {'cn':_cn};
		  
		},
        error:  function (request, status, errorThrown){	

			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);		
			
		},
		success:  function (response, status, request){
			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;			
            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
            if(_res.res!='exito'){
            	alert('error al consultar la base de datos');
            	return;
            }
            _DataUsuaries=_res.data;
        }
 	});	
}





function consultarListadoRaster(){
		
	_parametros = {
		'codMarco':_CodMarco
	}
	_cn = consultasPHP_nueva('./app_raster/app_raster_consulta_listado.php');
	
	$.ajax({
		url:   './app_raster/app_raster_consulta_listado.php',
		type:  'post',
		data: _parametros,
        beforeSend: function(request, settings) { 
			
			request._data = {'cn':_cn};
		  
		},
        error:  function (request, status, errorThrown){	

			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);		
			
		},
		success:  function (response, status, request){
			
			var _res = $.parseJSON(response);            
            _cn = request._data.cn;			
            consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
            
            _DataRaster['listado']=_res.data;
            
            mostrarListadoRaster();
		}
	});	
	
}

function accionCargarDocsCandidatosRaster(){
        
    var parametros = {
        'codMarco': _CodMarco
    };
	_cn = consultasPHP_nueva('./app_docs/app_docs_consulta_externa.php');
    
    $.ajax({
		url:   './app_docs/app_docs_consulta_externa.php',
		type:  'post',
		data: parametros,
		beforeSend: function(request, settings) { 
			
			request._data = {'cn':_cn};
		  
		},
		error:  function (request, status, errorThrown){	

			_cn = request._data.cn;
			consultasPHP_respuesta("err",_cn);		
			
		},
		success:  function (response, status, request){
			
			var _res = $.parseJSON(response);            
			_cn = request._data.cn;			
			consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
			
			 if(_res.res!='exito'){return;}
			
			_DataRaster['candidatos']=_res.data;
							
			document.querySelector('#divCandidatosDocRaster #listado_candidatos').innerHTML='';
						  
				//cargarListadoCampas(_res);
			for(_np in _res.data.orden.psdir){
				
				_id_psdir=_res.data.orden.psdir[_np];
				
				_dat_psdir=_res.data.psdir[_id_psdir];
				
					
				_titulocreado='no'; //solo lo creamos si tene archivos candidatos.
				
				for(_id_arch in _dat_psdir.archivos){
					//console.log(_id_arch);
					_dat_arch=_dat_psdir.archivos[_id_arch];
					
					//console.log(_dat_arch.nombre);
					_cumpleinicio='no';
					
					_inicio='S2A_MSIL2A';
					if(_inicio == _dat_arch.nombre.substring(0, _inicio.length)){
						_cumpleinicio='si';
						console.log('cumple inicio');
					}
					
					_inicio='S2B_MSIL2A';
					if(_inicio == _dat_arch.nombre.substring(0, _inicio.length)){
						_cumpleinicio='si';
						console.log('cumple inicio');
					}
					
					_cumpleextension='no';
					
					_ext='zip';
					_s=_dat_arch.nombre.split('.');
					
					if(_ext == _s[_s.length-1]){
						_cumpleextension='si';
						console.log('cumple extension');
					}
					
					
					
					if(_cumpleextension=='si' && _cumpleinicio=='si'){
					
						if(_titulocreado=='no'){
							_h3=document.createElement('h3');
							_h3.innerHTML=_dat_psdir.nombre;
							document.querySelector('#divCandidatosDocRaster #listado_candidatos').appendChild(_h3);
							_titulocreado='si';
						}
					
						_a=document.createElement('a');
						_a.setAttribute('onclick','procesarDocARaster("'+_id_arch+'")');
						_a.setAttribute('proceso','no');
						_a.setAttribute('iddoc',_id_arch);
						_a.innerHTML='<img class="ok" src="./img/check-sinborde.png">';
						_a.innerHTML+='<img class="cargando" src="./img/cargando.gif">';
						_a.innerHTML+=_dat_arch.nombre;
						
						document.querySelector('#divCandidatosDocRaster #listado_candidatos').appendChild(_a);
					}
					
				}					
				
			}
		}
    });
}



function procesarTodosCandidatosDocRaster(){
	
		_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [proceso="no"]');
		_a.setAttribute('proceso','procesando');
	
		_parametros = {
			'codMarco':_CodMarco,
			'idraster':'0',
			'iddoc':_a.getAttribute('iddoc')
		}
		
		_cn = consultasPHP_nueva('./app_raster/app_raster_proc_procesar_archivo.php');
		
		$.ajax({
			url:   './app_raster/app_raster_proc_procesar_archivo.php',
			type:  'post',
			data: _parametros,
			beforeSend: function(request, settings) { 
				
				request._data = {'cn':_cn};
			  
			},
			error:  function (request, status, errorThrown){	

				_cn = request._data.cn;
				consultasPHP_respuesta("err",_cn);		
				
			},
			success:  function (response, status, request){
				
				var _res = $.parseJSON(response);            
				_cn = request._data.cn;			
				consultasPHP_respuesta("exito",_cn,_res.mg,_res.res);
				
				 if(_res.res!='exito'){return;}
				
		
				_iddoc=_res.data.iddoc;
				
				_a=document.querySelector('#divCandidatosDocRaster #listado_candidatos [iddoc="'+_iddoc+'"]');
				_a.setAttribute('proceso','localizada');
				 procesarTodosCandidatosDocRaster();
			
			}
		});	

	
}
        
        
        
/*
function accionCargarNuevaCapaRaster(){
		
	_parametros = {
		'codMarco':_CodMarco,
		'idraster':'1'
	}
	* _cn = consultasPHP_nueva('./app_raster/app_raster_proc_procesar_archivo.php');
	$.ajax({
		url:   './app_raster/app_raster_proc_procesar_archivo.php',
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
*/
