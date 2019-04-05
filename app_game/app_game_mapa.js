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


var _src={};
var _lyr={};

//GAME layer de geometría del buffer Zero
_src['bufferzero']= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
_lyr['bufferzero']= new ol.layer.Vector({
	name: 'bufferzero',
    source: _src['bufferzero'],
    style: new ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(117, 25, 8)',  width: 1,  lineDash: [4,4]}),
		 zIndex:101
	})       
});
mapa.addLayer(_lyr['bufferzero']);


//layer de superposicion a layer buffer
var _source_ind_superp= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_ind_superp= new ol.layer.Vector({
	name: 'buffer',
    source: _source_ind_superp,
    style: ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
		 fill: new ol.style.Fill({color: 'rgb(0,0,0)'}),
		 zIndex:1
	})    
});
mapa.addLayer(_layer_ind_superp);

//layer de geometría del indicador
var _source_ind= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_ind= new ol.layer.Vector({
	name: 'indicador',
    source: _source_ind,
    style: ol.style.Style({
		 zIndex:100
	})       
});
mapa.addLayer(_layer_ind);

var _source_ind_sel= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});


//layer de geometría seleccionadad para cargar datos
var _st_ind_sel=new ol.style.Style({
     image: new ol.style.Circle({
	       stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 8}),
	       radius: 6
	 }),
	 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 20}),
	 zIndex:200
});
var _layer_ind_sel= new ol.layer.Vector({
	name: 'indicador: elemento selecto',
    source: _source_ind_sel,
    style: _st_ind_sel
});
mapa.addLayer(_layer_ind_sel);


//layer de area de influencia de la geometría
var _source_ind_buffer= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_ind_buffer= new ol.layer.Vector({
	name: 'buffer',
    source: _source_ind_buffer,
    style: ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)', width: 0.5}),
		 fill: new ol.style.Fill({color: 'rgb(0,0,0)'}),
		 zIndex:2
	})    
});
mapa.addLayer(_layer_ind_buffer);


//GAME layer de geometría cubierta por la geometria ejecutada en el juego
_src['game_cubierto']= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
_lyr['game_cubierto']= new ol.layer.Vector({
	name: 'game_cubierto',
    source: _src['game_cubierto'],
    style: new ol.style.Style({
    	image: new ol.style.Circle({	fill: new ol.style.Fill({color: 'rgba(255,102,0,1)'}),	stroke: new ol.style.Stroke({color: '#ff3333',width: 0.8}), radius: 6 }),
		fill: new ol.style.Fill({color: 'rgba(255,102,0,0.5)'}),
		stroke: new ol.style.Stroke({color: 'rgb(80, 0, 0)',  width: 1}),
		zIndex:101
	})       
});
mapa.addLayer(_lyr['game_cubierto']);


//GAME layer de geometría del buffer Preliminar
_src['bufferpre']= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
_lyr['bufferpre']= new ol.layer.Vector({
	name: 'bufferzero',
    source: _src['bufferzero'],
    style: new ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(117, 25, 8)',  width: 1,  lineDash: [4,4]}),
		 zIndex:102
	})       
});
mapa.addLayer(_lyr['bufferpre']);

//alert('o');


var _encuadrado='no';

var _mapaEstado ='';
//alert(_mapaEstado);
mapa.on('pointermove', function(evt){
	//alert(_mapaEstado);
	if(_mapaEstado=='terminado'){return;}
	if(_mapaEstado=='ejecutando'){
		preliminarGeom(evt,'prelim');
		return;
	}
	
});

mapa.on('click', function(evt){   
	
	console.log(_mapaEstado);
	if(_mapaEstado=='terminado'){return;}
	if(_mapaEstado=='ejecutando'){
		preliminarGeom(evt,'click');		
		return;
	}
	
	if(_mapaEstado=='change'){
		console.log('detenido');		
		_mapaEstado='detenido';
		return;
	}
	
		
  	if(_mapaEstado=='detenido'){
  		//_mapaEstado='ejecutando'
  		//console.log('click estando detenido');
  		preliminarGeom(evt,'click');
  		
  		_mapaEstado='ejecutando'
		return;
  	}
});



