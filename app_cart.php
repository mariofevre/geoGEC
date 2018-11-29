<?php
/**
* aplicación de visualización y gestion de documentos de trabajo. consulta carga y genera la interfaz de configuración de lo0s mismos.
 * 
 *  
* @package    	geoGEC
* @subpackage 	app_muba. Aplicacion para la gestión de documento
* @author     	GEC - Gestión de Espacios Costeros, Facultad de Arquitectura, Diseño y Urbanismo, Universidad de Buenos Aires.
* @author     	<mario@trecc.com.ar>
* @author    	http://www.municipioscosteros.org
* @author	based on TReCC SA Panel de control. https://github.com/mariofevre/TReCC---Panel-de-Control/
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
if(!isset($_SESSION)) {
	 session_start(); 

	if(!isset($_SESSION["geogec"]["usuario"]['id'])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
	}
}


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
    <link href="./css/geogec_app_capa.css" rel="stylesheet" type="text/css">
    <link href="./css/geogec_app_cart.css" rel="stylesheet" type="text/css">
    
     <style>
    	#portamapa img{
    		max-width:100%;
    	}
    	#portamapa a{
    		font-size:20px;
    		text-align:center;
    		display:block;
    		
    	}
    	
    </style>	
</head>

<body>
	
<script type="text/javascript" src="./js/jquery/jquery-1.12.0.min.js"></script>	
<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
<script type="text/javascript" src="./js/ol4.2/ol-debug.js"></script>

<div id="pageborde">
    <div id="page">
        <div id='cuadrovalores'>
		<a href='./index.php?est=est_02_marcoacademico&cod=<?php echo $COD;?>' class='fila' id='encabezado'>
                <h2>geoGEC</h2>
                <p>Plataforma Geomática del centro de Gestión de Espacios Costeros</p>
            </a>

            <div id='elemento'>
                <img src='./img/app_cart_hd.png' style='float:left;'>
                <h2 id='titulo'>Cartelera de publicación para proyectos de Investigación</h2>
                <div id='descripcion'></div>
            </div>	
        </div>
        <div id='menutablas'>
            <h1 id='titulo'>- nombre de proyecto -</h1>
            <p id='descripcion'>- descripcion de proyecto -</p>
        </div>	
        <div id='menuacciones'>
			<div id='lista'></div>	
		</div>
        <div id='portamapa'>
           
        </div>
		
    </div>
</div>

<script type="text/javascript" src="./sistema/sistema_marco.js"></script> <!-- funciones de consulta general del sistema -->
<script type="text/javascript" src="./comunes_consultas.js"></script> <!-- carga funciones de interaccion con el mapa -->
<script type="text/javascript">
	consultarElementoAcciones('','<?php echo $_GET['cod'];?>','est_02_marcoacademico');
</script>


<script>
	
	    var _IdMarco='<?php echo $ID;?>';
        var _CodMarco='<?php echo $COD;?>';	
        var _Items=Array();
        var _Orden=Array();
        
        function cargaBase(){

                _parametros = {
                        'idMarco': _IdMarco,
                        'codMarco': _CodMarco
                };

                $.ajax({
                        data: _parametros,
                        url:   './app_cart/app_cart_consulta.php',
                        type:  'post',
                        success:  function (response){
                                var _res = $.parseJSON(response);
                                console.log(_res);
                                for(_nm in _res.mg){
                                        alert(_res.mg[_nm]);
                                }
                                if(_res.res=='exito'){	
                                  for(_nd in _res.data.documentosOrden){
										_iddoc=_res.data.documentosOrden[_nd];
										_docdata=_res.data.documentos[_iddoc];
										if(_docdata==undefined){continue;}
										
										_sp=_docdata.archivo.split('.');
										_ext = _sp[(_sp.length - 1)].toLowerCase();
										_cont=document.querySelector('#portamapa');
										if(
												_ext=='jpg'
												||
												_ext=='jpeg'
												||
												_ext=='gif'
												||												
												_ext=='png'
												||
												_ext=='tif'
												||
												_ext=='bmp'
										){
											_img=document.createElement('img');
											_img.setAttribute('src',_docdata.archivo);
											_cont.appendChild(_img);
											
										}else{											
											_descarga=document.createElement('a');
											_descarga.setAttribute('href',_docdata.archivo);
											
											if(_docdata.descripcion!=null){
												_descarga.innerHTML=_docdata.descripcion;
											}else{
												_descarga.innerHTML=_docdata.nombre;
											}
											_cont.appendChild(_descarga);
										}
										
									}
                                }else{
                                        alert('error dsfg');
                                }
                               
                        }
                });
        }

        cargaBase('cargainicial');

</script> 

</body>