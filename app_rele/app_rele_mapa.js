/**
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



function cargarMapa(){
	
	document.getElementById('mapa').innerHTML='';
    document.getElementById('mapa').setAttribute('estado','activo');
    
   	_yStroke = new ol.style.Stroke({
		color : 'rgba(0,100,255,0.8)',
		width : 2,
	});
	_yFill = new ol.style.Fill({
	   color: 'rgba(0,100,255,0.6)'
	}); 
	var cRes = new ol.style.Circle({
	    radius: 5,
	    fill: _yFill,
	    stroke: _yStroke
	});
    var styleMapResalt = new ol.style.Style({
	     image:cRes
    });
    
    var styleDef = new ol.style.Style({
	     image:	new ol.style.Circle({
			    radius: 5,
			    fill: _yFill,
			    stroke: _yStroke
			})
    });
    
	_source = new ol.source.Vector({ 
		wrapX: false,   
    	projection: 'EPSG:3857' 
    }); 

	_sourceSeleccion = new ol.source.Vector({ 
		wrapX: false,   
    	projection: 'EPSG:3857' 
    }); 
    
    
    var styleArea = new ol.style.Style({
	    stroke: new ol.style.Stroke({color : 'rgba(255,50,100,1)', width : 2}),
	    fill: new ol.style.Fill({color : 'rgba(255,150,150,0.4)'})
    });
 
    var styleCandidato = new ol.style.Style({	    
	    image: new ol.style.Circle({ radius: 5,
		    stroke: new ol.style.Stroke({color : 'rgba(255,100,50,1)', width : 1}),
	    	fill: new ol.style.Fill({color : 'rgba(200,250,100,0.5)'}) 
		})
    });
    
    _CentStyle = new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: 'rgba(255,155,155,1)'}),
		       stroke: new ol.style.Stroke({color: '#ff3333',width: 0.5}),
		       radius: 3
		 }),
		 fill: new ol.style.Fill({color: 'rgba(255,155,155,1)'}),
		 stroke: new ol.style.Stroke({color: '#ff3333',width: 0.5})
     });
     
   	_CentSelStyle = new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: 'rgba(255,102,0,1)'}),
		       stroke: new ol.style.Stroke({color: '#ff3333',width: 0.8}),
		       radius: 6
		 }),
		 fill: new ol.style.Fill({color: 'rgba(255,102,0,1)'}),
		 stroke: new ol.style.Stroke({color: '#ff3333',width: 1.8}),
		 zIndex:100
     });
     
    _lyrElemStyle = new ol.style.Style({
        image: new ol.style.Circle({
		      fill: new ol.style.Fill({color: 'rgba(255,102,0,0.5)'}),
		      stroke: new ol.style.Stroke({color: '#ff3333',width: 0.8}),
		      radius: 6
		}),
		fill: new ol.style.Fill({color: 'rgba(228,25,55,0.5)'}),
		stroke: new ol.style.Stroke({color: 'rgba(228,25,55,0.8)',width: 2}),
		zIndex:1
    });


 	var _myStroke = new ol.style.Stroke({
		color : 'rgba(255,0,0,1.0)',
		width : 1,
	});
	var circle = new ol.style.Circle({
	    radius: 5,
	    stroke: _myStroke
	}); 
	var sy = new ol.style.Style ({
	   image:circle
	});
	var _sResalt = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });
    var _sCargado = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });
    var _sCandidato = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });	    	    
	var  _sArea = new ol.source.Vector({        
      projection: 'EPSG:3857'      
    });
    
	var _cargado='no';

	vectorLayer = new ol.layer.Vector({
		name: 'vectorLayer',
		style: styleArea,
		zIndex:100
	    //source: _source
	});

	seleccionLayer = new ol.layer.Vector({
		name: 'seleccionLayer',
		style: _CentStyle,
	    source: _sourceSeleccion,
	    zIndex:108
	});
	
	var marcoLayer = new ol.layer.Vector({
		style: new ol.style.Style({
			stroke: new ol.style.Stroke({color : 'rgba(200,50,50,1)', width : 1, lineDash: [2,3]}),
	    	fill: new ol.style.Fill({color : 'rgba(250,255,250,0)'})
		}),
		source: _sMarco,
		zIndex:10
	});

	
	var resaltadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sResalt,
		zIndex:105
	});

	var cargadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sCargado,
		zIndex:100
	});

	var candidatoLayer = new ol.layer.Vector({
		style: styleCandidato,
		source: _sCandidato,
		zIndex:110
	});	
		
	var areaLayer = new ol.layer.Vector({
		style: styleArea,
		source: _sArea,
		zIndex:102
	});
	
	_view =	new ol.View({
      projection: 'EPSG:3857',
      center: [-7000000,-4213000],
      zoom: 5,
      minZoom:2,
      maxZoom:19	      
	});

	var tablaRasLayer = new ol.layer.Image();

	_lyrCent.setStyle(_CentStyle);

	var style = new ol.style.Style ({
	  fill: new ol.style.Fill({
	    color: 'black',
	  }),
	});

	
     
    _lyrElem.setStyle(_lyrElemStyle);
    La_ExtraBaseWms = new ol.layer.Tile({
        visible: true,
        source: _ExtraBaseWmsSource
    });
    
	mapa = new ol.Map({
	    layers: [
			seleccionLayer,
			vectorLayer,
			resaltadoLayer,
			candidatoLayer,
			cargadoLayer,
			areaLayer,
			tablaRasLayer,
			La_ExtraBaseWms,
			_lyrCent,
			_lyrElem,
			_lyrPropios,
			_layerRecorte,
			_layerLocat,
			marcoLayer
	    ],
	    target: 'mapa',
	    view: _view
	});
	
	
	if(document.querySelector('.ol-zoom.ol-unselectable.ol-control .ol-zoom-out')!=null){
		//corrije el encode del zoom out
    	document.querySelector('.ol-zoom.ol-unselectable.ol-control .ol-zoom-out').innerHTML='-';
    }
    
	vectorLayer.setSource(_source);	

	
	function reiniciarMapa(){
		_features=_sCargado.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCargado.removeFeature(_features[i]);
		}
		
		_features=_sCandidato.getFeatures();	
		for (i = 0; i < _features.length; i++) {		
			_sCandidato.removeFeature(_features[i]);
		}
		//mostrarArea(parent._Adat);	
	}

		
	function consultaPuntoAj(_Pid){
		console.log(_Pid);
		formAI(_Pid);
		
	}
}

cargarMapa();
$("#mapa").css("height", $(window).height() - 150); this.mapa.updateSize();

function mostrarTablaEnMapa(_tabla){
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
}

function cargarCapaMarco(){
	_sMarco.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	if(_DataMarco.geotx==''){return;}		
	
	//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
	//console.log(_DataMarco.geotx);
	if(_DataMarco.geotx!=null){
		console.log('carga marco');
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_DataMarco.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	    //_ft.setProperties(_geo);	    
	   	_sMarco.addFeature(_ft);
   	} 
}


function cargarElementoPropio(_codSel){
	_lyrPropiosSrc.clear();
    //console.log(_source_ind.getFeatures());
	_haygeom='no';
	if(_UsuarioA.permisos.marcos[_codSel].geotx==''){return;}		
	if(_UsuarioA.permisos.marcos[_codSel].geotx==null){return;}	
	console.log('carga elmento porpio');	
	var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_UsuarioA.permisos.marcos[_codSel].geotx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857',
        id:_codSel
    });
    console.log(_codSel);
    //_ft.setProperties(_geo);	    
   	_lyrPropiosSrc.addFeature(_ft);   	
}

function desCargarElementoPropio(_codSel){
	_lyrPropiosSrc.clear();
}

//layer de superposicion a layer buffer
var _source_rele_superp= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});
var _layer_rele_superp= new ol.layer.Vector({
	name: 'buffer',
    source: _source_rele_superp,
    style: ol.style.Style({
		 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 0.5}),
		 fill: new ol.style.Fill({color: 'rgb(0,0,0)'}),
		 zIndex:1
	})    
});

//layer de geometr�a del relevamiento
var _source_rele= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});

var _color='rgba(0,200,256,0.2)';
var _colorb='rgba(0,0,100,1)';
var _ancho='0.5';

var labelStyle = new ol.style.Style({
	image: new ol.style.Circle({
		   fill: new ol.style.Fill({color: _color}),
		   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
		   radius: 12
	}),
	fill: new ol.style.Fill({color: _color}),
	stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
	zIndex:1000,
	text: new ol.style.Text({
		font: '12px Calibri,sans-serif',
		overflow: true,
		offsetX:8,
		offsetY:-4,
		fill: new ol.style.Fill({
		color: '#000'
		}),
		stroke: new ol.style.Stroke({color: '#fff', width: 2})
	})
});


var _mudoStyle = new ol.style.Style({
	image: new ol.style.Circle({
		   fill: new ol.style.Fill({color: _color}),
		   stroke: new ol.style.Stroke({color: _colorb,width: 2}),
		   radius: 12
	}),
	fill: new ol.style.Fill({color: _color}),
	stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
	zIndex:1000
});


var _layer_rel= new ol.layer.Vector({
	name: 'relevamiento',
    source: _source_rele,
    zIndex:5000,
    //style:_mudoStyle,
    
	style: function(feature){
		if(feature.get('name')!=null){
	    	labelStyle.getText().setText(feature.get('name'));	    	
	   	}else{
	   		labelStyle.getText().setText('s/n');
	   	}
	   //console.log(feature.get('estado'));
	   	if(feature.get('estado')==='0'){
	   		labelStyle.getFill().setColor("rgba(255,0,0,0.5)");
	   		console.log(labelStyle.getImage().getFill());
	   		//labelStyle.getImage().setColor("rgba(0,255,100,0.8)");
	   		
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(255,0,0,0.5)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		
	   	}else if(feature.get('estado')==='1'){
	   		labelStyle.getFill().setColor("rgba(0,255,100,0.8)");
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: "rgba(0,255,100,0.8)"}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   		
	   		
	   	}else{
	   		labelStyle.getFill().setColor(_color);
	   		labelStyle.setImage(
				new ol.style.Circle({
					   fill: new ol.style.Fill({color: _color}),
					   stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
					   radius: 12,
					   zIndex:1000,
				})
	   		);
	   	}
	   
		return labelStyle;
	},
	declutter: true   
});
mapa.addLayer(_layer_rel);

var _source_rel_sel= new ol.source.Vector({
	wrapX: false,   
	projection: 'EPSG:3857' 
});

//layer de geometr�a seleccionadad para cargar datos
var _st_rel_sel=new ol.style.Style({
     image: new ol.style.Circle({
	       stroke: new ol.style.Stroke({color:'rgb(8, 175, 217)',width: 0.8}),
	       radius: 6
	 }),
	 stroke: new ol.style.Stroke({color: 'rgb(8, 175, 217)',width: 1}),
	 zIndex:2000
});
var _layer_rel_sel= new ol.layer.Vector({
	name: 'indicador: elemento selecto',
    source: _source_rel_sel,
    style: _st_rel_sel
});
//console.log('1');
mapa.addLayer(_layer_rel_sel);


mapa.on('click', function(evt){   
	if(_mapaEstado=='dibujando'){return;} 	
	
  	consultaPuntoRel(evt.pixel,evt);       
});

function consultaPuntoRel(pixel,_event) { 
	if(_mapaEstado=='dibujando'){return;}
	if(_mapaEstado=='nuevaGeom'){return;}  
	if(_mapaEstado=='terminado'){return;}
	//if(_Dibujando=='si'){return;}	
	_source_rel_sel.clear();
    var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
        if(layer.get('name')=='relevamiento'){	        	
          return feature;
        }else{
        	console.log('no');
        }
    });
    
    _prop=feature.getProperties();
    
     console.log('carga punto rel');
    var _format = new ol.format.WKT();
	var _ft = _format.readFeature(_prop.geotx, {
        dataProjection: 'EPSG:3857',
        featureProjection: 'EPSG:3857'
    });
    
    _colors='rgba(0,0,256,0.5)';
    
    _ft.setStyle(new ol.style.Style({
         image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: _color}),
		       stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
		       radius: 6,
			   zIndex:100
		 }),
		 fill: new ol.style.Fill({color: _color}),
		 stroke: new ol.style.Stroke({color: _colors,width: 0.5}),
		 zIndex:100
	}));
	
    _source_rel_sel.addFeature(_ft);
    //console.log(feature.getProperties());
    accionGeomSeleccionada(_prop.id);
   //alert('hizo click en registro id:')
}


var _rmax = 228;
var _gmax = 25;
var _bmax = 55;

var _rmin = 204;
var _gmin = 255;
var _bmin = 204;

var _encuadrado='no';

var _mapaEstado ='';



function asignarRepresentacion(_data){
	
	_modo='anance'; // TODO incorporar la posibilidad de ver contenidos en lugar de avances.
	
	if(_modo=='anance'){
	
		_color='rgba(0,200,256,0.2)';
		_colorb='rgba(0,0,100,1)';
		_ancho='0.5';
		_estilo =new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: _color}),
			 stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
			 zIndex:100
		});
		
		
	}else if(_modo=='contenido'){
		
		_color='rgba(255,100,100, 0.9)';
		_colors='rgba(255,100,100, 1)';
	   if(
	   	_geo.estadocarga=='sin carga'){
	   		_color='rgba(98, 98, 98, 0.9';
	   		_colors='rgba(98, 98, 98, 1)';
	   }else if(
	   	_geo.estadocarga=='incompleto'){
	   		_color='rgba(128, 128, 128,0.9)';
	   		_colors='rgba(128, 128, 128,1)';
	   }else if(_val!=undefined){
		    _amplitud=(_res.data.indicador.representar_val_max-_res.data.indicador.representar_val_min);
		    //console.log(_amplitud);
		    _porc=(_val-_res.data.indicador.representar_val_min)/_amplitud;
		    //console.log(_porc);
		    if(_porc>1){
		    	_red= _rmax;
		    	_gre = _gmax;
		   		_blu = _bmax;
		    }else if(_porc<0){
		    	_red=_rmin;
		    	_gre = _gmin;
		   		_blu = _bmin;	
		    		
		    }else{
			    _red = _rmin+(_rmax-_rmin)*_porc;
			    _gre = _gmin+(_gmax-_gmin)*_porc;
			    _blu = _bmin+(_bmax-_bmin)*_porc;
		    }
		    _color='rgba('+_red+','+_gre+','+_blu+', 0.8)';
		    _colors='rgba('+_red+','+_gre+','+_blu+', 1)';
	    }
	    
	    _estilo =new ol.style.Style({
	         image: new ol.style.Circle({
			       fill: new ol.style.Fill({color: _color}),
			       stroke: new ol.style.Stroke({color: _colors,width: 0.8}),
			       radius: 6
			 }),
			 fill: new ol.style.Fill({color: _color}),
			 stroke: new ol.style.Stroke({color: _colors,width: _ancho}),
			 zIndex:100
		});
	}
	
	
	
	
	
	
	return _estilo;
}


var _featurescargadas=0;

function dibujarReleMapa(_res){
	
    //console.log('inicia funcion'); 
	//console.log(_res.data.geom);
	//console.log(_source_rele.getFeatures());
    _source_rele.clear();
    //console.log(_source_rele.getFeatures());
    
	_campo=_res.data.campa.representar_campo;
	_haygeom='no';
	
		
	for(_gn in _res.data.geom){
		_featurescargadas++;
		_geo=_res.data.geom[_gn];
		_val=null;
		_haygeom='si';
		
		
		for(_vn in _geo.valores){
			//console.log(_vn);
			if(_geo.valores[_vn].zz_superado=='0'){
				_val=_geo.valores[_vn][_campo+'_dato'];				
			}
		}		
		
		//console.log('+ um geometria: campo'+_campo+'. valor:'+_val);
		 console.log('carga regeom rele');
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_geo.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857',
	        id: _gn
	    });
	 	_ft.setId(_gn);
	 	
	 	
		/*
        _feat.setProperties({
            'id':_features[elem].id
        });*/
		
		//console.log(_color);
		_ancho=1.8;
		//console.log(_res.data.capa);
		if(_res.data.capa.tipogeometria=='LineString'){
			_ancho=10;
		}
	    
	    _estilo=asignarRepresentacion('_data');
	    //_ft.setStyle(_estilo);//por ahora usamos el estilo del mapa
	    
	    _ft.setProperties(_geo);
	    if(_geo.t1!=''){
			_ft.set('name',_geo.t1);
		}else{
			_ft.set('name','s/n');
		}
		
		_ft.set('estado',_geo.n1);
	   	_source_rele.addFeature(_ft);
	   	
	   	
	   	/*
	   	_ft.setStyle(function(feature) {
			if(feature.get('name')!=null){
		    labelStyle.getText().setText(feature.get('name'));
		    
		   }else{
		   	labelStyle.getText().setText('');
		   }
		   return labelStyle;
		});
		*/
		
	   	_c='rgba(250,250,250,0.8)';
	   	if(_geo.n1=='0'){ 
		   	_c='rgba(250,0,0,0.8)';
	   	}else if(_geo.n1=='1'){
		   	_c='rgba(0,250,0,0.8)';
	   	}
	   	
	   	_st= new ol.layer.Vector({
			image: new ol.style.Circle({
		       fill: new ol.style.Fill({color: _c}),
		       stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
		       radius: 6
			}),
			fill: new ol.style.Fill({color: _color}),
			stroke: new ol.style.Stroke({color: _colorb,width: _ancho}),
			zIndex:1000,
			text: new ol.style.Text({
		    	font: '12px Calibri,sans-serif',
		    	offsetX:5,
		    	overflow: true,
		    	fill: new ol.style.Fill({
		      	color: '#000'
			    }),
			    stroke: new ol.style.Stroke({color: '#fff', width: 2})
			})
		});
		//_ft.setStyle(_st);

	}
	
	if(_haygeom=='si'){
		_ext= _source_rele.getExtent();	
		console.log(_ext);
		if(_encuadrado=='no'){
			mapa.getView().fit(_ext, { duration: 1000 , padding: [50, 50, 50, 50]});
			
			_encuadrado='si';
		}
	}
	//geometryOrExtent
	
	
	mapa.on('pointermove', function(evt){
		if(_mapaEstado=='dibujando'){return;}   
        if (evt.dragging) {	
        	//console.log(evt);
        	//deltaX = evt.coordinate[0] - evt.coordinate_[0];
  			//deltaY = evt.coordinate[1] - evt.coordinate_[1];
			//console.log(deltaX);			
          return;
        }
        var pixel = mapa.getEventPixel(evt.originalEvent);
        sobreIndicador(pixel);
    });

	var sobreIndicador = function(pixel) {     
	 
		if(_mapaEstado=='dibujando'){return;}   
    	
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')=='indicador'){	        	
	          return feature;
	        }else{
	        	//console.log('no');
	        }
        });
        //console.log(feature.getProperties);
       //alert('se�al� en registro id:')
    }
    
    
}