function dibujarBufferMapa(_res){
    //console.log('inicia funcion'); 
	//console.log(_res.data.geom);
	//console.log(_source_ind.getFeatures());
    _source_ind_buffer.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	for(_gn in _res.data.geom){		
		//console.log('aaa');
		_geo=_res.data.geom[_gn];
		_val=null;
		_haygeom='si';		
		
		//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_geo.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });

		
		_ft.setStyle(new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: 'rgba(0,0,0,0)'}),
			 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
			 zIndex:1
		}));
		
	    //_ft.setProperties(_geo);	    
	   	_source_ind_buffer.addFeature(_ft); 
	   		
	}
	
	if(_haygeom=='si'){
		_ext= _source_ind_buffer.getExtent();	
		//console.log(_ext);
		if(_encuadrado=='no'){
			mapa.getView().fit(_ext, { duration: 1000 });
			_encuadrado='si';
		}
	}
	//geometryOrExtent

}


var xmlDoc;
function dibujarCapaSuperp(_res){
	_source_ind_superp.clear();
    if (_res.data != null){
        var capaQuery = _res.data.capa_superp;
		_features = _res.data.geom_superp;
		_capa = _res.data.capa_superp;
        //Operaciones para leer del xml los valores de simbologia
        var xmlSld = capaQuery["sld"];
		
		//console.log('representando capa superpuesta');
        if (xmlSld && xmlSld != ''){
            var colorRelleno = '';
            var transparenciaRelleno = '';
            var colorTrazo = '';
            var anchoTrazo = '';

            
            if (window.DOMParser){
                parser = new DOMParser();
                xmlDoc = parser.parseFromString(xmlSld, "text/xml");
            }else{ // Internet Explorer
                xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async = false;
                xmlDoc.loadXML(xmlSld);
            }
            
            _rules= xmlDoc.getElementsByTagName("Rule");	
		    _condiciones=Array();
		    
		    if(Object.keys(_rules).length>1){
			    for(_rn in _rules){	
					_larule = _rules[_rn];
					if(typeof _larule != 'object'){continue;}
					_algo=_larule.getElementsByTagName("Fill")[0];
					//console.log(_algo);
					var _mayorIgualQue = _larule.getElementsByTagName("ogc:PropertyIsGreaterThanOrEqualTo")[0];   			
			        for(var node in _mayorIgualQue.childNodes){		            	
			            if (_mayorIgualQue.childNodes[node].nodeName == "ogc:PropertyName"){
			                _campoMM = _mayorIgualQue.childNodes[node].textContent;
			            }
			            if (_mayorIgualQue.childNodes[node].nodeName == "ogc:Literal"){
			                _valorMM = _mayorIgualQue.childNodes[node].textContent;
			            }
			        }
			        var _menorQue = _larule.getElementsByTagName("ogc:PropertyIsLessThan")[0];   			
			        for(var node in _menorQue.childNodes){		            	
			            if (_menorQue.childNodes[node].nodeName == "ogc:PropertyName"){
			                _campomm = _menorQue.childNodes[node].textContent;
			            }
			            if (_menorQue.childNodes[node].nodeName == "ogc:Literal"){
			                _valormm = _menorQue.childNodes[node].textContent;
			            }
			        }
			        var xmlFill = _larule.getElementsByTagName("Fill")[0];						
					for(var node in xmlFill.childNodes){
			            if (xmlFill.childNodes[node].nodeName == "CssParameter" 
			                    && xmlFill.childNodes[node].getAttribute("name") == "fill"){
			                colorRelleno = xmlFill.childNodes[node].textContent;
			                //console.log(colorRelleno);
			            }
			            if (xmlFill.childNodes[node].nodeName == "CssParameter"
			                    && xmlFill.childNodes[node].getAttribute("name") == "fill-opacity"){
			                transparenciaRelleno = xmlFill.childNodes[node].textContent;
			                 //console.log(transparenciaRelleno);
			            }
			        }
			        var xmlStroke = _larule.getElementsByTagName("Stroke")[0];
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
			        
			        _con={
			        	'campoMM':_campoMM,
			        	'valorMM':_valorMM,
			        	'campomm':_campomm,
			        	'valormm':_valormm,
			        	'colorRelleno':colorRelleno,
			        	'transparenciaRelleno':transparenciaRelleno,
			        	'colorTrazo':colorTrazo,
			        	'anchoTrazo':anchoTrazo
			        }
			        _condiciones.push(_con);
				}
			}
			
			//console.log('o'+_features);
			
		    for(var elem in _features){
				//console.log('feat:'+elem);
		        var format = new ol.format.WKT();	
		        var _feat = format.readFeature(_features[elem].geom_intersec, {
		            dataProjection: 'EPSG:3857',
		            featureProjection: 'EPSG:3857'
		        });
		
		        _feat.setId(_features[elem].id);
		
		        _feat.setProperties({
		            'id':_features[elem].id
		        });
		        
		        
		        if(_condiciones.length>0){
					_datasec=_features[elem];
					for(_k in _datasec){
						_kref=_k.replace('texto','nom_col_text');
						_kref=_kref.replace('numero','nom_col_num');
						//console.log(_kref+' - '+_capa[_kref] +' vs ' +_campoMM);
						if(_capa[_kref] == _campoMM){
							_campoMM =_k; 
							//console.log('eureka. ahora: '+_campoMM);
							break;
						}		
					}
					
					for(_k in _datasec){
						
						_kref=_k.replace('texto','nom_col_text');
						_kref=_kref.replace('numero','nom_col_num');
						//console.log(_kref+' - '+_capa[_kref] +' vs ' +_campoMM);
						if(_capa[_kref] == _campomm){
							_campomm =_k; 
							//console.log('eureka. ahora: '+_campomm);
							break;
						}	
								
					}
				}
				//console.log(_features[elem][_campoMM] +' >= '+_valorMM+'&&'+_features[elem][_campomm]+' < '+ _valormm);
				
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
			        //console.log('lll:'+_st);
			        for(_nc in _condiciones){
						if(
							Number(_features[elem][_campoMM]) >= Number(_condiciones[_nc].valorMM)
							&&
							Number(_features[elem][_campomm]) <  Number(_condiciones[_nc].valormm)
						){
							_c= hexToRgb(_condiciones[_nc].colorRelleno);
							//console.log(_condiciones[_nc].transparenciaRelleno);
					        _n=(1 - (_condiciones[_nc].transparenciaRelleno));
					        _rgba='rgba('+_c.r+', '+_c.g+', '+_c.b+', '+_n+')';
					
					        _st= new ol.style.Style({
					          fill: new ol.style.Fill({
					            color: _rgba
					
					          }),
					          stroke: new ol.style.Stroke({
					            color: _condiciones[_nc].colorTrazo,
					            width: _condiciones[_nc].anchoTrazo
					          }),
					          zIndex:1
					        });
						}
					}
		        	_feat.setStyle(_st);
		
		        _source_ind_superp.addFeature(_feat);
		    }
		}else{
			_st= new ol.style.Style({
		          fill: new ol.style.Fill({
		            color: 'rgba(250, 200, 100, 0.5)'
		
		          }),
		          stroke: new ol.style.Stroke({
		            color: 'rgba(255, 100, 50, 1)',
		            width: '1'
		          })
	        });
			
			for(var elem in _features){
				//console.log('feat:'+elem);
		        var format = new ol.format.WKT();	
		        var _feat = format.readFeature(_features[elem].geom_intersec, {
		            dataProjection: 'EPSG:3857',
		            featureProjection: 'EPSG:3857'
		        });
		
		        _feat.setId(_features[elem].id);
		
		        _feat.setProperties({
		            'id':_features[elem].id
		        });
		        
		        
	        	_feat.setStyle(_st);
		
		        _source_ind_superp.addFeature(_feat);
			}
		}
    }
}


