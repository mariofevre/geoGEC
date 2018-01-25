<?php 

function validarUsuario(){
	
	$USU['datos']['id']=1;
	
	//niveles de acceso
	// 3:  equipo de dearrollo GEC
	// 2:  colaboradores GEC
	// 1:  colaboradores externos
	
	$USU['acc']['est']['gral']=3;
	$USU['acc']['ref']["AGREGANDOVALOR2017FEVRE"]=3;	
	
	return $USU;
}

?>
