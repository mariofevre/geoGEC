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


function consultarPermisos(){
	var _IdMarco = getParameterByName('id');
	var _CodMarco = getParameterByName('cod');
	_parametros = {
		'codMarco':_CodMarco	
	}
	$.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
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


function actualizarPermisos(){
    //repite consultas y cargas en caso de actualizarse los permisos por acceso de usuario registrado
    consultarTablas();
}

function consultarTablas(){
    document.querySelector('#menutablas #lista').innerHTML='';
    consultarElemento();//limpia residuos de visualización de elementos;
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
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            //console.log(_res);
            _Tablas=_res.data.tablas;
            _TablasConf=_res.data.tablasConf;
            _cont=document.querySelector('#menutablas #lista');
            for(var _nn in _Tablas['est']){			
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

                _aaa.setAttribute('onclick','alert("modo deprecado, actualizar referencia a funcion de descarga")');//antes: escargarSHP(this,event),

                _host="http://190.111.246.33:8080/geoserver/geoGEC/";

                _aaa.setAttribute('link',_host+_standarSHP+_capaSHP);
                _aaa.setAttribute('link',_host+_standarSHP+_capaSHP);//retiramos el recorte para la descarga


                _aaa.innerHTML='<img src="./img/descargar.png" alt="descargar">';
                _aaa.setAttribute('tabla',_Tablas['est'][_nn]);
                _cont.appendChild(_aaa);

                _aaa=document.createElement('br');
                _cont.appendChild(_aaa);
            }

            if(_Est!=''){
                mostrartabla(document.querySelector('#lista > a.nombretabla[tabla="'+_Est+'"]'));
            }
        }
    });
}
consultarTablas();


var _Linkeables={};


function consultarCapasLinkeables(){
	

    var _parametros = {
        'codMarco':_CodMarco
    };
    
    
    document.querySelector('#formlinkcapa #lista').innerHTML='';
	document.querySelector('#formlinkcapa').style.display='none';
	
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado_linkeable.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg)
            {
                alert(_res.mg[_nm]);
            }
            
            _Linkeables=_res.data.linkeables;
            
            
            for(_n in _Linkeables){
				_d=_Linkeables[_n];
				_op=document.createElement('a');
				_op.innerHTML=_d.nombre;
				_op.setAttribute('onclick','elijeCapaLink("'+_n+'")');
				_op.title=_d.descripcion;				
				document.querySelector('#formlinkcapa #lista').appendChild(_op);
			}
			
			
			document.querySelector('#formlinkcapa').style.display='block';
		}
	})
}






function consultarCamposExternosLinkeables(){	

    var _parametros = {
        'codMarco':_CodMarco
    };
    
    _va_li_ca=document.querySelector('#vinculaciones input[name="link_capa"]').value;
    if(_va_li_ca==''){
		alert('para elegir un campo de vinculación en la capa destino antes debe elegir cual seá la capa destino');
		return;
	}
      
    
    document.querySelector('#formlinkcampoexterno #lista').innerHTML='';
	document.querySelector('#formlinkcampoexterno').style.display='none';
	
    $.ajax({
        url:   './app_capa/app_capa_consultar_listado_linkeable.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            
            _Linkeables=_res.data.linkeables;
            
             _va_li_ca=document.querySelector('#vinculaciones input[name="link_capa"]').value;
            
            for(_n in _Linkeables[_va_li_ca]){
				_d=_Linkeables[_va_li_ca][_n];
				
				if(_n.substring(0,8)!='nom_col_'){continue;}
				
				_op=document.createElement('a');
				_op.innerHTML=_d;
				_op.setAttribute('onclick','elijeCampoExternoLink("'+_n+'")');
				document.querySelector('#formlinkcampoexterno #lista').appendChild(_op);
				
			}
			
			document.querySelector('#formlinkcampoexterno').style.display='block';
		}
	})			
}

