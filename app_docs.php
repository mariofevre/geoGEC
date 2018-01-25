<?php 
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);
ini_set('display_errors', '1');
// verificación de seguridad 
//include('./includes/conexion.php');
session_start();

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");

$COD = isset($_GET['cod'])?$_GET['cod'] : '';
$ID = isset($_GET['id'])?$_GET['id'] : '';
if($ID==''&&$COD==''){
	header('location: ./index.php');
}

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<head>
	<title>GEC - Plataforma Geomática</title>
	<?php include("./includes/meta.php");?>
	<link href="./css/mapauba.css" rel="stylesheet" type="text/css">
	<link href="./css/BaseSonido.css" rel="stylesheet" type="text/css">
	<link href="./css/ad_navega.css" rel="stylesheet" type="text/css">	
	<link href="./css/tablarelev.css" rel="stylesheet" type="text/css">
	<link rel="manifest" href="pantallahorizontal.json">
	<link href="./css/BA_salidarelevamiento.css" rel="stylesheet" type="text/css">
	<link href="./css/geogecindex.css" rel="stylesheet" type="text/css">
	<link href="./css/geogec_app_docs.css" rel="stylesheet" type="text/css">	
	
	<style type='text/css'>					
	
	</style>	
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>

<div id="pageborde">
	<div id="page">
		<div id='cuadrovalores'>
			<div class='fila' id='encabezado'>
				<h2>geoGEC</h2>
				<p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
			</div>
			
			<div id='elemento'>
				<img src='./img/app_docs_hd.png' style='float:left;'>
				<h2 id='titulo'>Gestor de documentos de referencia</h2>
				<div id='descripcion'>espacio para cargar y gestionar docuemtno de trabajo de cada proyecto de investigación</div>
			</div>	
		</div>
		<div id='menutablas'>
			<h1 id='titulo'>- nombre de proyecto -</h1>
			<p id='descripcion'>- descripcion de proyecto -</p>
		</div>	
		<div id='portamapa'>
			<div id='titulomapa'><p id='tnombre'></p><h1 id='tnombre_humano'></h1><p id='tdescripcion'></p><b><p id='tseleccion'></p></b></div>
		</div>
		<div id='modelos'>
			
				<div
					class='item'
					idit='nn'
					draggable="true"
					ondragstart="dragcaja(event);bloquearhijos(event,this);"
					ondragleave="limpiarAllowFile()"
					ondragover="allowDropFile(event,this)"
					ondrop='dropFile(event,this)'
				>
					<h3 onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>nombre de la caja</h3>
					<p onmouseout='desaltar(this)' onmouseover='resaltar(this)' onclick='editarI(this)'>descipcion del contenido de la caja</p>
					<div class='documentos'>
					</div>
					<div 
						class='hijos'
						ondrop="drop(event,this)"
						ondragover="allowDrop(event,this)"
						ondragleave="limpiarAllow()" 
					></div>
				</div>
			</div>
			
			<div id="archivos">
				
				<form action='' enctype='multipart/form-data' method='post' id='uploader' ondragover='resDrFile(event)' ondragleave='desDrFile(event)'>
					<div id='contenedorlienzo'>									
						<div id='upload'>
							<label>Arrastre todos los archivos aquí.</label>
							<input multiple='' id='uploadinput' type='file' name='upload' value='' onchange='cargarCmp(this);'></label>
						</div>
					</div>
				</form>
				<div id="listadosubiendo">
					<label>archivos subiendo...</label>
				</div>
				<div id="listadoaordenar">
					<label>archivos subidos.</label>
				</div>
					
				<div id="eliminar"
					ondragover="allowDropFile(event,this)"
					ondragleave="limpiarAllowFile()"
					ondrop='dropTacho(event,this)'
				>
					<br>X
					<span>tacho de basura</span>
				</div>		
			</div>	
			
								
			<div id="contenidoextenso" idit='0'>
				<a id='botonanadir' onclick='anadirItem()'>+ nueva caja</a>
				<div 
					class='hijos'
					nivel="0"
					ondrop="drop(event,this)" 
					ondragover="allowDrop(event,this);resaltaHijos(event,this)" 
					ondragleave="desaltaHijos(this)" 
				></div>
			</div>
		
		

	</div>	