function accionSeleccionarGeom(idgeom, _res){
	
	_source_ind_sel.clear();

    //TODO
    
    //Aqui el codigo para seleccionar una feature del mapa, pero por codigo y no con click 
    //(o sea que no voy a saber que pixel se apreta sino la geometria seleccionada)
    
    accionGeomSeleccionada(idgeom, _res);
}

var drawL={};
var _nnelem=0;

var _drawL_coords=Array();
var _drawL_largoacum=0;
var _lprev=0;
var _abortarFeature=0;//valor 1 aborta la creacion del feature.

function accionEditarCrearGeometria(){    
	_mapaEstado='ejecutando';
	_drawL_coords=Array();	
	//_typeGeom=_DatosCapaRes.tipogeometria;
	_typeGeom='LineString';
	mapa.removeInteraction(drawL);
		
    
    
    drawL = new ol.interaction.Draw({
        source: _source_ind_sel,
        type: _typeGeom
    });
            
    mapa.addInteraction(drawL); 
		
	_source_ind_sel.on('change', function(evt){
		
		if(_mapaEstado=='validando'){return;}
		
		console.log('change');		
		_drawL_coords=Array();
		//console.log('vaciado el listado');
		_mapaEstado='change';
		//console.log('change');		
		_drawL_largoacum=_lprev;
		ValidarGeom();
		/*
		_features=_source_ind_sel.getFeatures();
		var format = new ol.format.WKT();
		_geometria=format.writeGeometry(_features[0].getGeometry());
		
		
		_nnelem++;		
		//guardarNuevaGeometria(_geometria,_nnelem);
		
		_clon=_features[0].clone();
		_source_ind.addFeature(_clon);
		_clon.setId('nn'+_nnelem);
		
		_source_ind_sel.clear();
	*/
	});	
}

