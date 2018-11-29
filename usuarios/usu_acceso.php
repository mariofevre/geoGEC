<?php


?>
<script>
	$('head').append('<link rel="stylesheet" type="text/css" href="./usuarios/usuarios.css?v=2">');
</script>	


<div id='navegador'>
	<div id='configuracion'>
		<a id='botonconfig' onclick='config()'><img src='./img/configurar.png'></a>
	</div>	
	<div id='acceso'>
		<p id='hola'></p>
		<a id='botonacceder' onclick='formUsuario()'><img alt='acceder' src='./img/login.png'></a>
		<a id='botonsalir' onclick='salir()'>salir</a>
	</div>	
</div>

<div id='formacceso'>
	<h1>Bienvenido</h1>
	<a id='cerr' onclick='cerrar(this)'>cerrar</a>
	<div id='dataacceso'>
            <p>log </p><input name='log' autocomplete="off" id="inputUsuarioLogNombre" onkeypress="handleKeyPressUsuario(event)">
            <p>Contraseña </p><input type='password' name='password' autocomplete='off' id="inputUsuarioLogPass" onkeypress="handleKeyPressPass(event)">
            <input id='acceder' type="button" onclick='acceder(this);' value="acceder">
	</div>
	<div id='dataregistro'>		
		<p>Nombre</p><input name='nombre' autocomplete='off'>
		<p>Confirmar Contraseña</p><input type='password' name='password2' autocomplete='off'>
		<p>Apellido</p><input name='apellido' autocomplete='off'>
		<p>Mail de referencia</p><input name='mail' autocomplete='off'>
		
		<a id='registrarse' onclick='registrar(this);'>registrarse</a>
	</div>
	<a id='ampliar' onclick='ampliarUsu(this);'>registrarse como nuevo usuario</a>			
</div>	

<div id='formconfig'>
	<h1>Configuración</h1>
	<a id='cerr' onclick='cerrar(this)'>cerrar</a>
	<div id='permisos'>
		<h2>Configuración de permisos de acceso a la plataforma</h2>
		
		<form id='crearpermiso' onsubmit='crearPermiso(event,this)'>
		<div id='encabezado'>
			<div>Usuario</div>
			<div>a Tabla</div>
			<div>a Elemento</div>
			<div>a la accion</div>
			<div>con nivel</div>
		</div>
		<div id='datos'>
			<select id='usu'>
				<option>-elegir-</option>
			</select><select id='tabla' onchange='actualizarElementos(this.value)'>
				<option>-elegir-</option>
				<option value='general' confirm='Atención, el valor "general" para la tabla de acceso: significa que se le está brindando acceso a toda la plataforma geoGEC'>¡general!</option>
			</select><select id='elemento'>
				<option>-elegir-</option>
				<option value='general' confirm='Atención, el valor "general" para el elemento de acceso: significa que se le está brindando acceso a todo elemento accesible desde esta tabla'>¡general!</option>
			</select><select id='accion'>
				<option>-elegir-</option>
				<option value='general'>¡general!</option>
				<option value='app_docs'>gestión de la documentación</option>
				<option value='app_plan'>gestión de la planificación</option>
			</select><select id='nivel'>
				<option>-elegir-</option>
				<option value='3'>¡3! (administrador)</option>
				<option value='2'> 2  (investigador)</option>
				<option value='1'> 1  (auditor)</option>
			</select>
			<input type='submit' value='crear permiso'>
		</div>	
		</form>
		
		<form id='eliminarpermiso'>
		</form>
		<p>en desarrollo</p>
	</div>
	<div id='perfil'>
		<h2>Configuración de tu perfil de usuario</h2>
		<p>en desarrollo</p>
	</div>	
</div>	

<?php

	ini_set('display_errors',1);
	include_once("./includes/fechas.php");
	include_once("./includes/cadenas.php");
	include_once("./includes/pgqonect.php");

	include_once("./usuarios/usu_validacion.php");
	$Usu= validarUsuario();
	
	$dataU=array();
	if(isset($_SESSION["geogec"]) && isset($_SESSION["geogec"]["usuario"])){
		foreach($_SESSION["geogec"]["usuario"] as $k => $v){
			$dataU[$k]=utf8_encode($v);
		}
	}

?>	

