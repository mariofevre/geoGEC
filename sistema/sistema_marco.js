/**
* 
*     funciones de consulta general del sistema
*  
* @package    	geoGEC
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on https://github.com/mariofevre/TReCC-Mapa-Visualizador-de-variables-Ambientales
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

//Esta funcion obtiene el QueryString en base a su nombre de la url, es case insensitive
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var results = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", 'i').exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

var _IdMarco = getParameterByName('id');
var _CodMarco = getParameterByName('cod');

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
        success:  function (response) {
            var _res = $.parseJSON(response);
            console.log(_res);
            for(var _nm in _res.mg) {
                    alert(_res.mg[_nm]);
            }

            if(_res.res=='exito') {		
                document.querySelector('#menutablas #titulo').innerHTML=_res.data.elemento.nombre_oficial;
                document.querySelector('#menutablas #descripcion').innerHTML=_res.data.elemento.nombre;
                //generarItemsHTML();		
                //generarArchivosHTML();
            } else {
                alert('error dsfg');
            }
        }
    });	
}
consultarMarco();

function procesarAcc(_acc){
	
	if(_acc=='login'){
		alert('su sesión ha caducado. Por favor vuela a loguearse.');
		window.location.assign('./index.php?est=est_02_marcoacademico&cod='+_CodMarco);
	}else{
		alert('aación desconocida');
	}	
}


function formatearNumero(_numero,_dec){
    if (!_numero || _numero == 'NaN') return '-';
    if (_numero == 'Infinity') return '&#x221e;';
    _numero = _numero.toString().replace(/\$|\,/g, '');
    if (isNaN(_numero))
    	_numero = "0";
    sign = (_numero == (_numero = Math.abs(_numero)));
    _numero = Math.floor(_numero * 100 + 0.50000000001);
    cents = _numero % 100;
    _numero = Math.floor(_numero / 100).toString();
    
    if(_dec==0){
    	for (var i = 0; i < Math.floor((_numero.length - (1 + i)) / 3) ; i++)
	        _numero = _numero.substring(0, _numero.length - (4 * i + 3)) + '.' + _numero.substring(_numero.length - (4 * i + 3));
	   return (((sign) ? '' : '-') + _numero);	   	
    }else if(_dec==1){
	    for (var i = 0; i < Math.floor((_numero.length - (1 + i)) / 3) ; i++)
	        _numero = _numero.substring(0, _numero.length - (4 * i + 3)) + '.' + _numero.substring(_numero.length - (4 * i + 3));
	    return (((sign) ? '' : '-') + _numero + ',' + cents);
    }else{
    	 if (cents < 10)
	        cents = "0" + cents;
	    for (var i = 0; i < Math.floor((_numero.length - (1 + i)) / 3) ; i++)
	        _numero = _numero.substring(0, _numero.length - (4 * i + 3)) + '.' + _numero.substring(_numero.length - (4 * i + 3));
	    return (((sign) ? '' : '-') + _numero + ',' + cents);	
    }
    

}