/*
var xmlDoc;
function dibujarCapaSuperp(_res){
	_source_rele_superp.clear();
    if (_res.data != null){
        var capaQuery = _res.data.capa_superp;
		_features = _res.data.geom_superp;
		_capa = _res.data.capa_superp;
        //Operaciones para leer del xml los valores de simbologia
        var xmlSld = capaQuery["sld"];
		
		console.log('representando capa superpuesta');
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
			
			console.log('o'+_features);
			
		    for(var elem in _features){
				console.log('feat:'+elem);
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
					          zIndex:50
					        });
						}
					}
		        	_feat.setStyle(_st);
		
		        _source_rele_superp.addFeature(_feat);
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
				console.log('feat:'+elem);
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
		        _source_rele_superp.addFeature(_feat);
			}
		}
    }
}
*/

var drawL={};
var _nnelem=0;
function accionEditarCrearGeometria(_idgeom){    
	
	_typeGeom=_DataCapa.tipogeometria;
	//_typeGeom='Polygon';//TODO resolver este hardcoded
	mapa.removeInteraction(drawL);
    drawL = new ol.interaction.Draw({
        source: _source_rel_sel,
        type: _typeGeom
    });
    
    _mapaEstado='dibujando';
    
    mapa.addInteraction(drawL);  
	
	
	_source_rel_sel.on('change', function(evt){
		mapa.removeInteraction(drawL);
		
		if(_mapaEstado!='dibujando'){return;}
		if(_mapaEstado=='terminado'){_mapaEstado.estado='nuevaGeom';return;}
		
		if(_mapaEstado.estado=='error'){
			_mapaEstado.estado='terminado';
			_source_rel_sel.clear();
			return;
		}	
		
		_features=_source_rel_sel.getFeatures();
		var _format = new ol.format.WKT();
		_geometriatx=_format.writeGeometry(_features[0].getGeometry());
		document.querySelector('#FormularioRegistro #nuevageometria').value=_geometriatx;
		
		
		_idgeom=document.querySelector('#FormularioRegistro [name="idgeom"]').value;
		console.log('actualizanro atributo de nuevageom.... con _idgeom: '+_idgeom);
		document.querySelector('#FormularioRegistro #nuevageometria').setAttribute('idgeom',_idgeom);
		
		console.log('removiendo dibujante');
		_nnelem++;		
		
		_mapaEstado='terminado';	
		_clon=_features[0].clone();
		
		_source_rele.addFeature(_clon);
		_clon.setId(_idgeom);
		
		_source_rel_sel.clear();
		
		_mapaEstado='';	
	});	
	
	alert('dibuje en el mapa la nueva geometr�a');
}