<script>
	var _UsuarioAcceso= $.parseJSON('<?php echo json_encode($Usu['acc']);?>');
	if(_UsuarioAcceso.general.general.general<3){
		_botn=document.querySelector('#configuracion');
		_botn.style.display='none';
		
	}
	
	var _UsuarioA= $.parseJSON('<?php echo json_encode($dataU);?>');
	
	if(_UsuarioA==null){
		_UsuarioA={'nombre':'Anónimo','apellido':''}
	}
	cargarusuario();
	
	function cargarusuario(){
		if(_UsuarioA==null){
			_UsuarioA={'nombre':'Anónimo','apellido':''}
		}
	
		document.querySelector('#hola').innerHTML=_UsuarioA.nombre+" "+_UsuarioA.apellido;
		document.querySelector('#hola').style.display='block';
		document.querySelector('#botonacceder').style.display='none';
		document.querySelector('#botonsalir').style.display='block';
		document.querySelector('#configuracion').style.display='inline-block';
		
		if(_UsuarioA.log==undefined){
			document.querySelector('#hola').style.display='none';
			document.querySelector('#botonacceder').style.display='inline-block';
			document.querySelector('#botonsalir').style.display='none';
			document.querySelector('#configuracion').style.display='none';
		}
		
	}
	
	//funciones del formulario de usuarios
	function formUsuario(){		
		$('head').append('<link id="usucssform" rel="stylesheet" type="text/css" href="./usuarios/usuariosF.css?v=2">');
                
                document.getElementById('acceder').value = "acceder";
                
                var delayInMilliseconds = 500;
                setTimeout(function() {
                    document.getElementById('inputUsuarioLogNombre').focus();
                }, delayInMilliseconds);
	}
        
        function handleKeyPressUsuario(e){
            var key=e.keyCode || e.which;
            if (key==13){
                document.getElementById('inputUsuarioLogPass').focus();
            }
        }
        
        function handleKeyPressPass(e){
            var key=e.keyCode || e.which;
            if (key==13){
                document.getElementById('acceder').focus();
            }
        }
	
	function cerrar(){
		$("#usucssform").remove();
		_inps=document.querySelectorAll('#formacceso input');
		for(_nn in _inps){
			_inps[_nn].value='';
		}
		
		_inps=document.querySelectorAll('#formconfig input');
		for(_nn in _inps){
			_inps[_nn].value='';
		}
		_sel=document.querySelectorAll('#formconfig select');
		for(_nn in _sel){
			if(typeof _sel[_nn] != 'object'){continue;}
			if(_sel[_nn].getAttribute('id')=='nivel'){continue;}
			if(_sel[_nn].getAttribute('id')=='accion'){continue;}
			_opt=_sel[_nn].querySelectorAll('option');
			_c=0;
			for(_no in _opt){
				_c++;
				if(_c==1){continue;}
				if(typeof _opt[_no] != 'object'){continue;}
				_opt[_no].parentNode.removeChild(_opt[_no]);
			}
		}
		
	}
	
	function ampliarUsu(_this){
		_this.parentNode.querySelector('#dataregistro').style.display='block';
		_this.parentNode.querySelector('#acceder').style.display='none';
		_this.style.display='none';
	}
	
	function verayuda(_this){
		_this.parentNode.querySelector('#ayuda').style.display='block';
	}
	
	function acceder(_this){		
		
		var parametros = {
			'log': _this.parentNode.parentNode.querySelector('input[name="log"]').value,
			'password': _this.parentNode.parentNode.querySelector('input[name="password"]').value
		};
		
		$.ajax({
			data:  parametros,
			url:   './usuarios/usu_acceso_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				console.log(_res);
				if(_res.res=='exito'){	
					_UsuarioA= _res.data;
					cargarusuario();
					_UsuarioAcceso= _res.data.permisos.acc;
					actualizarPermisos();//en index_consultas.php
					cerrar();
					
				}else{
					alert('error')
				}
			}
		});
	
	}

	function salir(){		
		
		var parametros = {
		};
		
		$.ajax({
			data:  parametros,
			url:   './usuarios/usu_salir_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				console.log(_res);	
				_UsuarioA= Array();
				_UsuarioA.nombre= "Anónimo";
				_UsuarioA.apellido= "";
				cargarusuario();
				actualizarPermisos();//en index_consultas.php
				cerrar();
			}
		});
	
	}
	
	
		
	function registrar(_this){		
		
		_stop='no';
		_form=_this.parentNode.parentNode;
		if(_form.querySelector('input[name="password"]').value !=_form.querySelector('input[name="password2"]').value){
			_form.querySelector('input[name="password2"]').backgroundColor='#fda';
			alert('no coinciden las contraseñas');
			_stop='si';
		}
		
		if(_form.querySelector('input[name="password"]').value.lenght < 4){
			_form.querySelector('input[name="password"]').backgroundColor='#fda';
			alert('la contraseña requiere al menos 4 caracteres');
			_stop='si';
		}
		
		if(_form.querySelector('input[name="nombre"]').value.lenght < 1){
			_form.querySelector('input[name="nombre"]').backgroundColor='#fda';
			alert('falta ingresar su nombre');
			_stop='si';
		}
		
		if(_form.querySelector('input[name="apellido"]').value.lenght < 1){
			_form.querySelector('input[name="apellido"]').backgroundColor='#fda';
			alert('falta ingresar su nombre');
			_stop='si';
		}
		
		if(_form.querySelector('input[name="mail"]').value.lenght < 6){
			_form.querySelector('input[name="mail"]').backgroundColor='#fda';
			alert('falta ingresar su mail');
			_stop='si';
		}
		
		if(_stop=='si'){
			return;
		}
		
		var parametros = {
			'log': _form.querySelector('input[name="log"]').value,
			'password': _form.querySelector('input[name="password"]').value,
			'password2': _form.querySelector('input[name="password2"]').value,
			'nombre': _form.querySelector('input[name="nombre"]').value,
			'apellido': _form.querySelector('input[name="apellido"]').value,
			'mail': _form.querySelector('input[name="mail"]').value
		};
		
		$.ajax({
			data:  parametros,
			url:   './usuarios/usu_registro_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				console.log(_res);
				if(_res.res=='exito'){
					_UsuarioA= _res.data;
					acceder(_this);	
					cerrar();					
				}else{
					alert('error')
				}
				
			}
		});
	
	}	