function mostrarCamposLocalesLinkeables(){	

    document.querySelector('#formlinkcampolocal #lista').innerHTML='';
	document.querySelector('#formlinkcampolocal').style.display='none';
	
            
	for(_n in _Capa){
		
		_d=_Capa[_n];
		
		if(_n.substring(0,8)!='nom_col_'){continue;}
		
		_op=document.createElement('a');
		_op.innerHTML=_d;
		_op.setAttribute('onclick','elijeCampoLocalLink("'+_n+'")');
		
		document.querySelector('#formlinkcampolocal #lista').appendChild(_op);	
	}
	document.querySelector('#formlinkcampolocal').style.display='block';
}


					
function cargarAtabla(_this){
    limpiarfomularioversion();
    document.getElementById('divCargaCapa').style.display='block';
    document.getElementById('divCargaCapa').setAttribute('tabla',_this.getAttribute('tabla'));
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

    _ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/geoGEC/wms',
        params: {
            'VERSION': '1.1.1',
            tiled: true,
            LAYERS: _tabla,
            STYLES: ''
        }
    });
    La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
    consultarCentroides(_this);
}

	
function consultarCentroides(_this){
    _parametros={
        'tabla': _this.getAttribute('tabla')
    };


    $.ajax({
        data: _parametros,
        url:   './consulta_centroides.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);			
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='err'){
                
            } else {
                //cargaContrato();	
                _lyrCentSrc.clear();
                _cont=document.querySelector('#menuelementos #lista');
                _cont.innerHTML='';
                for(var _no in _res.data.centroidesOrden){
                    _nc=_res.data.centroidesOrden[_no];
                    _hayaux='no';						
                    _dat=_res.data.centroides[_nc];					
                    var format = new ol.format.WKT();				
                    var _feat = format.readFeature(_dat.geo, {
                        dataProjection: 'EPSG:3857',
                        featureProjection: 'EPSG:3857'
                    });
                    _feat.setId(_dat.id);
                    _feat.setProperties({
                        'nom':_dat.nom,
                        'cod':_dat.cod,
                        'id':_dat.id
                    });

                    _lyrCentSrc.addFeature(_feat);						
                    _lyrCent.setSource(_lyrCentSrc);

                    _MapaCargado='si';

                    _aaa=document.createElement('a');
                    _aaa.setAttribute('centid',_dat.id);
                    _aaa.setAttribute('onmouseover','resaltarcentroide(this)');
                    _aaa.setAttribute('onmouseout','desaltarcentroide(this)');
                    _aaa.setAttribute('cod',_dat.cod);
                    _aaa.innerHTML='<span class="nom">'+_dat.nom+"</span>"+'<span class="cod">'+_dat.cod+"</cod>";
                    _aaa.setAttribute('onclick','consultarElemento("0","'+_dat.cod+'","'+_res.data.tabla+'")');
                    _cont.appendChild(_aaa);
                }
                _ext= _lyrCentSrc.getExtent();
                mapa.getView().fit(_ext, { duration: 1000 });

                if(_Cod != ''){		
                    consultarElemento("0",_Cod,_Est);
                }
            }
        }
    });		
}



function guardarCapa(_this,_procesar){
    var _this=_this;
    var _procesar=_procesar;
    
    editarNombreCapa();
    editarDescripcionCapa();
    editarCamposCapa();
    editarCamposNombresCapa();
    editarTipoGeomCapa();
    editarModoWMSCapa();
    editarCaracteristicasCapa();
    guardarSLD();
    
    
    var _parametros = {
        'instrucciones': _this.parentNode.querySelector('#verproccampo').innerHTML, 
        'fi_prj': _this.parentNode.querySelector('select#crs').value,
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'codMarco': _CodMarco
    };
    $.ajax({
        url:  './app_capa/app_capa_editar_shapefile.php',
        type: 'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            console.log(_res);
			
            if(_procesar=='si')
            {
                procesarCapa2(_this.parentNode,0);
                return;
            }
            formversion(_this.parentNode);
        }
    });
}



function editarCapaNombre(_event,_this){
    //console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarNombreCapa();
    }
}

function editarCapaDescripcion(_event,_this){
    //console.log(_event.keyCode);
    if(_event.keyCode==9){return;}//tab
    if(_event.keyCode>=33&&_event.keyCode<=40){return;}//direccionales
    if(_event.keyCode==13){
        editarDescripcionCapa();
    }
}

function editarNombreCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevoNombre = document.getElementById('capaNombre').value;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'nombre': nuevoNombre
    };
    
    editarCapa(parametros);
}


function editarTipoGeomCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    
    _val='';
    _ops=document.querySelectorAll('#carga [name="tipogeometria"] option');
    for(_opn in _ops){
    	if(typeof _ops[_opn] != 'object'){continue;}
    	if(_ops[_opn].selected==true){
    		_val=_ops[_opn].value;
    		break;		
    	}
    }
        
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'tipogeometria': _val
    };
    
    editarCapa(parametros);
}


function editarModoWMSCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');

    _val=document.querySelector('#carga [name="modo_wms"]').value;
    
    if(_val=='si'){
    	_wms_layer='geoGEC:v_capas_registros_capa_'+idCapa;
    }else{
    	_wms_layer='';
    }
    var _wms_layer = 'v_capas_registros_capa_'+idCapa;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'wms_layer': _wms_layer
    };
    
    $.ajax({
	   url:   './app_capa/app_capa_publicar_wms.php',
        type:  'post',
        data: parametros,
        error:  function (response){alert('error al contactar al servidor');},
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res=='exito'){
                //Hacer algo luego de editar?
    			alert('wmseditado');            
            }else{
                alert('error asf0jg4fcn02h');
            }
        }
    })
    
}



function editarCaracteristicasCapa(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');

    _val=document.querySelector('#carga [name="modo_wms"]').value;
        
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'modo_publica': document.querySelector('#carga [name="modo_publica"]').value,
        'tipo_fuente': document.querySelector('#carga [name="tipo_fuente"]').value,
        'link_capa': document.querySelector('#carga [name="link_capa"]').value,
        'link_capa_campo_local': document.querySelector('#carga [name="link_capa_campo_local"]').value,
        'link_capa_campo_externo': document.querySelector('#carga [name="link_capa_campo_externo"]').value,
        'fecha_ano': document.querySelector('#carga [name="fecha_ano"]').value,
        'fecha_mes': document.querySelector('#carga [name="fecha_mes"]').value,
        'fecha_dia': document.querySelector('#carga [name="fecha_dia"]').value
    };
    
    $.ajax({
	   url:   './app_capa/app_capa_editar.php',
        type:  'post',
        data: parametros,
        error:  function (response){alert('error al contactar al servidor');},
        success:  function (response)
        {   
            var _res = $.parseJSON(response);
            //console.log(_res);
            for(_nm in _res.mg){alert(_res.mg[_nm]);}
            if(_res.res=='exito'){
                //Hacer algo luego de editar?
                
                accionCargarCapaPublicada('',_res.data.id);
                
    			//alert('wmseditado');            
            }else{
                alert('error asf0jg4fcn02h');
            }
        }
    })
    
}





function editarDescripcionCapa(idCapa, descripcion){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idCapa,
        'descripcion': nuevaDescripcion
    };

    editarCapa(parametros);
}

function editarCamposCapa(idCapa, descripcion){
    var idMarco = getParameterByName('id');
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    var nuevaDescripcion = document.getElementById('capaDescripcion').value;
    
    _inps=document.querySelectorAll('#camposident #renombrar');
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco': idMarco,
        'id': idCapa,
    };
    
    
    
    _cant=0;
    for(_ni in _inps){
    	if(typeof _inps[_ni] != 'object'){continue;}
		_nom=_inps[_ni].getAttribute('nom');
		_nom=_nom.replace('texto','text');
		_nom=_nom.replace('numero','num');
		parametros['nom_col_'+_nom]=_inps[_ni].value;
		_cant++;
    }
	if(_cant>0){
    	editarCapa(parametros);
    }
}


function editarCamposNombresCapa(){
	

    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
    
    
    
    var parametros = {
        'codMarco': _CodMarco,
        'idMarco':  getParameterByName('id'),
        'id': idCapa,
    };
    
    _inps=document.querySelectorAll('#configurarCampos input[editado="si"]');
    for(_ni in _inps){
    	if(typeof _inps[_ni] != 'object'){continue;}
		_nom=_inps[_ni].getAttribute('name');
		parametros[_nom]=_inps[_ni].value;
    }
    
	editarCapa(parametros);
    
	
}

