<?php
	require_once('Database.php');
	class Configuracion{

		private $id;
		private $clave;
		private $valor;


		function verClave() {
			return $this->$clave;
		}

		function setClave($clave) {
			$this->$clave = $clave;
		}

		function verValor() {
			return $this->$valor;
		}

		function setValor($valor) {
			$this->$valor = $valor;
		}


		function verConfig(){
			$db = new Database();
			$sql = "SELECT clave, valor
					FROM configuracion";
			$db->query($sql);
			$datos = $db->resultado();
			return $datos;
		}


		function cantidadDias(){
			$db = new Database();
			$sql = "SELECT clave, valor
					FROM configuracion WHERE clave LIKE 'cantidad'";
			$db->query($sql);
			$datos = $db-> resultado();
			return $datos;
		}

		function obtenerClaveValor($opcion){
			$db = new Database();
			$sql = "SELECT clave, valor
					FROM configuracion WHERE clave LIKE :opcion";
			$db->query($sql);
			$db->bind(':opcion', $opcion, PDO::PARAM_STR);
			$datos = $db-> resultado();
			return $datos[0]["valor"];
		}

		function obtenerID($opcion){
			$db = new Database();
			$sql = "SELECT id
					FROM configuracion WHERE clave LIKE :opcion";
			$db->query($sql);
			$db->bind(':opcion', $opcion, PDO::PARAM_STR);
			$datos = $db-> resultado();
			return $datos[0]["id"];
		}
		
		function verConfiguracion($id){
			$db = new Database();
			$sql = "SELECT clave, valor
					FROM configuracion
					WHERE id = :id";
			$db->query($sql);
			$db->bind(':id', $id, PDO::PARAM_STR);
			$datos = $db->resultado();
			return $datos;
		}

		function guardarModificado($id,$clave,$valor){
			$db = new Database();
			$sql = "UPDATE configuracion
					SET clave = :clave, valor= :valor
					WHERE id = :id";
			$db->query($sql);
			$db->bind(':id', $id, PDO::PARAM_STR);
			$db->bind('clave', $clave, PDO::PARAM_STR);
			$db->bind('valor', $valor, PDO::PARAM_STR);
			$db->ejecutar();
		}
	}
?>