function zoomArea(){
	_feats=_sMarco.getFeatures();
	
	for(_f in _feats){
		
		_view.fit(
			_feats[_f].getGeometry(), 
			{
				duration: 3000	
			}	
		);
	}
}



function bufferAIprevia(){

	 var parser = new jsts.io.OL3Parser();
    parser.inject(Point, LineString, LinearRing, Polygon, MultiPoint, MultiLineString, MultiPolygon);

    for (var i = 0; i < features.length; i++) {
      var feature = features[i];
      // convert the OpenLayers geometry to a JSTS geometry
      var jstsGeom = parser.read(feature.getGeometry());

      // create a buffer of 40 meters around each line
      var buffered = jstsGeom.buffer(40);

      // convert back from JSTS and replace the geometry on the feature
      feature.setGeometry(parser.write(buffered));
    }

    source.addFeatures(features);
}


var _BufferZeroCoord=Array();
var _BufferZeroFeature={};
function SeguirCursorBufferZero(_nuevaCoord){
	
	//console.log('coord:'+_BufferZeroCoord);
	//console.log('ncoord:'+_nuevaCoord);
	
	_deltax=_nuevaCoord[0]-_BufferZeroCoord[0];
	_deltay=_nuevaCoord[1]-_BufferZeroCoord[1];
	
	//console.log('deltas:'+_deltax+','+_deltay)
	
	if(_bufferzerodisponible=='no'){return;}
	_BufferZeroFeature.getGeometry().translate(_deltax, _deltay);
	
	_BufferZeroCoord=_nuevaCoord;
	//console.log('ncoord act:'+_BufferZeroCoord);
}