var selecP={};
var _nnelem=0;
function accionSeleccionarGeometria(){    
	//_DataCapa.tipogeometria='Polygon';
	_typeGeom=_DataCapa.tipogeometria;
	mapa.removeInteraction(drawL);
    drawL = new ol.interaction.Draw({
        source: _source_rel_sel,
        type: _typeGeom
    });
    
    _mapaEstado='dibujando';        
    mapa.addInteraction(drawL);  
	
	
	_source_rel_sel.on('change', function(evt){	
	
		if(_mapaEstado!='dibujando'){return;}
	
		if(_mapaEstado=='terminado'){_mapaEstado.estado='nuevaGeom';return;}	
		if(_mapaEstado.estado=='error'){
			_mapaEstado.estado='terminado';
			_source_rel_sel.clear();
			return;
		}	
		
		_features=_source_rel_sel.getFeatures();
		var _format = new ol.format.WKT();
		_geometria=_format.writeGeometry(_features[0].getGeometry());
		
		_nnelem++;		
		guardarNuevaGeometria(_geometria,_nnelem);
		
		_mapaEstado='terminado';	
		_clon=_features[0].clone();
		_source_rele.addFeature(_clon);
		_clon.setId('nn'+_nnelem);
		_source_rel_sel.clear();
		_mapaEstado='';
	});	
	
	alert('dibuje en el mapa la nueva geometr�a');
}


