<?php
	
require_once('Database.php');

	class Donante {


	
	function mostrarDonantes() {
		require_once('Database.php');
		$db = new Database();
		$sql = "SELECT id, razon_social, apellido_contacto,nombre_contacto, telefono_contacto, mail_contacto, domicilio_contacto
				FROM donante";
		$db->query($sql);
		$donantes= $db->resultado();
		return $donantes;
	}


	function mostrarInfoDonante($idDonante) {
		require_once('Database.php');
		global $donante;
		$db = new Database();
		$sql = "SELECT id, razon_social, apellido_contacto,nombre_contacto, telefono_contacto, mail_contacto, domicilio_contacto
				FROM donante 
				WHERE id = :idDonante ";
 		$db->query($sql);
 		$db->bind(':idDonante', $idDonante, PDO::PARAM_STR);
 		$donante= $db->resultado();
 		return $db->resultado();
	}
	
	function modificarDonante($idDonante , $razon_social, $apellido_contacto, $nombre_contacto, $telefono_contacto,
							 $mail_contacto, $domicilio_contacto){
		$db = new Database();	

		$sql = "UPDATE donante SET razon_social=:razon_social, 
				apellido_contacto=:apellido_contacto, nombre_contacto=:nombre_contacto,	telefono_contacto=:telefono_contacto,
				 mail_contacto=:mail_contacto, domicilio_contacto=:domicilio_contacto
				WHERE id = '". $idDonante ."'" ;

		$db->query($sql);
		
		$db->bind(':razon_social', $razon_social, PDO::PARAM_STR);
		$db->bind(':apellido_contacto', $apellido_contacto, PDO::PARAM_STR);
		$db->bind(':nombre_contacto', $nombre_contacto, PDO::PARAM_STR);
		$db->bind(':telefono_contacto', $telefono_contacto, PDO::PARAM_STR);
		$db->bind(':mail_contacto', $mail_contacto, PDO::PARAM_STR);
		$db->bind(':domicilio_contacto', $domicilio_contacto, PDO::PARAM_STR);


		return $db->ejecutar();
	}

	function altaDonante($razon_social, $apellido_contacto, $nombre_contacto, $domicilio_contacto,
						$telefono_contacto, $mail_contacto){
		$db = new Database();	
		$sql = "INSERT INTO donante (razon_social, apellido_contacto, nombre_contacto, domicilio_contacto, telefono_contacto,mail_contacto)
				VALUES (:razon_social, :apellido_contacto, :nombre_contacto, :domicilio_contacto, :telefono_contacto, :mail_contacto)";
		$db->query($sql);
		$db->bind('razon_social', $razon_social, PDO::PARAM_STR);
		$db->bind('apellido_contacto', $apellido_contacto, PDO::PARAM_STR);
		$db->bind('nombre_contacto', $nombre_contacto, PDO::PARAM_STR);
		$db->bind('domicilio_contacto', $domicilio_contacto, PDO::PARAM_STR);
		$db->bind('telefono_contacto', $telefono_contacto, PDO::PARAM_STR);
		$db->bind('mail_contacto', $mail_contacto, PDO::PARAM_STR);

		return $db->ejecutar();
	}

	function bajaDonante ($idDonante) {
		require_once('Database.php');
		$db = new Database();
		$sql = "DELETE FROM donante WHERE id = '". $idDonante ."'"; 
		$db->query($sql);
		$db->bind('idDonante',$idDonante, PDO::PARAM_STR);
		$db->ejecutar();

	}

	function agregarDonacion($id,$donante_id,$cantidad){
			require_once('Database.php');
			$db = new Database();
			$sql="INSERT INTO alimento_donante (detalle_alimento_id, donante_id,cantidad)
				  VALUES (:id, :donante_id, :cantidad)";
			$db->query($sql);
			$db->bind(':id', $id, PDO::PARAM_STR);
			$db->bind(':donante_id', $donante_id, PDO::PARAM_STR);
			$db->bind(':cantidad', $cantidad, PDO::PARAM_STR);
			$db->ejecutar();
		}

		
	function listaAlimentos($id){	
			$db = new Database();
			$sql = "SELECT d.razon_social, ad.cantidad, da.id
					FROM donante as d 
					INNER JOIN alimento_donante as ad on d.id = ad.donante_id
					INNER JOIN detalle_alimento as da ON ad.detalle_alimento_id = da.id
					where da.id= :id
				   ";
			$db->query($sql);
			$db->bind(':id', $id, PDO::PARAM_STR);
			$datos = $db->resultado();
			return $datos;

			

		}	


}

?>