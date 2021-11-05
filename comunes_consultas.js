/**
*
* funciones js para ejecutar consultas desde index
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


var _ConsultaActiva='no';

function controlConsultando(){
	if(_ConsultaActiva=='si'){
		return true;
	}else{
		return false;
	}	
}

function activarConsultando(){
	_ConsultaActiva='si';	
}

function desactivarConsultando(){
	_ConsultaActiva='no';	
}


function consultarElementoAcciones(_idElem,_codElem,_tabla){
	/*
	document.querySelector('#menudatos #titulo').innerHTML='';
	document.querySelector('#menudatos #lista').innerHTML='';
	document.querySelector('#menudatos').removeAttribute('style');
	document.querySelector('#menuacciones #titulo').innerHTML='';
	document.querySelector('#menuacciones #lista').innerHTML='';
	document.querySelector('#menuacciones').removeAttribute('style');
	*/
	
	/*
	_elems = document.querySelectorAll('#menuelementos #lista a[cargado="si"]');
	if(_elems!=null){
	for(_nn in _elems){
		if(typeof _elems[_nn] != 'object'){continue;}
		_elems[_nn].removeAttribute('cargado');
	}
	}
	
	if(_codElem==null){return;}
	
	document.querySelector('div#cuadrovalores').setAttribute('cargado','si');*/
	

	_parametros = {
		'id': _idElem,
		'cod': _codElem,
		'tabla':_tabla
	};
	
		
	if(controlConsultando()){alert('Antes tiene que resolverse una consulta en curso');return;}
	activarConsultando();
	
	$.ajax({
		data: _parametros,
		url:   './consulta_elemento.php',
		type:  'post',
		success:  function (response){
			desactivarConsultando();
			var _res = $.parseJSON(response);
			console.log(_res);
			for(_nm in _res.mg){
				alert(_res.mg[_nm]);
			}
			if(_res.res=='exito'){		
				
				_campocod=_res.data.tablasConf.campo_id_geo;
				_camponom=_res.data.tablasConf.campo_id_humano;
				_campodesc=_res.data.tablasConf.campo_desc_humano;
					
								
				/*document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
				document.querySelector('#menuacciones #titulo').innerHTML="acciones disponibles";*/
				_lista=document.querySelector('#menuacciones #lista');
					
				for(_accnom in _res.data.tablasConf.acciones){
					
					_accndata=_res.data.tablasConf.acciones[_accnom];
					
					if(_res.data.elemento.accesoAccion[_accnom]>0){
					
						document.querySelector('#menuacciones').style.display='block';
						_li=document.createElement('a');
						_li.setAttribute('href','./'+_accnom+'.php?cod='+_res.data.elemento[_campocod]);
						_la=document.createElement('img');
						_la.setAttribute('src','./img/'+_accnom+'.png');
						_la.setAttribute('alt',_accnom);
						_la.setAttribute('title',_accndata.resumen);
						_li.appendChild(_la);
						_lista.appendChild(_li);
						
					}
				}
				/*
				document.querySelector('#menudatos').style.display='block';
				
				
				document.querySelector('#menudatos #titulo').innerHTML=_res.data.elemento[_camponom];
				
				
				if(_campodesc==null){
					_desc='';
				}else{
					_desc=_res.data.elemento[_campodesc];
				}
				document.querySelector('#menudatos #descripcion').innerHTML=_desc;
				
				
				_lista=document.querySelector('#menudatos #lista');	
				for(_nd in _res.data.elemento){
					if(_nd == 'geo'){continue;}
					if(_nd == 'accesoAccion'){continue;}
					if(_nd == 'acceso'){continue;}
					if(_nd == 'geotx'){continue;}
					if(_nd == 'zz_obsoleto'){continue;}
					if(_nd == 'zz_accesolibre'){continue;}
					
					_li=document.createElement('div');
					_li.setAttribute('class','fila');
					_la=document.createElement('label');
					_la.setAttribute('class','variable');
					_la.innerHTML=_nd+":";
					_li.appendChild(_la);
					_sp=document.createElement('div');
					_sp.setAttribute('class','dato');
					_sp.innerHTML=_res.data.elemento[_nd];
					_li.appendChild(_sp);
					_lista.appendChild(_li);	
					
				}
				
				_lyrElemSrc.clear();
				var format = new ol.format.WKT();	
			    var _feat = format.readFeature(_res.data.elemento.geotx, {
			        dataProjection: 'EPSG:3857',
			        featureProjection: 'EPSG:3857'
			    });
			    
			    _feat.setId(_res.data.elemento.id);
			    
			    _feat.setProperties({
			    	'nom':_res.data.elemento[_camponom],
			    	'cod':_res.data.elemento[_campocod],
			    	'id':_res.data.elemento.id
			    });
			    
				_lyrElemSrc.addFeature(_feat);
				
				_MapaCargado='si';
					
					
				document.querySelector('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').setAttribute('cargado','si');	
				
				_pe=$('#menuelementos #lista').offset().top;
				_sc=document.querySelector('#menuelementos #lista').scrollTop;
				console.log($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc);
				
				$('#menuelementos #lista').animate({
				        scrollTop: ($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc-_pe)
				 }, 2000);
				    
				document.querySelector('#menudatos').style.display='block';
							
				_ext= _lyrElemSrc.getExtent();
				
				
				setTimeout(
					function(){mapa.getView().fit(_ext, { duration: 1000 })},
						200
					);
					
				*/	
									
				//generarItemsHTML();		
				//generarArchivosHTML();
			}else{
				alert('error dsfg');
			}
		}
	})	
}