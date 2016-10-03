<?php
	
	class Usuario{

		private $idUsuario;
		private $usuario;
		private $pass;


		function verUsuario() {
			return $this->$usuario;
		}

		function setUsuario($usuario) {
			$this->$usuario = $usuario;
		}

		function verPass() {
			return $this->$pass;
		}

		function setPass($pass) {
			$this->$pass = $pass;
		}

		


		function rol($idUsuario) {
			require_once('Database.php');
			global $resultado;
			$db = new Database();
			$sql= "SELECT r.nombre FROM roles AS r 
				   INNER JOIN usuario_rol AS ur ON r.idRol = ur.idRol
				   WHERE idUsuario = :idUsuario";
			$db->query($sql);
			$db->bind(':idUsuario', $idUsuario, PDO::PARAM_STR);
			$resultado = $db->resultado();
			return $resultado[0]['nombre'];
		}
	
	function rolUsuario($usuario) {
			require_once('Database.php');
			global $resultado;
			$db = new Database();
			$sql= "SELECT r.nombre FROM roles AS r 
				   INNER JOIN usuario_rol AS ur ON r.idRol = ur.idRol
				   INNER JOIN usuarios AS u ON ur.idUsuario = u.idUsuario
				   WHERE usuario = :usuario";
			$db->query($sql);
			$db->bind(':usuario', $usuario, PDO::PARAM_STR);
			$resultado = $db->resultado();
			return $resultado[0]['nombre'];
		}

	function mostrarUsuarios(){
		require_once('Database.php');
		$db = new Database();
		$sql = "SELECT u.idUsuario,usuario, ur.idRol, r.nombre
		FROM usuarios AS u
		INNER JOIN usuario_rol AS ur ON (u.idUsuario = ur.idUsuario) 
		INNER JOIN roles AS r ON (r.idRol = ur.idRol)
		"; 
		$db->query($sql);
		$usus = $db->resultado();	
		return $usus;
			
	}

	function existeUsuario($nombreUsuario) {
		require_once('Database.php');
		$db = new Database();			
		$sql = "SELECT * FROM usuarios WHERE usuario = :nombreUsuario ";
		$db->query($sql);
		$db->bind("nombreUsuario", $nombreUsuario, PDO::PARAM_STR);
		return ($db->filas() > 0);
		}

	function altaUsuario($nombreUsuario, $pass, $rol) {	
		require_once('Database.php');
		$db = new Database();
		$sql="INSERT INTO usuarios (usuario, pass)
			  VALUES (:nombreUsuario, :pass)";
			
		$db->query($sql);
		$db->bind(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
		$db->bind(':pass', $pass, PDO::PARAM_STR);
		$db->ejecutar();				

		$id = $db->lastInsertId('usuarios');

		$sql="INSERT INTO usuario_rol (idUsuario,idRol)
			  VALUES (:id, :rol)";
		$db->query($sql);
		$db->bind('id', $id, PDO::PARAM_STR);
		$db->bind('rol', $rol, PDO::PARAM_STR);
		$db->ejecutar();

		return $id;

	}	
	
	function mostrarInformacionUsuario($idUsuario){
		require_once('Database.php');
		$db = new Database();
		$sql = "SELECT idUsuario, usuario, pass
				FROM  usuarios 
				WHERE idUsuario = :idUsuario";
		$db->query($sql);
		$db->bind('idUsuario',$idUsuario, PDO::PARAM_STR);
		$usuario = $db->resultado();
		return $usuario;	

		}

	

	function modificarUsuario($idUsuario, $usuario, $pass, $rol){
		require_once('Database.php');
		$db = new Database();
		$sql="UPDATE usuarios SET usuario = :usuario, pass= :pass WHERE idUsuario = :idUsuario" ;
		$db->query($sql);
		$db->bind('usuario',$usuario, PDO::PARAM_STR);
		$db->bind('pass', $pass, PDO::PARAM_STR);
		$db->bind('idUsuario',$idUsuario, PDO::PARAM_STR);
		$db->ejecutar();
		

		$sql="UPDATE usuario_rol SET idRol= :rol WHERE idUsuario = :idUsuario";
		$db->query($sql);
		$db->bind('rol',$rol, PDO::PARAM_STR);
		$db->bind('idUsuario',$idUsuario, PDO::PARAM_STR);

		return $db->ejecutar();
		}

	function baja ($idUsuario){
		require_once('Database.php');
		$db = new Database();
		$sql = "DELETE FROM usuario_rol WHERE idUsuario = :idUsuario" ;
		$db->query($sql);
		$db->bind(':idUsuario',$idUsuario, PDO::PARAM_STR);
		$db->ejecutar();

		$sql="DELETE FROM usuarios WHERE idUsuario = :idUsuario";
		$db->query($sql);
		$db->bind(':idUsuario',$idUsuario, PDO::PARAM_STR);
		$db->ejecutar();

			
	}
	}
?>