</script>	
<script>
	//funciones de configuración
	function config(){
		
		if(_UsuarioAcceso.general.general.general<3){return;}
		$('head').append('<link id="usucssform" rel="stylesheet" type="text/css" href="./usuarios/usuariosC.css?v=3">');
		actualizarusuarios();
		actualizarTablas();
		actualizarAccesos();
	}
	
	function actualizarusuarios(){		
		$.ajax({
			url:   './usuarios/usu_consulta_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
					console.log(_res);
				if(_res.res=='exito'){
					formCrearAccesoUsus(_res);
				}else{
					alert('error asfffgh');
				}
			}
		});	
	}
	
	function formCrearAccesoUsus(_usuarios){
			//abre el formulario para crear permisos de acceso
			
			_sel=document.querySelector('form#crearpermiso select#usu');
			for(_nu in _usuarios.data){
				_opt=document.createElement('option');
				_opt.setAttribute('value',_usuarios.data[_nu].id);
				_opt.innerHTML=_usuarios.data[_nu].apellido+', '+_usuarios.data[_nu].nombre+',  <span id="log">'+_usuarios.data[_nu].log+'</span>';
				_sel.appendChild(_opt);
			}	
	}
		
	function actualizarTablas(){	
		var _parametros = {
			'selecTabla':'',
			'selecElemCod':'',
			'selecElemId':''		
		};
		
		$.ajax({
			url:   'consulta_tablas.php',
			type:  'post',
			data: _parametros,
			success:  function (response){
				var _res = $.parseJSON(response);
					console.log(_res);
				if(_res.res=='exito'){
					formCrearAccesoTablas(_res.data);
				}else{
					alert('error asfffgh');
				}
			}
		});	
	}
	
	
	function formCrearAccesoTablas(_tablas){
			//abre el formulario para crear permisos de acceso
			
			_sel=document.querySelector('form#crearpermiso select#tabla');
			for(_nu in _tablas.tablas.est){
				 _tab=_tablas.tablas.est[_nu];
				_opt=document.createElement('option');
				_opt.setAttribute('value',_tab);
				_opt.innerHTML=_tablas.tablasConf[_tab].nombre_humano;
				_sel.appendChild(_opt);
			}	
	}
	
	
	function actualizarElementos(_tabla){
		
		_parametros={
			'tabla':_tabla
		}
	
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
					_sel=document.querySelector('form#crearpermiso select#elemento');
					_sel.innerHTML='';
					_aaa=document.createElement('option');
						_aaa.setAttribute('value','general');
						_aaa.setAttribute('conf','Atención, el valor "general" para el elemento de acceso: significa que se le está brindando acceso a todo elemento accesible desde esta tabla');
						_aaa.innerHTML='¡general!';
						_sel.appendChild(_aaa);
					for(_no in _res.data.centroidesOrden){
						_nc=_res.data.centroidesOrden[_no];
						_dat=_res.data.centroides[_nc];
						_aaa=document.createElement('option');
						_aaa.setAttribute('value',_dat.cod);
						_aaa.innerHTML=_dat.nom;
						_sel.appendChild(_aaa);				
					}
				}
			}
		})			
	}

