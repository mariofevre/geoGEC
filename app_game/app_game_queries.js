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


function consultarPermisos(){
    var _IdMarco = getParameterByName('id');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco	
    };
    $.ajax({
        url:   './app_capa/app_capa_consultar_permisos.php',
        type:  'post',
        data: _parametros,
        error:  function (response){alert('error al consultar el servidor');},
        success:  function (response){
            var _res = $.parseJSON(response);
            for(_nm in _res.mg){
                alert(_res.mg[_nm]);
            }
            for(_na in _res.acc){
            	console.log(_res.acc[_na])
	          	procesarAcc(_res.acc[_na]);
            }
            if(_res.res!='exito'){
                alert('error al consultar la base de datos');
            }
        }
    });	
}
consultarPermisos();



function generarNuevaSesion(){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '0';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_sesion.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    if (_res.data != null){
                        cargarValoresSesion(_res);
                    } else {
                        generarNuevaSesion();
                    }
                }else{
                    alert('error asf0jg44f9ytfgh');
                }
            }
    });
}

function generarNuevasesion(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco
    };
    
    $.ajax({
            url:   './app_game/app_game_generar_sesion.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    asignarIdSesion(_res.data.id);
                }else{
                    alert('error asf0jg3444ffgh');
                }
            }
    });
}

function consultarSEsionParaModificar(idsesion){
    //consultar si ya existe un indicador sin publicar para este autor y sino crearlo
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': idsesion
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_sesion.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    if (_res.data != null){
                        //TODO
                        cargarValoresSesionExist(_res);
                    }
                }else{
                    alert('error asf0jg44f9ytfgh');
                }
            }
    });
}

function editarSesion(_parametros){
    $.ajax({
            url:   './app_game/app_game_editar_sesion.php',
            type:  'post',
            data: _parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    //Hacer algo luego de editar?
                }else{
                    alert('error al ejecutar la edición');
                }
            }
    });
}


function publicarSesion(_this){
    var idindicador = document.getElementById('formEditarIndicadores').getAttribute('idindicador');
    var _CodMarco = getParameterByName('cod');
    _parametros = {
            'codMarco':_CodMarco,
            'id': idindicador
    };
    $.ajax({
        url:   './app_game/app_game_consultar_publicable.php',
        type:  'post',
        data: _parametros,
        success:  function (response){
            var _res = $.parseJSON(response);
            
            if(_res.res == 'exito'){
                publicarIndicadorQuery(_this);
            } else {
                var mensajeError = "El indicador no cumple los requisitos para publicar:\n";
                for(var _nm in _res.mg)
                {
                    mensajeError += _res.mg[_nm];
                }
                
                mensajeError = mensajeError.replace("\\n","\n").slice(0, -2);
                
                alert (mensajeError);
            }
        }
    });
}

function publicarIndicadorQuery(_this){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');

    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'id': document.getElementById('formEditarSEsiones').getAttribute('idsesion')
    };
    
    $.ajax({
            url:   './app_game/app_game_publicar_sesion.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res == 'exito'){
                    accionCreaCancelar(_this);
                    alert("Indicador publicado");
                } else {
                    alert('error asf0jofvg24fcn02h');
                }
            }
    });
}

function cargarListadoSesionesPublicadas(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_listado_sesiones.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                    
                    cargarValoresSesionesPublicadas(_res, "accionSesionPublicadaSeleccionada");
                    mostrarListadoSesionesPublicadas();
                }else{
                    alert('error asf0jg44ff0gh');
                }
            }
    });
}


function cargarListadoIndicadoresPublicados(){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    var zz_publicada = '1';
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': zz_publicada
    };
    
    $.ajax({
            url:   './app_ind/app_ind_consultar_listado.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                //console.log(_res);
                if(_res.res=='exito'){
                	for(_in in _res.data){
	                	_op=document.createElement('option')
	                	_op.value=_res.data[_in].id;
	                	_op.innerHTML=_res.data[_in].id+' - '+_res.data[_in].nombre;
	                	_op.title=_res.data[_in].descripcion;
                    	document.querySelector('#sesionIndicadorAsociado').appendChild(_op);
                   }
                }else{
                    alert('error asf0jg44ff0gh');
                }
            }
    });
}
cargarListadoIndicadoresPublicados(); //consulta los indicadores disponibles y los carga en el formulario correspondiente como opciones.
	
function refrescarSesionActiva(){
    var idindicador = document.getElementById('sesionActivo').getAttribute('idsesion');
    cargarInfoIndicador(idindicador);
}

function refrescarDatosSesionActiva(_res){
	if(idSesion!=_res.data.idSesion){return;}
}

function cargarSesionPublicada(idSesion){
    cargarInfoSesion(idSesion);
}


var _DataSesion;
function cargarInfoSesion(idSesion){
    var idMarco = getParameterByName('id');
    var codMarco = getParameterByName('cod');
    
    var parametros = {
        'codMarco': codMarco,
        'idMarco': idMarco,
        'zz_publicada': '1',
        'idSesion': idSesion
    };
    
    $.ajax({
            url:   './app_game/app_game_consultar_sesion.php',
            type:  'post',
            data: parametros,
            success:  function (response)
            {   
                var _res = $.parseJSON(response);
                
                for(var _nm in _res.mg){
	                alert(_res.mg[_nm]);
	            }
	            for(var _na in _res.acc){
	                procesarAcc(_res.acc[_na]);
	            }
                //console.log(_res);
                if(_res.res=='exito'){
                	_DataSesion=_res.data.sesion;
                	accionSesionPublicadaCargar(_DataSesion.id);
                    
                }else{
                    alert('error asf0jg44f8f0gh');
                }
            }
    });
}