var _BufferPreCoord=Array();
var _BufferPreFeature={};
function SeguirBufferPrevio(){
	
	_feat=_source_ind_sel.getFeatures();
	
	 var parser = new jsts.io.OL3Parser();
     parser.inject(Point, LineString, LinearRing, Polygon, MultiPoint, MultiLineString, MultiPolygon);

    
    _src['bufferpre'].clear();  
	for(_ff in _feat){
		  // convert the OpenLayers geometry to a JSTS geometry
          var jstsGeom = parser.read(_feat[_ff].getGeometry());
          
          // create a buffer of 40 meters around each line
          var buffered = jstsGeom.buffer(200);
          //TODO parametrizar distancia buffer
          
          // convert back from JSTS and replace the geometry on the feature
          _ft.setGeometry(parser.write(buffered));
          
          _src['bufferpre'].addFeature(_ft);
	}
	//console.log('coord:'+_BufferZeroCoord);
	//console.log('ncoord:'+_nuevaCoord);
	
}


var _bufferzerodisponible='no';
function DibujarBufferZero(_geo_buffer_centroide_tx){
	
	_format = new ol.format.WKT();	
	_BufferZeroFeature = _format.readFeature(_geo_buffer_centroide_tx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });
	
	_src['bufferzero'].clear();
	_src['bufferzero'].addFeature(_BufferZeroFeature);
	_bufferzerodisponible='si';
	
	mapa.on('pointermove', function(evt){
		
		if(_mapaEstado=='terminado'){return;}   
        
        //console.log(evt);
        _nuevaCoord = evt.coordinate;
        
        SeguirCursorBufferZero(_nuevaCoord);

  	});

}

/////
//tomado de stackoverflow: https://stackoverflow.com/a/6853926
///////
function pDistance(x, y, x1, y1, x2, y2) {

  var A = x - x1;
  var B = y - y1;
  var C = x2 - x1;
  var D = y2 - y1;

  var dot = A * C + B * D;
  var len_sq = C * C + D * D;
  var param = -1;
  if (len_sq != 0) //in case of 0 length line
      param = dot / len_sq;

  var xx, yy;

  if (param < 0) {
    xx = x1;
    yy = y1;
  }
  else if (param > 1) {
    xx = x2;
    yy = y2;
  }
  else {
    xx = x1 + param * C;
    yy = y1 + param * D;
  }

  var dx = x - xx;
  var dy = y - yy;
  return Math.sqrt(dx * dx + dy * dy);
}
///////////////



