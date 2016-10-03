<?php
			require('Database.php');
			
			function login($usuario,$pass) {
				$pass = $pass;            //md5($pass);
				global $idUsuario;
				$db = new Database();
				$sql = "SELECT idUsuario, usuario, pass 
						FROM usuarios 
						WHERE usuario = :usuario AND pass = :pass  ";

				$db->query($sql);					
				$db->bind(':usuario', $usuario, PDO::PARAM_STR);
	        	$db->bind(':pass', $pass, PDO::PARAM_STR, 40);
				$idUsuario = $db->columna(0);
				$usuario = $db->columna(1); 			  	
				if ($db->filas() > 0) {
					return true;
				}						
				else {
					return false;						
				}			

			}

			function rol($idUsuario) {
				global $resultado;				
				require_once('Database.php');
				$db = new Database();
				$sql= "SELECT r.nombre FROM roles AS r 
					   INNER JOIN usuario_rol AS ur ON r.idRol = ur.idRol
					   WHERE idUsuario = :idUsuario";
				$db->query($sql);
				$db->bind(':idUsuario', $idUsuario, PDO::PARAM_STR);
				$resultado = $db->resultado();
				return $resultado[0]['nombre'];
			}

			
?>

