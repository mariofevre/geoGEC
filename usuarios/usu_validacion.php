<?php 

function validarUsuario(){
	global $ConecSIG;
	global $_POST;
	
	
	$USU['acc']['general']['general']['general']=0;

	
	if(isset($_POST['codMarco'])){		
		if($_POST['codMarco']!=''){
			
			$query="
			SELECT 
				zz_accesolibre
			FROM 
				geogec.est_02_marcoacademico
			WHERE
				codigo = '".$_POST['codMarco']."'
			";
			$ConsultaUsu = pg_query($ConecSIG, $query);
			$fila=pg_fetch_assoc($ConsultaUsu);
			
			if(pg_errormessage($ConecSIG)!=''){
				$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
				$Log['tx'][]='query: '.$query;
				$Log['res']='err';
				terminar($Log);	
			}
			if($fila['zz_accesolibre']=='1'){
				$USU['acc']['est_02_marcoacademico'][$_POST['codMarco']]['general']='2';
			}
			
		}		
	}
	
	if(!isset($_SESSION["geogec"])){
		return $USU;
	}
	
	if(!isset($_SESSION["geogec"]["usuario"])){
		$_SESSION["geogec"]["usuario"]['id']='-1';
		return $USU;
	}
		
	if($_SESSION["geogec"]["usuario"]['id']<1){
		return $USU;
	}
	
	
	$query="
		SELECT id, id_p_sis_usu_registro,tabla, elemento, accion, nivel
		FROM geogec.sis_usu_accesos
		WHERE  
			id_p_sis_usu_registro='".$_SESSION["geogec"]["usuario"]['id']."'
			AND
			zz_borrada=0
		
		";
		
	$ConsultaUsu = pg_query($ConecSIG, $query);

	
	if(pg_errormessage($ConecSIG)!=''){
		$Log['tx'][]=utf8_encode('error: '.pg_errormessage($ConecSIG));
		$Log['tx'][]='query: '.$query;
		$Log['res']='err';
		terminar($Log);	
	}	

	if(pg_num_rows($ConsultaUsu)==0){
		$USU['acc']['general']['general']['general']=0;
	}
	

	while($fila=pg_fetch_assoc($ConsultaUsu)){
		$USU['acc'][$fila['tabla']][$fila['elemento']][$fila['accion']]=$fila['nivel'];
	}
	
	
	
	

	//niveles de acceso
	// 3:  equipo de dearrollo GEC
	// 2:  colaboradores GEC
	// 1:  colaboradores externos
	// 0:  sin privilegios
		
	return $USU;
	 	
}

