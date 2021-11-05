/**

* index_mapa_funciones.php
* funciones de interacci�n del mapa gen�erico
 * 
 *  
* @package    	geoGEC
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
* @copyright	2018 Universidad de Buenos Aires
* @copyright	esta aplicaci�n se desarroll� sobre una publicaci�n GNU 2017 TReCC SA
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
//funciones para el control del mapa


mapa.on('pointermove', function(_ev) {
    if (_ev.dragging) {
    	
    	//console.log(evt);
    	//deltaX = evt.coordinate[0] - evt.coordinate_[0];
		//deltaY = evt.coordinate[1] - evt.coordinate_[1];
		//console.log(deltaX);
		
      return;
    }
    var pixel = mapa.getEventPixel(_ev.originalEvent);

    sobrePunto(pixel,_ev);
});

mapa.on('click', function(_ev){    	
  consultaPunto(_ev.pixel,_ev);       
});

var sobrePunto = function(pixel) {
	if(_mapaEstado=='dibujando'){return;}
		_feat = mapa.forEachFeatureAtPixel(pixel, function(_feat, _layer){
	    if(_layer.get('name')=='centroides'){
			limpiarCentroidesResaltados();
    		return _feat;
        }else{
        	//console.log('no');
        }
    });
   
   
   if(_lyrCent.getSource()!=null){
        if(_feat==undefined){
    		return;
        }
        
        limpiarCentroidesResaltados();
        resaltarCentroide(_feat);
        
	}   
}

function limpiarCentroidesResaltados(){
	if(_lyrCent.getSource()!=null){
    	_feats = _lyrCent.getSource().getFeatures();
    	for(_nn in _feats){  
    		_feats[_nn].setStyle(_CentStyle);   	
    		document.querySelector('#tseleccion').innerHTML='';
    		document.querySelector('#tseleccion').style.display='none';	    	
    		document.querySelector('#tseleccion').removeAttribute('cod');
    		document.querySelector('#menuelementos #lista a[centid="'+_feats[_nn].getId()+'"]').removeAttribute('estado');
		}
	}
}


function resaltarCentroide(_feat){
		_feat.setStyle(_CentSelStyle);
        _pp=_feat.getProperties('nom');
        document.querySelector('#tseleccion').setAttribute('cod',_pp.cod);
		document.querySelector('#tseleccion').innerHTML=_pp.nom;
		document.querySelector('#tseleccion').style.display='inline-block';
		document.querySelector('#menuelementos #lista a[centid="'+_feat.getId()+'"]').setAttribute('estado','selecto');
}

function consultaPunto(pixel,_ev){
	
    if(_MapaCargado=='no'){console.log('el mapa no se carg� aun');return;}
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