function editarCapa(parametros){
    $.ajax({
            url:   './app_capa/app_capa_editar.php',
            type:  'post',
            data: parametros,
            error:  function (response){alert('error al contactar al servidor');},
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                for(_nm in _res.mg){alert(_res.mg[_nm]);}
                if(_res.res=='exito'){
                    //Hacer algo luego de editar?
                }else{
                    alert('error asf0jg4fcn02h');
                }
            }
    });
}

function publicarCapaQuery(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa')
    };
    
    $.ajax({
            url:   './app_capa/app_capa_publicar.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res!='exito'){
                    alert('error asf0jofvg4fcn02h');
                }
            }
    });
}

function publicarCapa(_this){
    var idCapa = document.getElementById('divCargaCapa').getAttribute('idcapa');
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
            
            if(_res.res == 'exito'){
                publicarCapaQuery();

                accionCancelarCargarNuevaCapa(_this);
                alert("Capa publicada");
            } else {
                alert ("La capa no tiene shapefile cargado");
            }
        }
    });
}

function guardarSLD(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var colorRelleno = document.getElementById('inputcolorrelleno').value;
    var transparenciaRelleno = document.getElementById('inputtransparenciarellenoNumber').value;
    var colorTrazo = document.getElementById('inputcolortrazo').value;
    var anchoTrazo = document.getElementById('inputanchotrazoNumber').value;
    var capaNombre = document.getElementById('capaNombre').value;
    var layerName = capaNombre;
    var styleTitle = '';
    var ruleTitle = '';
    
    //Convertir la transparencia de porcentaje a numero decimal 
    transparenciaRelleno = transparenciaRelleno * 1.0 /100;
    var opacidadRelleno = 1 - transparenciaRelleno;
    
    
    _rules=document.querySelectorAll('#simbologia div[name="rule"]');
    if(Object.keys(_rules).length==0){

    var sld = `<?xml version="1.0" encoding="ISO-8859-1"?>
<StyledLayerDescriptor version="1.0.0"
  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd"
  xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

  <NamedLayer>
	<Name>`+layerName+`</Name>
	<UserStyle>
  	<Title>`+styleTitle+`</Title>
  	<FeatureTypeStyle>
    	<Rule>
      	<Title></Title>
      	<PolygonSymbolizer>
        	<Fill>
          	<CssParameter name="fill">`+colorRelleno+`</CssParameter>
          	<CssParameter name="fill-opacity">`+opacidadRelleno+`</CssParameter>
        	</Fill>
        	<Stroke>
          	<CssParameter name="stroke">`+colorTrazo+`</CssParameter>
          	<CssParameter name="stroke-width">`+anchoTrazo+`</CssParameter>
        	</Stroke>
      	</PolygonSymbolizer>
    	</Rule>
  	</FeatureTypeStyle>
	</UserStyle>
  </NamedLayer>
</StyledLayerDescriptor>`;
    }else{
    	
     var sld = `<?xml version="1.0" encoding="ISO-8859-1"?>
<StyledLayerDescriptor version="1.0.0"
  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd"
  xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

  <NamedLayer>
	<Name>`+layerName+`</Name>
	<UserStyle>
  	<Title>`+styleTitle+`</Title>
  	<FeatureTypeStyle>
  	`;
  	
  	
  	for(_nr in _rules){
		if(typeof _rules[_nr] != 'object'){continue;}
   		sld += `
	   		<Rule>
	         <Name>`+_rules[_nr].querySelector('#etiqueta').value+`</Name>
	         <Title></Title>
	         <ogc:Filter>
	           <ogc:And>
	             <ogc:PropertyIsGreaterThanOrEqualTo>
	               <ogc:PropertyName>`+_rules[_nr].querySelector('#campo').value+`</ogc:PropertyName>
	               <ogc:Literal>`+_rules[_nr].querySelector('#desde').value+`</ogc:Literal>
	             </ogc:PropertyIsGreaterThanOrEqualTo>
	             <ogc:PropertyIsLessThan>
	               <ogc:PropertyName>`+_rules[_nr].querySelector('#campo').value+`</ogc:PropertyName>
	               <ogc:Literal>`+_rules[_nr].querySelector('#hasta').value+`</ogc:Literal>
	             </ogc:PropertyIsLessThan>
	           </ogc:And>
	         </ogc:Filter>
	        <PolygonSymbolizer>
			   <Fill>
				 <CssParameter name="fill">`+_rules[_nr].querySelector('#inputcolorrelleno').value+`</CssParameter>
				 <CssParameter name="fill-opacity">`+_rules[_nr].querySelector('#inputtransparenciarellenoNumber').value/100+`</CssParameter>
			   </Fill>
			   <Stroke>
	          	<CssParameter name="stroke">` +_rules[_nr].querySelector('#inputcolortrazo').value+`</CssParameter>
	          	<CssParameter name="stroke-width">`+_rules[_nr].querySelector('#inputanchotrazoRange').value+`</CssParameter>
	        	</Stroke>
	         </PolygonSymbolizer>
	     </Rule>
	     `;
     
  	}
  	
  	sld += ` 
  	</FeatureTypeStyle>
	</UserStyle>
  </NamedLayer>
</StyledLayerDescriptor>`;

   	//console.log(sld);
    }
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('divCargaCapa').getAttribute('idcapa'),
        'sld': sld
    };
    
    editarCapa(parametros);
}