</script>
<script>	
	function actualizarAccesos(){
		$.ajax({
			//data: _parametros,
			url:   './usuarios/acc_consulta_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);			
				console.log(_res);
				for(_nm in _res.mg){alert(_res.mg[_nm]);}
				if(_res.res=='err'){
				}else{
					//cargaContrato();	
					_form=document.querySelector('form#eliminarpermiso');
					_form.innerHTML='';
					
					for(_po in _res.data.permisosOrden){
						_pi = _res.data.permisosOrden[_po];
						_pdat=_res.data.permisos[_pi];
						
						_fil=document.createElement('div');
						_fil.setAttribute('accid',_pi);
						if(_pdat.zz_borrada=='1'){
							_fil.setAttribute('borrada','si');
						}
						_aaa=document.createElement('span');
						_aaa.innerHTML=_pdat.U_nom
						_fil.appendChild(_aaa);		
						
						_aaa=document.createElement('span');
						_aaa.innerHTML=_pdat.tabla
						_fil.appendChild(_aaa);		
						
						_aaa=document.createElement('span');
						_aaa.innerHTML=_pdat.elemento
						_fil.appendChild(_aaa);		
						
						_aaa=document.createElement('span');
						_aaa.innerHTML=_pdat.accion
						_fil.appendChild(_aaa);		
						
						_aaa=document.createElement('span');
						_aaa.innerHTML=_pdat.nivel
						_fil.appendChild(_aaa);		
			
						if(_pdat.zz_borrada!='1'){
							_aaa=document.createElement('input');
							_aaa.setAttribute('type','submit');
							_aaa.setAttribute('onclick','revocarPermiso(event,this)');
							_aaa.value='revocar permiso';
							_aaa.innerHTML=_pdat.nivel
							_fil.appendChild(_aaa);		
						}
													
						_form.appendChild(_fil);			
					}
				}
			}
		})			
	}
</script>
<script>
	function revocarPermiso(_event,_this){
		_event.preventDefault();
		_idacc=_this.parentNode.getAttribute('accid');
		_parametros={
				'id_acc':_idacc
			}
			$.ajax({
				data: _parametros,
				url:   './usuarios/acc_revocar_ajax.php',
				type:  'post',
				success:  function (response){
					var _res = $.parseJSON(response);			
					console.log(_res);
					for(_nm in _res.mg){alert(_res.mg[_nm]);}
					if(_res.res=='err'){
						
					}else if(_res.res=='exito'){
						actualizarAccesos();
					}
				}
			})	
	}

	function crearPermiso(_event,_this){
		_event.preventDefault();
		/*if(_this.querySelector('#tabla').getAttribiute('confirm')!=undefined){
			if(!confirm(_this.querySelector('#tabla').getAttribiute('confirm'))){return;}
		}
		if(_this.querySelector('#elemento').getAttribiute('confirm')!=undefined){
			if(!confirm(_this.querySelector('#elemento').getAttribiute('confirm'))){return;}
		}*/
		
		_parametros={
			'id_usu':_this.querySelector('#usu').value,
			'tabla':_this.querySelector('#tabla').value,
			'elemento':_this.querySelector('#elemento').value,
			'accion':_this.querySelector('#accion').value,
			'nivel':_this.querySelector('#nivel').value,
		}
	
		$.ajax({
			data: _parametros,
			url:   './usuarios/acc_crear_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);			
				console.log(_res);
				for(_nm in _res.mg){alert(_res.mg[_nm]);}
				if(_res.res=='err'){
					
				}else if(_res.res=='exito'){
					actualizarAccesos();
				}
			}
		})					
		
		
	}
</script>		