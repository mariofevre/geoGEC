<?php 
/**
* aplicaci�n de visualizaci�n y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuraci�n de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_docs. Aplicacion para la gesti�n de documento
* @author     	GEC - Gesti�n de Espacios Costeros, Facultad de Arquitectura, Dise�o y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author		based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
* 
*
*/
?>
<script>
	$('head').append('<link rel="stylesheet" type="text/css" href="./css/autorias.css?v=2">');
</script>	

<div id='pieagpl' estado='cerrado' onclick='abrecierra(this);'>
	<h2>Acerca de</h2>
	<h2>Licencia</h2>
	<h3>El software de esta p�gina se encuentra publicado bajo la licencia 		
		<a target="_blank"  href="https://www.gnu.org/licenses/agpl-3.0.en.html" class="tec" title="GNU AFFERO GENERAL PUBLIC LICENSE">AGPL 3.0</a>
		</br>
		Esto quiere decir que podes descargarlo y usarlo como quieras, <br>
		siempre que publiques de igual forma tus productos.
	</h3>
	
	<h2>C�digo fuente</h2>
	<h3>El c�digo fuente de este proyecto puede ser consultado y descargado en <br>	
		<a target="_blank"  href="https://github.com/mariofevre/geoGEC" class="tec" title="plataforma geom�tica de la Universidad Nacional de Moreno ">github</a>
	</h3>
	
	<h2>Financiamiento</h2>
	<h3>Financiado por la Universidad de Buenos Aitres mediante los siguiente proyectos: <br>
		<a class="tec"  target="_blank" >Vinculaci�n Tecnol�gica 2017</a> <br>
		<a class="tec"  target="_blank" >PDE 07/2018: </a> <br>
		<a class="tec"  target="_blank" >UBACyT 20020170100337BA-2018;</a> <br> 
		<a class="tec"  target="_blank" >PDE ??/2019.</a> <br>
	</h3>	
	
	
	<h2>Derivado de</h2>
	<h3>Este proyecto fue desarrollado a partir de las siguientes tecnolog�as y proyectos</h3>
	
	<a target="_blank"  href="https://github.com/mariofevre/UNMgeo" class="tec" title="plataforma geom�tica de la Universidad Nacional de Moreno ">UNM(tm) UNMgeo</a>
	<a target="_blank"  href="https://github.com/mariofevre/UNmapa/" class="tec" title="Herramienta ped�gogica para la construccion colaborativa del territorio.">UNM(tm) UNmapa</a>
	<a target="_blank"  href="https://github.com/mariofevre/TReCC---Panel-de-Control" class="tec" title="plataforma de seguimiento de sistemas de indicadores">TReCC(tm) Panel de Control</a>
	<a target="_blank"  href="https://github.com/mariofevre/MAPAUBA/" class="tec" title="Proyecto Plataforma Colectiva de Informaci�n Territorial: UBATIC2014">UBA(tm) MAPAUBA</a>
	<a target="_blank"  href="http://www.baseobra.com.ar/" class="tec" title="plataforma de construcci�n colectiva de conocimiento t�cnico">baseobra</a>
	<a target="_blank"  href="http://www.trecc.com.ar/recursos/proyectoppu.htm" class="tec" title="plataforma para procesos urbano-territoriales participativos">TReCC(tm) PPU</a>
	
</div>

<script>

	function abrecierra(_this){
		if(_this.getAttribute('estado')=='cerrado'){
			_this.setAttribute('estado','abierto');
			_this.querySelector('h2').innerHTML='ocultar origen';
			
		}else if(_this.getAttribute('estado')=='abierto'){
			_this.setAttribute('estado','cerrado');
			_this.querySelector('h2').innerHTML='ver origen';
		}
	}
	
	
</script>		