</div>	


	

	<form id="editoritem" onsubmit="guardarI(event,this)">
		<label>Nombre de la caja</label>
		<input name='nombre'>
		<input name='id' type='hidden'>
		<label>Descripcion del contenido de la caja</label>
		<textarea name='descripcion'></textarea>
		<a id='botoncierra' onclick='cerrar(this)'>cerrar</a>
		<input type='submit' value='guardar'>
		<a id='botonelimina' onclick='eliminarI(event,this)'>eliminar</a>
	</form>
	
	<script type='text/javascript'>
	///funciones de consulta general del sistema
	var _IdMarco='<?php echo $ID;?>';
	var _CodMarco='<?php echo $COD;?>';	
	function consultarMarco(){

		_parametros = {
			'id': _IdMarco,
			'cod': _CodMarco,
			'tabla':'est_02_marcoacademico'
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
					
					document.querySelector('#menutablas #titulo').innerHTML=_res.data.elemento.nombre_oficial;
					document.querySelector('#menutablas #descripcion').innerHTML=_res.data.elemento.nombre;
					//generarItemsHTML();		
					//generarArchivosHTML();
				}else{
					alert('error dsfg');
				}
			}
		})	
	}
	consultarMarco();
	</script>
<script type='text/javascript'>

	///funciones para cargar información base
		var _IdMarco='<?php echo $ID;?>';
		var _CodMarco='<?php echo $COD;?>';	
		var _Items=Array();
		var _Orden=Array();
		
		function cargaBase(){
			
			document.querySelector('#contenidoextenso > .hijos').innerHTML='';			
			document.querySelector('#listadosubiendo').innerHTML='<label>archivos subiendo...</label>';
			document.querySelector('#listadoaordenar').innerHTML='<label>archivos subidos.</label>';

			_parametros = {
				'idMarco': _IdMarco,
				'codMarco': _CodMarco
			};
			
			$.ajax({
				data: _parametros,
				url:   './app_docs/app_docs_consulta.php',
				type:  'post',
				success:  function (response){
					var _res = $.parseJSON(response);
					console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){		
						_Items=_res.data.psdir;
						_Orden=_res.data.orden;
						
						generarItemsHTML();		
						generarArchivosHTML();
					}else{
						alert('error dsfg');
					}
				}
			})	
		}
		
		cargaBase();
		
		
		function generarArchivosHTML(){
			
			if(Object.keys(_Items[0].archivos).length>0){
				for(_na in _Items[0].archivos){
					_dat=_Items[0].archivos[_na];
					console.log(_dat);
					_aaa=document.createElement('a');
					_aaa.innerHTML=_dat.nombre;
					_aaa.setAttribute('href',_dat.archivo);
					_aaa.setAttribute('download',_dat.nombre);
					_aaa.setAttribute('draggable',"true");
					_aaa.setAttribute('ondragstart',"dragFile(event)");
					_aaa.setAttribute('idfi',_dat.id);
					_aaa.setAttribute('class','archivo');					
					document.getElementById('listadoaordenar').appendChild(_aaa);
				}			
			}
		}
		
		function generarItemsHTML(){
			//genera un elemento html por cada instancia en el array _Items
			for(_nO in _Orden.psdir){
				
				_ni=_Orden.psdir[_nO];
				
				_dat=_Items[_ni];
				_clon=document.querySelector('#modelos .item').cloneNode(true);
				
				_clon.setAttribute('idit',_dat.id);
				
				if(_dat.nombre==null){_dat.nombre='- caja sin nombre -';}
				
				_clon.querySelector('h3').innerHTML=_dat.nombre;
				if(_dat.descripcion==null){_dat.descripcion='- caja sin descripción -';}
				_clon.querySelector('p').innerHTML=_dat.descripcion;
				_clon.setAttribute('nivel',"1");
				
				for(_na in _dat['archivos']){
					_dar=_dat['archivos'][_na];
					_aa=document.createElement('a');
					
					_aa.innerHTML=_dar.nombre;
					_aa.setAttribute('href',_dar.archivo);
					_aa.setAttribute('download',_dar.nombre);
					_aa.setAttribute('draggable',"true");
					_aa.setAttribute('ondragstart',"dragFile(event)");
					_aa.setAttribute('idfi',_dar.id);
					_aa.setAttribute('class','archivo');
					_clon.querySelector('.documentos').appendChild(_aa);
				}
				
				
				document.querySelector('#contenidoextenso > .hijos').appendChild(_clon);
			}
			  
			//anida los itmes genreados unos dentro de otros
			for(_nO in _Orden.psdir){
				_ni=_Orden.psdir[_nO];
				_el=document.querySelector('#contenidoextenso > .hijos > .item[idit="'+_Items[_ni].id+'"]');
				
				if(_Items[_ni].id_p_ref_02_pseudocarpetas!='0'){
					//alert(_Items[_ni].id_p_ESPitems_anidado);
					_dest=document.querySelector('#contenidoextenso > .hijos .item[idit="'+_Items[_ni].id_p_ref_02_pseudocarpetas+'"] > .hijos');
					_niv=_dest.parentNode.getAttribute('nivel');
					_niv++;
					_dest.appendChild(_el);
				}
			}
				
			_itemscargados=document.querySelectorAll('#contenidoextenso > .hijos .item');
			
			for(_nni in _itemscargados){
				if(typeof _itemscargados[_nni]=='object'){
					_esp=document.createElement('div');				
					_esp.setAttribute('class','medio');
					_esp.innerHTML='<div class="submedio"></div>';
					_esp.setAttribute('ondragover',"allowDrop(event,this);resaltaHijos(event,this)");
					_esp.setAttribute('ondragleave',"desaltaHijos(this)");
					_esp.setAttribute('ondrop',"drop(event,this)");  
					
					_itemscargados[_nni].parentNode.insertBefore(_esp, _itemscargados[_nni]);
					
					
				}
			}
		}
		
	
	</script>
	
	<script type='text/javascript'>
	///funciones para editar y crear items
	
		function resaltar(_this){
			//realta el div del item al que pertenese un título o una descripcion
			
			_dests=document.querySelectorAll('[resaltado="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('resaltado');
				}
			}
			_this.parentNode.setAttribute('resaltado','si');
			
		}
		function desaltar(_this){
			//realta el div del item al que pertenese un título o una descripcion
			_dests=document.querySelectorAll('[resaltado="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('resaltado');
				}
			}
			
		}
		function editarI(_this){				
			//abre el formulario para edittar item
			_idit=_this.parentNode.getAttribute('idit');
			_form=document.querySelector('#editoritem');
			_form.style.display='block';
			_form.querySelector('input[name="nombre"]').value=_Items[_idit].nombre;
			_form.querySelector('input[name="id"]').value=_Items[_idit].id;
			_form.querySelector('[name="descripcion"]').value=_Items[_idit].descripcion;
		}
		
		
		function cerrar(_this){
			//cierra el formulario que lo contiene
			_this.parentNode.style.display='none';
		}
		
		function eliminarI(_event,_this){
			if (confirm("¿Eliminar item y sus archivos asociados? \n (los ítems anidados quedarán en la raiz)")==true){
				
				_event.preventDefault();
				
				var _this=_this;
				
				_parametros = {
					"id": _this.parentNode.querySelector('input[name="id"]').value,
					"accion": "borrar",
					"tipo": "item",
					"idMarco": _IdMarco,
					'codMarco': _CodMarco
				};
				$.ajax({
					url:   './app_docs/app_docs_borraritem.php',
					type:  'post',
					data: _parametros,
					success:  function (response){
						var _res = $.parseJSON(response);
							console.log(_res);
						if(_res.res=='exito'){	
							cerrar(_this);
							cargaBase();
						}else{
							alert('error asfffgh');
						}
					}
				});
				//envía los datos para editar el ítem
				
			}
		}
		
		
		
		function guardarI(_event,_this){// ajustado geogec
			_event.preventDefault();
			console.log(_this);
			var _this=_this;
			_parametros = {
				"idMarco":_IdMarco,
				'codMarco': _CodMarco,
				"id": _this.querySelector('input[name="id"]').value,
				"nombre": _this.querySelector('input[name="nombre"]').value,
				"descripcion": _this.querySelector('[name="descripcion"]').value
			};
			$.ajax({
				url:   './app_docs/app_docs_cambiaritem.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
						console.log(_res);
					if(_res.res=='exito'){	
						cerrar(_this.querySelector('#botoncierra'));
						cargaBase();
					}else{
						alert('error asdfdasf');
					}
				}
			});
			//envía los datos para editar el ítem
			
		}
		
		function anadirItem(){//ajustado a geogec
			_parametros = {
				"idMarco":_IdMarco,
				'codMarco': _CodMarco
			};
			
			$.ajax({
				url:   './app_docs/app_docs_crearitem.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
					console.log(_res);
					for(_nm in _res.mg){
						alert(_res.mg[_nm]);
					}
					if(_res.res=='exito'){	
						cargaBase();
					}
				}
			})	
		}
		
		
	</script>	
	
	
	
	<script type='text/javascript'>
		///funciones para gestionar drop en el tacho
		function dropTacho(_event,_this){//ajustado geogec
			
			_event.stopPropagation();
    		_event.preventDefault();    		
    		
    		limpiarAllowFile();
    		
    		if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){
    		
    			if(confirm('¿Confirma que quiere eliminar el archivo del panel?')==true){
    				
    				_parametros={
				    	"idfi":JSON.parse(_event.dataTransfer.getData("text")).id,
				    	"tipo":JSON.parse(_event.dataTransfer.getData("text")).tipo,
				    	"idMarco":_IdMarco,
				    	'codMarco': _CodMarco,
				    	"accion":'borrar'
				    };
				    
			 		$.ajax({
						url:   './app_docs/app_docs_borrararchivo.php',
						type:  'post',
						data: _parametros,
						success:  function (response){
							var _res = $.parseJSON(response);
								console.log(_res);
							if(_res.res=='exito'){	
								cargaBase();
							}else{
								alert('error asffsvrrfgh');
							}
						}
					});
    				
    			}
    			return;
    			
    		}else if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='item'){
    			
    			if(confirm('¿Confirma que quiere eliminar el Item y todo su contenido?')==true){
    				
    				
    				
    			}
    			return;
    			
    		}
    		
		    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
		    console.log(_DragData);
		    _el=document.querySelector('.archivo[idfi="'+_DragData+'"]');
		    
		    _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idfi');
		    _em=_el.nextSibling;
		    _idit=_this.getAttribute('idit');
		    _ref=document.querySelector('.item[idit="'+_idit+'"] .documentos');
		    _ref.appendChild(_el);

		 }
		
		
	</script>
		
	<script type='text/javascript'>
		///funciones para gestionar drag y drop de archivos
			
		function dragFile(_event){
			//alert(_event.target.getAttribute('idit'));
			_event.stopPropagation();
    		_arr=Array();
			_arr={
				'id':_event.target.getAttribute('idfi'),
				'tipo':'archivo'
			};
			_arb = JSON.stringify(_arr);
    		_event.dataTransfer.setData("text", _arb);
		}
		
		function allowDropFile(_event,_this){
			_event.stopPropagation();
			//console.log(_this.parentNode.getAttribute('idit'));
			//console.log(_event.dataTransfer);
			if(_event.dataTransfer.items[0].kind=='file'){return;}
			if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo'){
				return;
			}
			
			limpiarAllowFile();
			_event.stopPropagation();
			_this.setAttribute('destinof','si');
			_event.preventDefault();
		}
		
		function limpiarAllowFile(){
			_dests=document.querySelectorAll('[destinof="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('destinof');
				}
			}
		}
		function dropFile(_event,_this){// ajustado a geogec
			_event.stopPropagation();
    		_event.preventDefault();
    		    		
    		if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='archivo'){
				return;
			}
    		
		    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
		    
		    _el=document.querySelector('.archivo[idfi="'+_DragData+'"]');
		    
		    //console.log(_DragData);
		   
		    if(_event.target.getAttribute('class')=='hijos'){	
		    	_tar=_event.target;
		    	 _idit=_this.parentNode.getAttribute('idit');
		    	_dest=_tar.parentNode.querySelector('.item[idit="'+_idit+'"] .documentos'); 
		    }else{
		    	 _idit=_this.getAttribute('idit');
		    	 _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idfi');
		    	_dest=document.querySelector('.item[idit="'+_idit+'"] .documentos');
		    }
		    
		    _dest.appendChild(_el);
		    		    			    
		    _parametros={
		    	"idMarco":_IdMarco,
		    	'codMarco': _CodMarco,
		    	"id":_DragData,
		    	"id_anidadoen":_idit
		    };
		    
	 		$.ajax({
				url:   './app_docs/app_docs_localizararchivo.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
						console.log(_res);
					if(_res.res=='exito'){	
						cargaBase();
					}else{
						alert('error asdfdsf');
					}
				}
			});
		    
		  }
	</script>
		
	<script type='text/javascript'>
		///funciones para gestjionar drag y drop de items
		
		function allowDrop(_event,_this){
			//console.log(_this.parentNode.getAttribute('idit'));
			
			console.log(_event.dataTransfer);
			
			if(JSON.parse(_event.dataTransfer.getData("text")).tipo!='item'){
				return;
			}
			
			limpiarAllow();
			
			_event.stopPropagation();
			_this.setAttribute('destino','si');
			_event.preventDefault();
			
		}
		
		function limpiarAllow(){
			_dests=document.querySelectorAll('[destino="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('destino');
				}
			}
		}
		
		function resaltaHijos(_event,_this){
			//realta el div del item al que pertenese un título o una descripcion
			//_this.style.backgroundColor='lightblue';
			_dests=document.querySelectorAll('[destino="si"]');
			for(_nn in _dests){
				if(typeof _dests[_nn]=='object'){
					_dests[_nn].removeAttribute('destino');
				}
			}
			_this.setAttribute('destino','si');
			_event.stopPropagation();
			
		}
		function desaltaHijos(_this){
			//realta el div del item al que pertenese un título o una descripcion
			//_this.style.backgroundColor='#fff';
			_this.removeAttribute('destino');
			_this.parentNode.removeAttribute('destino');
		}
		
		
		function dragcaja(_event){			
			//alert(_event.target.getAttribute('idit'));
			_arr=Array();
			_arr={
				'id':_event.target.getAttribute('idit'),
				'tipo':'item'
			};		
			_arb = JSON.stringify(_arr);

    		_event.dataTransfer.setData("text", _arb);
		}
		
		function bloquearhijos(_event,_this){			
			_idit=JSON.parse(_event.dataTransfer.getData("text")).id;
    		_negados = _this.querySelectorAll('.item[idit="'+_idit+'"] .hijos, .item[idit="'+_idit+'"] .medio');   
    		 		
    		for(_nn in _negados){
    			if(typeof _negados[_nn] == 'object'){
    				_negados[_nn].setAttribute('destino','negado');
    			}
    		}
		}
		
		function desbloquearhijos(_this){
    		_negados=document.querySelectorAll('[destino="negado"]');
    		for(_nn in _negados){
    			if(typeof _negados[_nn] == 'object'){
    				_negados[_nn].removeAttribute('destino');
    			}
    		}
		}	
		
			
		function drop(_event,_this){//ajustado a geogec
			
			_event.stopPropagation();
    		_this.removeAttribute('style');
    		_this.removeAttribute('destino');
    		
    		_event.preventDefault();
    		
    		if(JSON.parse(_event.dataTransfer.getData("text")).tipo=='archivo'){
    			dropFile(_event,_this);
				return;
			}
    		
		    var _DragData = JSON.parse(_event.dataTransfer.getData("text")).id;
		    console.log('u');
		    console.log(_event.dataTransfer.getData("text"));
		    
		    _el=document.querySelector('.item[idit="'+_DragData+'"]');
		    _ViejoIdIt=_el.parentNode.parentNode.getAttribute('idit');
		    _em=_el.previousSibling;
		        
		    _evitar='no';//evita destinos erronos por jerarquia.

		    
		    if(_event.target.getAttribute('class')=='medio'||_event.target.getAttribute('class')=='submedio'){
		    	
		    	if(_event.target.getAttribute('class')=='submedio'){
		    		_tar=_event.target.parentNode;
		    	}else{
		    		_tar=_event.target;
		    	}
		    	
		    	_dest=_tar.parentNode; 
			    _dest.insertBefore(_el,_tar);			    
			    _dest.insertBefore(_em,_el);
			    
		    }else if(_event.target.getAttribute('class')=='hijos'){
		    	
		    	_dest=_event.target;

			    _dest.appendChild(_el);
			    _dest.insertBefore(_em,_el);
		    	
		    	
		    }else{
		    	alert('destino inesperado');
		    	
		    	return;
		    	
		    }
		    
		    _niv=_dest.parentNode.getAttribute('nivel');
		    _niv++;
		    _el.setAttribute('nivel',_niv.toString());
		    		    
		    _NuevoIdIt=_dest.parentNode.getAttribute('idit');
		    
		    _enviejo=document.querySelectorAll('[idit="'+_ViejoIdIt+'"] > .hijos > .item');
		    _serieviejo='';
		    for(_ni in _enviejo){
		    	if(typeof _enviejo[_ni]=='object'){
		    		_serieviejo+=_enviejo[_ni].getAttribute('idit')+',';
		    	}
		    }
		    
		    console.log(_NuevoIdIt);
		    _ennuevo=document.querySelectorAll('[idit="'+_NuevoIdIt+'"] > .hijos > .item');
		    _serienuevo='';
		    for(_ni in _ennuevo){
		    	console.log(_ennuevo[_ni]);
		    	if(typeof _ennuevo[_ni]=='object'){
		    		_serienuevo+=_ennuevo[_ni].getAttribute('idit')+',';
		    	}
		    }
		   
		    _parametros={
		    	"idMarco":_IdMarco,
		    	"codMarco":_CodMarco,
		    	"id":_DragData,
		    	"id_anidado":_NuevoIdIt,
		    	"viejoAnidado":_ViejoIdIt,
		    	"viejoAserie":_serieviejo,
		    	"nuevoAnidado":_NuevoIdIt,
		    	"nuevoAserie":_serienuevo
		    };
		    
	 		$.ajax({
				url:   './app_docs/app_docs_anidaritem.php',
				type:  'post',
				data: _parametros,
				success:  function (response){
					var _res = $.parseJSON(response);
						console.log(_res);
					if(_res.res=='exito'){	
						cargaBase();
					}else{
						alert('error asfffgh');
					}
				}
			});
			//envía los datos para editar el ítem
		}
		
	
		
	</script>
	
	<script type='text/javascript'>
	///funciones para guardar archivos
	
		function resDrFile(_event){
			//console.log(_event);
			document.querySelector('#archivos #contenedorlienzo').style.backgroundColor='lightblue';
		}	
		
		function desDrFile(_event){
			//console.log(_event);
			document.querySelector('#archivos #contenedorlienzo').removeAttribute('style');
		}
		
		var _nFile=0;
		
		var xhr=Array();
		var inter=Array();
		function cargarCmp(_this){
			
			var files = _this.files;
					
			for (i = 0; i < files.length; i++) {
		    	_nFile++;
		    	console.log(files[i]);
				var parametros = new FormData();
				parametros.append('upload',files[i]);
				parametros.append('nfile',_nFile);
				parametros.append('idMarco',_IdMarco);
				parametros.append('codMarco',_CodMarco);
				
				var _nombre=files[i].name;
				_upF=document.createElement('a');
				_upF.setAttribute('nf',_nFile);
				_upF.setAttribute('class',"archivo");
				_upF.setAttribute('size',Math.round(files[i].size/1000));
				_upF.innerHTML=files[i].name;
				document.querySelector('#listadosubiendo').appendChild(_upF);
				
				_nn=_nFile;
				xhr[_nn] = new XMLHttpRequest();
				xhr[_nn].open('POST', './app_docs/app_docs_guardaarchivo.php', true);
				xhr[_nn].upload.li=_upF;
				xhr[_nn].upload.addEventListener("progress", updateProgress, false);
				
				
				xhr[_nn].onreadystatechange = function(evt){
					//console.log(evt);
					
					if(evt.explicitOriginalTarget.readyState==4){
						var _res = $.parseJSON(evt.explicitOriginalTarget.response);
						//console.log(_res);
						
						alert('terminó '+_res.data.nf);
						
						if(_res.res=='exito'){							
							_file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');								
							document.querySelector('#listadoaordenar').appendChild(_file);
							_file.setAttribute('href',_res.data.ruta);
							_file.setAttribute('download',_file.innerHTML);
							_file.setAttribute('draggable',"true");
							_file.setAttribute('ondragstart',"dragFile(event)");
							_file.setAttribute('idfi',_res.data.nid);
							
						}else{
							_file=document.querySelector('#listadosubiendo > a[nf="'+_res.data.nf+'"]');
							_file.innerHTML+=' ERROR';
							_file.style.color='red';
						}
						//cargaTodo();
						//limpiarcargando(_nombre);
					}
				}
				xhr[_nn].send(parametros);
			}			
		}

		function updateProgress(evt) {
			if (evt.lengthComputable) {
				var percentComplete = 100 * evt.loaded / evt.total;		   
				this.li.style.width="calc("+Math.round(percentComplete)+"% - ("+Math.round(percentComplete)/100+" * 6px))";
			} else {
				// Unable to compute progress information since the total size is unknown
			} 
		}

	</script>
	

	<script type='text/javascript'>
		function toogle(_elem){
		    _nombre=_elem.parentNode.parentNode.getAttribute('class');
	
		    elementos = document.getElementsByName(_nombre);
		    for (x=0;x<elementos.length;x++){			
				elementos[x].removeAttribute('checked');
			}
		    _elem.previousSibling.setAttribute('checked','checked');		
		}
	</script>
	
	<script type="text/javascript">	
		//carga el formulario para editar múltiple localizaciones simultáneamente.
		
		var _seleccionDOCSid = new Array();
		var _ultimamarca='';
	</script>

</body>