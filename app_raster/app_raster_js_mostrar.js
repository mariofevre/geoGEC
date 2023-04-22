/**
*
* funciones de operacion de la pagina 
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





function mostrarListadoRaster(){
	
	
	for(_id_cob in  _DataRaster.listado){
		document.querySelector('#cuadrovalores').setAttribute('listadoRaster','si');
		_cont=document.querySelector('#cuadrovalores #listadoRaster');
		_con.innerHTML='';
		_datR=_DataRaster.listado[_id_cob];
		_a=document.createElement('a');
		_a.setAttribute('onclick','cargarRaster("'+_id_cob+'")');
		_a.innerHTML=_datR.nombre+' '+_datR.tipo+' '+_datR.fecha_ano+' '+_datR.fecha_mes;
		_cont.appendChild(_a);
		
		//MOSTRAR GEOMETRIA
		
	}
	
}