function cargarListadoCapasPublicadas(){
	
	limpiarMapa();
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
            url:   './app_capa/app_capa_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                console.log(_res);
                for(_nm in _res.mg){alert(_res.mg[_nm]);}
                if(_res.res=='exito'){
                    cargarValoresCapasPublicadas(_res);
                    mostrarListadoCapasPublicadas();
                }else{
                    alert('error asf0jg44ff0gh');
                }
            }
    });
}



/* DEPRECADO
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
*/


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
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){		
                _campocod=_res.data.tablasConf.campo_id_geo;
                _camponom=_res.data.tablasConf.campo_id_humano;

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
                _lista=document.querySelector('#menudatos #lista');	
                for(var _nd in _res.data.elemento){
                    if(_nd == 'geo'){continue;}
                    if(_nd == 'accesoAccion'){continue;}
                    if(_nd == 'acceso'){continue;}
                    if(_nd == 'geotx'){continue;}
                    if(_nd == 'zz_obsoleto'){continue;}

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

                //generarItemsHTML();		
                //generarArchivosHTML();
            }else{
                alert('error dsfg');
            }
        }
    });	
}


function descargarSHP(_idcapa){
	_boton=document.querySelector('#listacapaspublicadas .filaCapaLista[idcapa="'+_idcapa+'"] .botondescarga');
	
	if(_boton.getAttribute('estado')=='generandoshp'){alert('ya estamos generando la descarga de esta capa... paciencia.');return;}
	_boton.setAttribute('estado','generandoshp');
	
    var _parametros = {
        'codMarco':_CodMarco,
        'idcapa': _idcapa
    };		
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_generar_SHP_descarga.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){	
				descargarSHPzip(_res.data.idcapa);
			}
		}
	})	
}

function descargarSHPzip(_idcapa){
	
    var _parametros = {		
        'codMarco':_CodMarco,
        'idcapa': _idcapa
    };		
    
	$.ajax({
        data: _parametros,
        url:   './app_capa/app_capa_generar_SHPzip_descarga.php',
        type:  'post',
        success:  function (response){
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            if(_res.res=='exito'){	
				
				_boton=document.querySelector('#listacapaspublicadas .filaCapaLista[idcapa="'+_res.data.idcapa+'"] .botondescarga');
				_boton.setAttribute('estado','generandoshp');
   
   
				console.log('descarga:'+_res.data.descarga);
				var file_path = _res.data.descarga;
				var a = document.createElement('A');
				a.href = file_path;
				a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
				a.download =_res.data.capa.nombre+'.zip';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
				
			}
		}
	})	
}