function preliminarGeom(_evt,_tipo){
	
	_l=0

	if(_drawL_coords.length=='0'){
		
		if(_tipo=='click'){
			
			_abortarFeature=0;
			
			_px=_evt.coordinate[0];
			_py=_evt.coordinate[1];
			
			//VERIFICACION DE CUMPLIMIENTO DE RED
			_feats=_source_ind_sel.getFeatures();
			
			_mindist=100000;//valor de referencia para iniciar el min para identificar refecto red;
			_n=0;
			
			featloop:
			for(_fn in _feats){
				_n++;
				_geom=_feats[_fn].getGeometry();
				
				
				
				_geom.forEachSegment(function(start, end){					
					_dist=pDistance(_px,_py,start[0],start[1],end[0],end[1])/1.23; //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
					_mindist=Math.min(_mindist,_dist);
						
		        });
		        
				
			}
			//console.log('mindist:'+_mindist);
			if(_n>0){
				//esta no es la primer feature.
				if(_DataSesion.modored=='1'){
					//el modo red debe impedir nuevos elementos lejos de los viejos.
					_maxdist=_DataIndicador.calc_buffer*0.3;//limite admisible en modo red (distancia de un nuevo elemento a los preexistentes);
					if(_mindist>_maxdist){
						_abortarFeature=1;						
						alert('Este punto está muy lejos de la red existente!');
					}	
				}
			}
			
			//console.log('se incorpora coordenada a listado vacio');
			_drawL_coords.push(_evt.coordinate);
		}
		return;
	}
	if(_mapaEstado=='detenido'){return;}
	
	_x=_drawL_coords[0][0];
	_y=_drawL_coords[0][1];
	
	for (i = 1; i < _drawL_coords.length; i++){
		//console.log('_l:'+_l);
		_x=_drawL_coords[i][0];
		_y=_drawL_coords[i][1];
	  	_deltax=_drawL_coords[i][0]-_drawL_coords[i-1][0];
	  	_deltay=_drawL_coords[i][1]-_drawL_coords[i-1][1];
	  
	  	_dist=Math.sqrt(Math.pow(_deltax,2)+ Math.pow(_deltay,2));
  		_dist=_dist/1.23; //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
  	
	  	_l+=Math.round(_dist);
	}
	if(_tipo=='click'){
		_drawL_coords.push(_evt.coordinate);
	}
	
		
	if(_drawL_coords.length=='0'){return;}
	_deltax=_evt.coordinate[0]-_x;
  	_deltay=_evt.coordinate[1]-_y;
  	_dist=Math.sqrt(Math.pow(_deltax,2)+ Math.pow(_deltay,2));
  	_dist=_dist/1.23; //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
  	_l+=Math.round(_dist);
  	_lprev=_drawL_largoacum+_l  	
  	_porc=100*_lprev/_DataSesion.limiteunitarioporturno;
  	document.querySelector('#limite #barra #avan').style.width=Math.min(100,_porc)+'%';
  	document.querySelector('#limite #porc').innerHTML=Math.round(_porc)+'%'+' '+_lprev+'m';
  	
  	if(_abortarFeature=='1'){
  		document.querySelector('#limite').setAttribute('estado','alerta');
	  	document.querySelector('#limite #tx').innerHTML='¡Empieza muy lejos!';
  	}else{
	  	if(_lprev>_DataSesion.limiteunitarioporturno){
	  		document.querySelector('#limite').setAttribute('estado','alerta');
	  		document.querySelector('#limite #tx').innerHTML='¡Límite Sobrepasado!';
	  	}else{
	  		document.querySelector('#limite').setAttribute('estado','normal');
	  		document.querySelector('#limite #tx').innerHTML='..propuesta válida...';
	  	}
  	}
	
}	
	



function ValidarGeom(){
	_mapaEstado='validando';
	_l=0;
	_estadoLimite='ok';
	_feats=_source_ind_sel.getFeatures();
	
	console.log('feature definido');
	//console.log(_feats.length);
	//console.log(_feats[_feats.length-1]);
	if(_abortarFeature=='1'){
		_source_ind_sel.removeFeature(_feats[_feats.length-1]);
	}
	
	_feats=_source_ind_sel.getFeatures();
	
	featloop:
	for(_fn in _feats){
		
		_geom=_feats[_fn].getGeometry();
		
		var _format = new ol.format.WKT();
		_geometriatx=_format.writeGeometry(_geom);
		
		_geometriatx=_geometriatx.replace('LINESTRING(','');
		_geometriatx=_geometriatx.replace(')','');
		_drawL_val_coords=_geometriatx.split(',');
		
		for (i = 1; i < _drawL_val_coords.length; i++){
			_coi=_drawL_val_coords[i].split(' ');
			_coi_1=_drawL_val_coords[i-1].split(' ');

		  	_deltax=_coi[0]-_coi_1[0];
		  	_deltay=_coi[1]-_coi_1[1];
		  
		  	_dist=Math.sqrt(Math.pow(_deltax,2)+ Math.pow(_deltay,2));
	  		_dist=_dist/1.23; //TODO ajustar este factor al valr correspondeinte por la proyección en esta zona.
	  	
		  	_l+=Math.round(_dist);
		  	
		  	if(_l>_DataSesion.limiteunitarioporturno){
				
				
				if(i==1){
					_source_ind_sel.removeFeature(_feats[_fn]);
					continue featloop;
				}
						  		
		  		_coordsCortas=Array();
		  		
		  		for (ib = 0; ib < i; ib++){
		  			_coib=_drawL_val_coords[ib].split(' ');		
		  			_coordsCortas.push(_coib);  
				}
				
				_geomCorta=new ol.geom.LineString(_coordsCortas);
				_feats[_fn].setGeometry(_geomCorta);
				_l-=Math.round(_dist);
		  		_drawL_largoacum=_l
		  		
		  		break;
		  	}
		}
		_lprev=_l;
		_drawL_largoacum=_lprev;
		_porc=100*_l/_DataSesion.limiteunitarioporturno;
	  	document.querySelector('#limite #barra #avan').style.width=Math.min(100,_porc)+'%';
	  	document.querySelector('#limite #porc').innerHTML=Math.round(_porc)+'%'+' '+_l+'m';  
	  	document.querySelector('#limite').setAttribute('estado','normal');
  		document.querySelector('#limite #tx').innerHTML='..propuesta válida...';

	}
	
	
	_mapaEstado='change';
}	
	