function dibujarNuevaUa(_idgeom){
	alert('Funci�n en desarrollo. Paciencia. \n Si necesit�s esta funci�n comunicate con el equipo de desarrollo.');
}

function cambiarGeometria(_idgeom){
	_source_rel_sel.clear();
	_feat=_source_rele.getFeatureById(_idgeom);
	if(_feat!=null){
		_feat.setStyle(
			new ol.style.Style({
				image: new ol.style.Circle({
				       fill: new ol.style.Fill({color: _color}),
				       stroke: new ol.style.Stroke({color: _colorb,width: 0.8}),
				       radius: 6
				}),
				fill: new ol.style.Fill({color: 'rgba(200,200,200,0.2)'}),
				stroke: new ol.style.Stroke({lineDash: [2,6], color: 'rgba(256, 75, 8, 0.8)',width: 2}),
				zIndex:1000
			})
		);
		_ext= _feat.getGeometry().getExtent();	
		//console.log(_ext);
	mapa.getView().fit(_ext, { duration: 1000 });
	}	
	console.log('inicionado accionEditar.... con _idgeom: '+_idgeom);
	accionEditarCrearGeometria(_idgeom);
}


function activarDibujarGeomatria(){
	
	
}

function descargarMapaDXF(){
	/* esto no funciona ya en openlayers6
	mapa.once('postcompose', function(event) {
	console.log(event);
		
      var canvas = event.context.canvas;
      //exportPNGElement.href = canvas.toDataURL('image/png');
      _ext=mapa.getView().calculateExtent();
      _img={
      	'png':canvas.toDataURL('image/png'),//para que esta acci�n no sea frenada por insegura, los wms tienen que tener cors. las capas OSM y vectoriales no tiebnen problema.
      	'minx':_ext[0],
      	'miny':_ext[1],
      	'maxx':_ext[2],
      	'maxy':_ext[3],
      }
      
      _imgs=Array(_img);
      guardarDXFfondo(_img);
    });
    mapa.renderSync();
    */
   
	   
	  mapa.once('rendercomplete', function () {
	    const mapCanvas = document.createElement('canvas');
	    const size = mapa.getSize();
	    mapCanvas.width = size[0];
	    mapCanvas.height = size[1];
	    const mapContext = mapCanvas.getContext('2d');
	    Array.prototype.forEach.call(
	      document.querySelectorAll('.ol-layer canvas'),
	      function (canvas) {
	        if (canvas.width > 0) {
	          const opacity = canvas.parentNode.style.opacity;
	          mapContext.globalAlpha = opacity === '' ? 1 : Number(opacity);
	          const transform = canvas.style.transform;
	          // Get the transform parameters from the style's transform matrix
	          const matrix = transform
	            .match(/^matrix\(([^\(]*)\)$/)[1]
	            .split(',')
	            .map(Number);
	          // Apply the transform to the export map context
	          CanvasRenderingContext2D.prototype.setTransform.apply(
	            mapContext,
	            matrix
	          );
	          mapContext.drawImage(canvas, 0, 0);
	        }
	      }
	    );
	    if (navigator.msSaveBlob) {
	      // link download attribute does not work on MS browsers
	      navigator.msSaveBlob(mapCanvas.msToBlob(), 'map.png');
	    } else {
	  
	  		_ext=mapa.getView().calculateExtent();

	    	_img={
	      	'png':mapCanvas.toDataURL(),//para que esta acci�n no sea frenada por insegura, los wms tienen que tener cors. las capas OSM y vectoriales no tiebnen problema.
	      	'minx':_ext[0],
	      	'miny':_ext[1],
	      	'maxx':_ext[2],
	      	'maxy':_ext[3],
	      }
	      guardarDXFfondo(_img);
	    }
	  });
	  mapa.renderSync();


}
/*
function exportPNGElement() {
  mapa.once('postcompose', function(event) {
      var canvas = event.context.canvas;
      if (navigator.msSaveBlob) {
        navigator.msSaveBlob(canvas.msToBlob(), 'map.png');
      } else {
        canvas.toBlob(function(blob) {
          saveAs(blob, 'map.png');
        });
      }
    });
    map.renderSync();
}*/
