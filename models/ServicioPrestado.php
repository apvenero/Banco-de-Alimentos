<?php
	
	class ServicioPrestado {

		private $id;
		private $descripcionServicio;


		function verId() {
			return $this->$id;
		}

		function verDescripcion() {
			return $this->$descripcionServicio;
		}

		function setDescripcion($descripcionServicio) {
			$this->$descripcionServicio = $descripcionServicio;
		}

	function mostrarServicios() {
		$db = new Database();
		$sql = "SELECT id, descripcionServicio
				FROM servicio_prestado";
 		$db->query($sql);
		$servicios = $db->resultado();
		return $servicios;
	}

	function mostrarInfoServicio($idServicio) {
		global $servicio;
		$db = new Database();
		$sql = "SELECT id, descripcionServicio 
				FROM servicio_prestado
				WHERE id = $idServicio ";
 		$db->query($sql);
		return $db->resultado();
	}
	
	function modificarServicio($id, $descripcionServicio){
		$db = new Database();	

		$sql = "UPDATE servicio_prestado SET descripcionServicio=:descripcionServicio
				WHERE id = '". $id ."'" ;
		$db->query($sql);
		$db->bind(':descripcionServicio', $descripcionServicio, PDO::PARAM_STR);

		return $db->ejecutar();
	}

	function altaServicio($descripcionServicio){
		$db = new Database();	
		$sql = "INSERT INTO servicio_prestado (descripcionServicio)
				VALUES (:descripcionServicio)";
		$db->query($sql);
		$db->bind('descripcionServicio', $descripcionServicio, PDO::PARAM_STR);

		return $db->ejecutar();
	}

	function bajaServicio($idServicio) {
		require_once('Database.php');
		$db = new Database();
		$sql = "DELETE FROM servicio_prestado WHERE id = '". $idServicio ."'"; 
		$db->query($sql);
		$db->bind('idServicio',$idServicio, PDO::PARAM_STR);
		$db->ejecutar();

	}




}

?>