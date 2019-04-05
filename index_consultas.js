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

//Esta funcion obtiene el QueryString en base a su nombre de la url, es case insensitive
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var results = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", 'i').exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

//funciones para consultar datos y mostrarlos
var _Tablas={};
var _TablasConf={};
var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);


function actualizarPermisos(){
	//repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
	consultarTablas();
}

function consultarTablas(){
	document.querySelector('#menutablas #lista').innerHTML='';
	consultarElemento();//limpia residuos de visualización de elementos;
        
    var _Est = getParameterByName('est');
        
	var _parametros = {
		'selecTabla':_SelecTabla,
		'selecElemCod':_SelecElemCod,
		'selecElemId':_SelecElemId		
	};
	
	$.ajax({
		url:   'consulta_tablas.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
				//console.log(_res);
				_Tablas=_res.data.tablas;
				_TablasConf=_res.data.tablasConf;
				_cont=document.querySelector('#menutablas #lista');
				for(_nn in _Tablas['est']){			
					
					_aaa=document.createElement('a');
					_aaa.innerHTML=_Tablas['est'][_nn];
					if(_TablasConf[_Tablas['est'][_nn]] != undefined){
						_aaa.innerHTML+=' - '+_TablasConf[_Tablas['est'][_nn]].nombre_humano;
					}
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_aaa.setAttribute('class','nombretabla');
					_aaa.setAttribute('onclick','mostrartabla(this)');
					_cont.appendChild(_aaa);
					
					
					if(_TablasConf[_Tablas['est'][_nn]].acceso>=3){
						//boton cargar version
						_aaa=document.createElement('a');
						_aaa.innerHTML='<img src="./img/editar.png" alt="editar">';
						_aaa.title='subir una nueva versión';
						_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
						_aaa.setAttribute('onclick','cargarAtabla(this)');
						_cont.appendChild(_aaa);
					}					
					
					if(_TablasConf[_Tablas['est'][_nn]].acceso>=3){
						//boton configurar
						_aaa=document.createElement('a');
						_aaa.innerHTML='<img src="./img/configurar.png" alt="configurar">';
						_aaa.title='confiturar capa';
						_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
						_aaa.setAttribute('onclick','configurartabla(this)');
						_cont.appendChild(_aaa);
					}
					
					_aaa=document.createElement('a');
					
					_standarSHP="ows?service=WFS&version=1.0.0&request=GetFeature&maxFeatures=1000000&outputFormat=SHAPE-ZIP";
					_capaSHP="&typeName=geogec:"+_Tablas['est'][_nn];
					
					_aaa.setAttribute('onclick','descargarSHP(this,event)');
					
					_host="http://190.111.246.33:8080/geoserver/geoGEC/";
						
					_aaa.setAttribute('link',_host+_standarSHP+_capaSHP);
					_aaa.setAttribute('link',_host+_standarSHP+_capaSHP);//retiramos el recorte para la descarga
					
					
					_aaa.innerHTML='<img src="./img/descargar.png" alt="descargar">';
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_cont.appendChild(_aaa);
										
					_aaa=document.createElement('br');
					_cont.appendChild(_aaa);
					
				}
				
			if(_Est!=null && _Est!=''){
				console.log(_Est);
				mostrartabla(document.querySelector('#lista > a.nombretabla[tabla="'+_Est+'"]'));
			}
		
				
		}
	});
}


					
function cargarAtabla(_this){
	limpiarfomularioversion();
	document.getElementById('formcargaverest').style.display='block';
	document.getElementById('formcargaverest').setAttribute('tabla',_this.getAttribute('tabla'));
}


