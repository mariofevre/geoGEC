/**

*
* aplicación para generar un mapa on line incorporando variable de la base de datos
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrolló sobre una publicación GNU 2017 TReCC SA
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
//funciones para el control del mapa

var _MapaCargado='no';
var _mapaEstado='';
var mapa={};
var vectorLayer={};
var seleccionLayer={};
var _source={};
var _souceSeleccion={};
var _AIsel='';
var _view={};
var _Dibujando='no';


var	_ExtraBaseWmsSource = new ol.source.TileWMS();//variable source utilizada por la capa extra base wms para mostar un url asignado dinámicamente.
var La_ExtraBaseWms = new ol.layer.Tile();

//definicion de variables para el layer de centroides
var _lyrCentSrc = new ol.source.Vector(
	{
		attributions: ['contenidos: <a href="http://www.municipioscosteros.org/nuestros-principios.aspx">GEC</a>']
  	}
);


var _lyrCent = new ol.layer.Vector({
	name:'centroides'
});   
var _CentSelStyle = new ol.style.Style();
var _CentStyle = new ol.style.Style();
//definicion de variables para el layer de elemento consultado
var _lyrElemSrc = new ol.source.Vector(
	{
		attributions: ['contenidos: <a href="http://www.municipioscosteros.org/nuestros-principios.aspx">GEC</a>']
  	}	
);

var _lyrElemStyle = new ol.style.Style();
var _lyrElem = new ol.layer.Vector({
	name:'elemento consultado',
	source: _lyrElemSrc

});   
_lyrElem.setStyle(_CentSelStyle);


var _sMarco = new ol.source.Vector({        
  projection: 'EPSG:3857'      
}); 

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
	
    var sobrePunto = function(pixel) {      
		//if(_Dibujando=='si'){return;}	
    	if(_mapaEstado=='dibujando'){return;}   
        var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')=='centroides'){	        	
	          return feature;
	        }else{
	        	//console.log('no');
	        }
        });
       
       if(_lyrCent.getSource()!=null){
       	
	        if(feature==undefined){
	        	
	        	_features = _lyrCent.getSource().getFeatures();
	        	for(_nn in _features){        		
	        		_features[_nn].setStyle(_CentStyle);        		
		    		document.querySelector('#tseleccion').innerHTML='';
		    		document.querySelector('#tseleccion').style.display='none';	    	
		    		document.querySelector('#tseleccion').removeAttribute('cod');
		    		
		    		document.querySelector('#menuelementos #lista a[centid="'+_features[_nn].getId()+'"]').removeAttribute('estado');
	    		}
	    		return;
	        }
	        
	        feature.setStyle(_CentSelStyle);
	        _pp=feature.getProperties('nom');
	        document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
			document.querySelector('#tseleccion').innerHTML=_pp.nom;
			document.querySelector('#tseleccion').style.display='inline-block';
			document.querySelector('#menuelementos #lista a[centid="'+feature.getId()+'"]').setAttribute('estado','selecto');
		}   
    }
    
 
	var _cargado='no';

	vectorLayer = new ol.layer.Vector({
		name: 'vectorLayer',
		style: styleArea
	    //source: _source
	});

	seleccionLayer = new ol.layer.Vector({
		name: 'seleccionLayer',
		style: _CentStyle,
	    source: _sourceSeleccion
	});
	
	var marcoLayer = new ol.layer.Vector({
		style: new ol.style.Style({
			stroke: new ol.style.Stroke({color : 'rgba(200,50,50,1)', width : 1, lineDash: [2,3]}),
	    	fill: new ol.style.Fill({color : 'rgba(250,255,250,0)'})
		}),
		source: _sMarco
	});

	
	var resaltadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sResalt
	});

	var cargadoLayer = new ol.layer.Vector({
		style: styleMapResalt,
		source: _sCargado
	});

	var candidatoLayer = new ol.layer.Vector({
		style: styleCandidato,
		source: _sCandidato
	});	
	
		
	var areaLayer = new ol.layer.Vector({
		style: styleArea,
		source: _sArea
	});
	
	_view =	new ol.View({
      projection: 'EPSG:3857',
      center: [-7000000,-4213000],
      zoom: 5,
      minZoom:2,
      maxZoom:19	      
	});

	var tablaRasLayer = new ol.layer.Image();
 /*
	 var tablaRasLayer = new ol.layer.Image({
	    source: new ol.source.ImageWMS({
	      ratio: 1,
	      url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
	      params: {
	            'VERSION': '1.1.1',  
	            LAYERS: 'est_01_municipios',
	            STYLES: ''
	      }
	    })
	});
	*/
	//var _sourceBaseOSM=new ol.source.OSM();
	var _sourceBaseOSM=new ol.source.Stamen({
		layer: 'toner'
	});
	
	_sourceBaseOSM.setAttributions(
		['base: <a href="http://stamen.com/">Stamen Design</a>, <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>']
	)

	
	var _sourceBaseBING=new ol.source.BingMaps({
	 	key: 'CygH7Xqd2Fb2cPwxzhLe~qz3D2bzJlCViv4DxHJd7Iw~Am0HV9t9vbSPjMRR6ywsDPaGshDwwUSCno3tVELuob__1mx49l2QJRPbUBPfS8qN',
	 	imagerySet:  'Aerial'
	});
		
	_sourceBaseBING.setAttributions(
		['base satelital: <a target="blank" href="https://www.microsoft.com/en-us/maps/product"><img src="https://dev.virtualearth.net/Branding/logo_powered_by.png"> Microsoft</a>']
	)
	
	var layerOSM = new ol.layer.Tile({
		 
	});
	
	var layerBing = new ol.layer.Tile({
		 
	});	

        
	_lyrCent.setStyle(_CentStyle);
   	
     
     _lyrElem.setStyle(_lyrElemStyle);
    La_ExtraBaseWms = new ol.layer.Tile({
        visible: true,
        source: _ExtraBaseWmsSource
    });
    
   	
    
	mapa = new ol.Map({
	    layers: [
			layerOSM,
			layerBing,
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
			marcoLayer
	    ],
	    target: 'mapa',
	    view: _view
	});
	 
	 //_xy=new ol.Coordinate(-6500000,-4100000);
	
	
	vectorLayer.setSource(_source);
	
	layerOSM.setSource(_sourceBaseOSM);		
	 /* 
	mapa.on('pointermove', function(evt) {
		
        if (evt.dragging) {
        	
        	//console.log(evt);
        	//deltaX = evt.coordinate[0] - evt.coordinate_[0];
  			//deltaY = evt.coordinate[1] - evt.coordinate_[1];
			//console.log(deltaX);
			
          return;
        }
        var pixel = mapa.getEventPixel(evt.originalEvent);

        sobrePunto(pixel);
    });
/*
    mapa.on('click', function(evt){    	
      consultaPunto(evt.pixel,evt);       
    });
*/
	_view.on('change:resolution', function(evt){
       
        if(_view.getZoom()>=19){
       		layerBing.setSource(_sourceBaseBING);
       		layerBing.setOpacity(0.8);
       }else if(_view.getZoom()>=17){
       		layerBing.setSource(_sourceBaseBING);
       		layerBing.setOpacity(0.5);
       }else{
       		layerBing.setSource();
       }
    });
	
	
	
	
	function consultaPunto(pixel,_ev){
		
	    if(_MapaCargado=='no'){console.log('el mapa no se cargó aun');return;}
	    
	    
	     var feature = mapa.forEachFeatureAtPixel(pixel, function(feature, layer){
	        if(layer.get('name')!=undefined){
	        	feature['layer']=layer.get('name');	        	
	          	return feature;	        
	        }else{
	        	console.log('sin elementos en ese punto del mapa');
	        	return null;	     
	        	
	        }
        });
	    
	    if(feature==null){
	    	return;
	    }else if(feature.layer=='centroides'){
			_cod=document.querySelector('#titulomapa #tseleccion').getAttribute('cod');		
			_tabla=document.querySelector('#titulomapa #tnombre').innerHTML;	
			consultarElemento('0',_cod,_tabla);
		}else if(feature.layer=='seleccionLayer'){
			
			console.log(feature);
			_cod=feature.get('cod');
			_tabla=feature.get('tabla');
			consultarSeleccion('',_cod,_tabla);
			
		}else{
			console.log('sin acciones definidas para esa capa');
		}

	}
	
	

	
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
		var _format = new ol.format.WKT();
		var _ft = _format.readFeature(_DataMarco.geotx, {
	        dataProjection: 'EPSG:3857',
	        featureProjection: 'EPSG:3857'
	    });
	    //_ft.setProperties(_geo);	    
	   	_sMarco.addFeature(_ft);
   	} 
}
