<?php
	
	class EntidadReceptora{


	
	function mostrarEntidades() {
		require_once('Database.php');
		$db = new Database();
		$sql = "SELECT id, razon_social, telefono, domicilio
				FROM entidad_receptora";
		$db->query($sql);
		$entidades= $db->resultado();
		return $entidades;
	}

	function mostrarMasEntidad($id) {
		require_once('Database.php');
		global $entidad;
		$db = new Database();
		$sql = "SELECT er.id, er.razon_social, er.telefono, er.domicilio, er.servicio_prestado_id, 
				er.necesidad_entidad_id, er.estado_entidad_id,es.descripcionEstado, ne.descripcionNecesidad,
				sp.descripcionServicio, er.latitud, er.longitud
				FROM entidad_receptora AS er
				INNER JOIN estado_entidad AS es ON ( er.estado_entidad_id = es.id ) 
				INNER JOIN necesidad_entidad AS ne ON ( er.necesidad_entidad_id = ne.id ) 
				INNER JOIN servicio_prestado AS sp ON ( er.servicio_prestado_id = sp.id ) 
				WHERE er.estado_entidad_id = es.id
				AND er.necesidad_entidad_id = ne.id 
				AND er.servicio_prestado_id = sp.id 
				AND er.id = :id";
 		$db->query($sql);
 		$db->bind(':id', $id, PDO::PARAM_STR);
 		$entidad= $db->resultado();
	}
	
	function mostrarServicios() {
		global $servicios;
		$db = new Database();
		$sql = "SELECT id, descripcionServicio
				FROM servicio_prestado";
 		$db->query($sql);
		return $db->resultado();
	}

	function mostrarNecesidades() {
		global $necesidades;
		$db = new Database();
		$sql = "SELECT id, descripcionNecesidad
				FROM necesidad_entidad";
 		$db->query($sql);
		return $db->resultado();
	}

	function mostrarEstados() {
		global $estados;
		$db = new Database();
		$sql = "SELECT id, descripcionEstado
				FROM estado_entidad";
 		$db->query($sql);
		return $db->resultado();
	}

	function modificarEntidad($id,$razon_social,$telefono, $domicilio, $servicio_prestado_id, $necesidad_entidad_id,
								$estado_entidad_id, $latitud, $longitud){
		require_once('Database.php');
		$db = new Database();	

		$sql = "UPDATE entidad_receptora SET razon_social=:razon_social, 
				telefono=:telefono, domicilio=:domicilio, servicio_prestado_id=:servicio_prestado_id, 
				necesidad_entidad_id=:necesidad_entidad_id, estado_entidad_id=:estado_entidad_id, latitud = :latitud, longitud= :longitud
				WHERE id = '". $id ."'" ;

		$db->query($sql);

		$db->bind(':razon_social', $razon_social, PDO::PARAM_STR);
		$db->bind(':telefono', $telefono, PDO::PARAM_STR);
		$db->bind(':domicilio', $domicilio, PDO::PARAM_STR);
		$db->bind(':servicio_prestado_id', $servicio_prestado_id, PDO::PARAM_STR);
		$db->bind(':necesidad_entidad_id', $necesidad_entidad_id, PDO::PARAM_STR);
		$db->bind(':estado_entidad_id', $estado_entidad_id, PDO::PARAM_STR);
		$db->bind('latitud', $latitud, PDO::PARAM_STR);
		$db->bind('longitud', $longitud, PDO::PARAM_STR);
		

		return $db->ejecutar();
	}	


	function eliminarEntidad( $idEntidad) {
		$db = new Database();
		$sql = "DELETE 	FROM entidad_receptora 
				WHERE id= '". $idEntidad ."'";
 		$db->query($sql);
 		$db->bind('idEntidad',$idEntidad, PDO::PARAM_STR);
		$db->ejecutar();

	}

	function altaEntidad($razon_social, $telefono, $domicilio, $servicio_prestado_id,
                         $necesidad_entidad_id, $estado_entidad_id, $latitud, $longitud){
		$db = new Database();	
		$sql = "INSERT INTO entidad_receptora (razon_social, telefono, domicilio, servicio_prestado_id, necesidad_entidad_id,estado_entidad_id, latitud, longitud)
				VALUES (:razon_social, :telefono, :domicilio, :servicio_prestado_id, :necesidad_entidad_id, :estado_entidad_id, :latitud, :longitud)";
		$db->query($sql);
		$db->bind('razon_social', $razon_social, PDO::PARAM_STR);
		$db->bind('telefono', $telefono, PDO::PARAM_STR);
		$db->bind('domicilio', $domicilio, PDO::PARAM_STR);
		$db->bind('servicio_prestado_id', $servicio_prestado_id, PDO::PARAM_STR);
		$db->bind('necesidad_entidad_id', $necesidad_entidad_id, PDO::PARAM_STR);
		$db->bind('estado_entidad_id', $estado_entidad_id, PDO::PARAM_STR);
		$db->bind('latitud', $latitud, PDO::PARAM_STR);
		$db->bind('longitud', $longitud, PDO::PARAM_STR);
		return $db->ejecutar();
	}




}

?>