function pasarTurno(){
		if(document.querySelector('#pasar').getAttribute('estado')=='inactivo'){return;}
		
		_feats=_source_ind_sel.getFeatures();
	
		_arr_wkt=Array();
		for(_fn in _feats){
			_geom=_feats[_fn].getGeometry();
			var _format = new ol.format.WKT();
			_geometriatx=_format.writeGeometry(_geom);
			_arr_wkt.push(_geometriatx);
		}
				
		guardarGeometriaTurno(_arr_wkt,_nnelem);
		
		
		_mapaEstado='terminado';
		_source_ind_sel.clear();
}

	
function guardarGeometriaTurno(){ 
	
	var _param = {
		'codMarco': _CodMarco,
        'idMarco': _IdMarco,
        'arr_wkt': _arr_wkt,
        'idSesion': _IdSesion,
        'partida':_Partida        
    };

	
	$.ajax({
		data:_param,
		url:   './app_game/app_game_guardar_turno.php',
		type:  'post',
		success: function (response){alert('error al consulta el servidor');},
		success:  function (response){
			var _res = $.parseJSON(response);
			if(_res.res=='exito'){
				mapa.removeInteraction(drawL);
				//accionPeriodoElegido(_DataPeriodo.ano, _DataPeriodo.mes, 'false');
				//accionIndicadorPublicadoSeleccionado('',_res.data.idInd);		
				
				_Partida=_res.data.nid.partida;
				_drawL_largoacum=0;
				document.querySelector('#limite #barra #avan').style.width='1%';
			  	document.querySelector('#limite #porc').innerHTML=Math.round(_porc)+'0%'+' '+_lprev+'m';
		  		document.querySelector('#limite').setAttribute('estado','normal');
		  		document.querySelector('#limite #tx').innerHTML='..propuesta válida...';
			  		
				
				_Features=_res.data.geom_superp;
				if(_res.res == 'exito'){
	            	cargarFeatures('cubierto');
	            }
	            
	            if(Number(_res.data.geom_superp_max.superp_max_numero1)>0){
					_val=Number(_res.data.intersec_sum*100/_res.data.geom_superp_max.superp_max_numero1);
					if(_val>10){
	        			_vp=formatearNumero(_val,0);	
	        		}else{
	        			_vp=formatearNumero(_val,2);
	        		}
	        		
	        		if(_res.data.intersec_sum>10){
	        			_v=formatearNumero(_res.data.intersec_sum,0);	
	        		}else{
	        			_v=formatearNumero(_res.data.intersec_sum,2);
	        		}
	        		
					_Puntaje=_v;
					_PuntajeP=_vp;
					
				}
				
				
	            avanzarTurno();			
			}else{
				alert('la solicitud no fue ejecutada');
			}
		}
	});
}