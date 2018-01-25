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

//funciones para consultar datos y mostrarlos

var _Tablas={};
var _TablasConf={};
var _SelecTabla='';//define si la consulta de nuevas tablas estará referido al elmento existente de una pabla en particular; 
var _SelecElemCod=null;//define el código invariable entre versiones de un elemento a consultar (alternativa a _SelElemId);
var _SelecElemId=null;//define el id de un elemento a consultar (alternativa a _SelElemCod);

function consultarTablas(){
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
							
					_aaa=document.createElement('a');
					_aaa.innerHTML='<img src="./img/editar.png" alt="editar">';
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_aaa.setAttribute('onclick','cargarAtabla(this)');
					_cont.appendChild(_aaa);
					
					_aaa=document.createElement('a');
					_aaa.innerHTML='<img src="./img/configurar.png" alt="configurar">';
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_aaa.setAttribute('onclick','configurartabla(this)');
					_cont.appendChild(_aaa);
					
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
				
		}
	});
}
consultarTablas();			

					
function cargarAtabla(_this){
	limpiarfomularioversion();
	document.getElementById('formcargaverest').style.display='block';
	document.getElementById('formcargaverest').setAttribute('tabla',_this.getAttribute('tabla'));
}


function mostrartabla(_this){	
	document.querySelector('#titulomapa').style.display='block';
	_tabla=_this.getAttribute('tabla');
	document.querySelector('#titulomapa #tnombre').innerHTML=_tabla;
	if(_TablasConf[_tabla]!='undefined'){
		document.querySelector('#titulomapa #tnombre_humano').innerHTML=_TablasConf[_tabla].nombre_humano;
		document.querySelector('#titulomapa #tdescripcion').innerHTML=_TablasConf[_tabla].resumen;
	}
	
	_ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
	        'VERSION': '1.1.1',
	        tiled: true,
	        LAYERS: _tabla,
	        STYLES: '',
        }
   });
	La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
	consultarCentroides(_this);
}

function consultarCentroides(_this){
	_parametros={
		'tabla': _this.getAttribute('tabla')
	}

	
	$.ajax({
		data: _parametros,
		url:   'consulta_centroides',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);			
			console.log(_res);
			for(_nm in _res.mg){alert(_res.mg[_nm]);}
			if(_res.res=='err'){
			}else{
				//cargaContrato();	
				_lyrCentSrc.clear();
				
				for(_nc in _res.data.centroides){
						
					_hayaux='no';
						
					_dat=_res.data.centroides[_nc];
					
					var format = new ol.format.WKT();
				
				    var _feat = format.readFeature(_dat.geo, {
				        dataProjection: 'EPSG:3857',
				        featureProjection: 'EPSG:3857'
				    });
				    _feat.setProperties({
				    	'nom':_dat.nom,
				    	'cod':_dat.cod,
				    	'id':_dat.id,
				    });
				    
					_lyrCentSrc.addFeature(_feat);						
					_lyrCent.setSource(_lyrCentSrc);
					
					_MapaCargado='si';
				
				}
			}
		}
	})			
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
	if(_codElem==null){return;}
	
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
				
				document.querySelector('#menuacciones #titulo').innerHTML=_res.data.elemento.nombre;
				document.querySelector('#menuacciones #titulo').innerHTML="acciones disponibles";
				_lista=document.querySelector('#menuacciones #lista');	
				for(_nd in _res.data.tablasConf.acciones){
					document.querySelector('#menuacciones').style.display='block';
					_li=document.createElement('a');
					_li.setAttribute('href','./'+_res.data.tablasConf.acciones[_nd]+'.php?cod='+_res.data.elemento[_campocod]);
					_la=document.createElement('img');
					_la.setAttribute('src','./img/app_docs.png');
					_la.setAttribute('alt','app_docs');
					_la.setAttribute('title','gestor de documentos internos');
					_li.appendChild(_la);
					_lista.appendChild(_li);
				}
				document.querySelector('#menudatos').style.display='block';
				
				
				document.querySelector('#menudatos #titulo').innerHTML=_res.data.elemento[_camponom];
				_lista=document.querySelector('#menudatos #lista');	
				for(_nd in _res.data.elemento){
					if(_nd == 'geo'){continue;}
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
				document.querySelector('#menudatos').style.display='block';
				//generarItemsHTML();		
				//generarArchivosHTML();
			}else{
				alert('error dsfg');
			}
		}
	})	
}