function mostrartabla(_this){	
	_lyrElemSrc.clear();
	//document.querySelector('#titulomapa').style.display='block';
	document.querySelector('#menuelementos').style.display='block';
	_tabla=_this.getAttribute('tabla');
	consultarElemento();//limpia datos ya consultados de elementos puntuales dentro de una tabla;
	document.querySelector('#titulomapa #tnombre').innerHTML=_tabla;
	if(_TablasConf[_tabla]!='undefined'){
		document.querySelector('#titulomapa #tnombre_humano').innerHTML=_TablasConf[_tabla].nombre_humano;
		document.querySelector('#titulomapa #tdescripcion').innerHTML=_TablasConf[_tabla].resumen;
	}
	
	mostrarTablaEnMapa(_tabla);	
	consultarCentroides(_tabla);
}


	
function consultarCentroides(_tabla){
	
	_parametros={
		'tabla': _tabla
	};
	
	$.ajax({
		data: _parametros,
		url:   './consulta_centroides.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
				
			}else{
				//cargaContrato();	
				_lyrCentSrc.clear();
				_cont=document.querySelector('#menuelementos #lista');
				_cont.innerHTML='';
				
				
				for(_no in _res.data.centroidesOrden){					
					_nc=_res.data.centroidesOrden[_no];
					_hayaux='no';						
					_dat=_res.data.centroides[_nc];				
					
					_aaa=document.createElement('a');
					_aaa.setAttribute('centid',_dat.id);
					_aaa.setAttribute('cod',_dat.cod);
					_aaa.innerHTML='<span class="nom">'+_dat.nom+"</span>"+'<span class="cod">'+_dat.cod+"</cod>";
					_aaa.setAttribute('onclick','consultarElemento("0","'+_dat.cod+'","'+_res.data.tabla+'")');
					if(_dat.geo!=null){		
						var format = new ol.format.WKT();				
					    var _feat = format.readFeature(_dat.geo, {
					        dataProjection: 'EPSG:3857',
					        featureProjection: 'EPSG:3857'
					    });
					    _feat.setId(_dat.id);
					    _feat.setProperties({
					    	'nom':_dat.nom,
					    	'cod':_dat.cod,
					    	'id':_dat.id,
					    });
				    
						_lyrCentSrc.addFeature(_feat);						
						_lyrCent.setSource(_lyrCentSrc);
						
						_MapaCargado='si';
						
						_aaa.setAttribute('onmouseover','resaltarcentroide(this)');
						_aaa.setAttribute('onmouseout','desaltarcentroide(this)');
					}else{
						_aaa.innerHTML+='<span class="alert" title="sin geometría">!</span>';
					}
					
					_cont.appendChild(_aaa);
									
				}
				
				if(_MapaCargado=='si'){
					
					_ext= _lyrCentSrc.getExtent();
					mapa.getView().fit(_ext, { duration: 1000 });
					
				}
				
				if(_Cod != ''){							
					consultarElemento("0",_Cod,_Est);					
				}
				
			}
		}
	})			
}	
function resaltarcentroide(_this){
	
	var _src = _lyrCent.getSource();
	_centid=_this.getAttribute('centid');
	_feat=_src.getFeatureById(_centid);
	
	  _feat.setStyle(_CentSelStyle);
	    _pp=_feat.getProperties('nom');
	    document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
		document.querySelector('#tseleccion').innerHTML=_pp.nom;
		document.querySelector('#tseleccion').style.display='inline-block';
}
function desaltarcentroide(_this){	
	var _src = _lyrCent.getSource();
	_centid=_this.getAttribute('centid');
	_feat=_src.getFeatureById(_centid);
	
	  _feat.setStyle(_CentStyle);
	    document.querySelector('#tseleccion').setAttribute('cod','');
		document.querySelector('#tseleccion').innerHTML='';
		document.querySelector('#tseleccion').style.display='none';
}

function descargarSHP(_this,_ev){	

	_ev.stopPropagation();
	_if=document.createElement('iframe');
	_this.appendChild(_if);
	
	_if.style.display='none';
	_if.onload = function() { alert('myframe is loaded'); }; 
	
	_im=document.createElement('img');
	//_this.appendChild(_im);
	_im.src='./img/cargando.gif';
	
	_if.src=_this.getAttribute('link');
}

function consultarElemento(_idElem,_codElem,_tabla){
	
	document.querySelector('#menudatos #titulo').innerHTML='';
	document.querySelector('#menudatos #lista').innerHTML='';
	document.querySelector('#menudatos').removeAttribute('style');
	document.querySelector('#menuacciones #titulo').innerHTML='';
	document.querySelector('#menuacciones #lista').innerHTML='';
	document.querySelector('#menuacciones').removeAttribute('style');
	
	
	
	_elems = document.querySelectorAll('#menuelementos #lista a[cargado="si"]');
	if(_elems!=null){
	for(_nn in _elems){
		if(typeof _elems[_nn] != 'object'){continue;}
		_elems[_nn].removeAttribute('cargado');
	}
	}
	
	if(_codElem==null){return;}
	
	document.querySelector('div#cuadrovalores').setAttribute('cargado','si');
	
	_parametros = {
		'id': _idElem,
		'cod': _codElem,
		'tabla':_tabla
	};
	
	$.ajax({
		data: _parametros,
		url:   './consulta_elemento.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			for(_nm in _res.mg){
				alert(_res.mg[_nm]);
			}
			if(_res.res=='exito'){		
				
				_campocod=_res.data.tablasConf.campo_id_geo;
				_camponom=_res.data.tablasConf.campo_id_humano;
				_campodesc=_res.data.tablasConf.campo_desc_humano;
								
				document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
				document.querySelector('#menuacciones #titulo').innerHTML="acciones disponibles";
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
				
				
				document.querySelector('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').setAttribute('cargado','si');	
				
				_pe=$('#menuelementos #lista').offset().top;
				_sc=document.querySelector('#menuelementos #lista').scrollTop;
				console.log($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc);
				
				$('#menuelementos #lista').animate({
				        scrollTop: ($('#menuelementos #lista [centid="'+_res.data.elemento.id+'"]').offset().top+_sc-_pe)
				 }, 2000);
				    
				document.querySelector('#menudatos').style.display='block';
				
				
				_lyrElemSrc.clear();
				if(_res.data.elemento.geotx!=null){
				
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
					
					_ext= _lyrElemSrc.getExtent();
				
					setTimeout(
						function(){mapa.getView().fit(_ext, { duration: 1000 })},
							200
					);
				}	
									
				//generarItemsHTML();		
				//generarArchivosHTML();
			}else{
				alert('error dsfg');
			}
		}
	})	
}