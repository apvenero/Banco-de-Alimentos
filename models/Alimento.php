<?php
	
	class Alimento{

		private $codigo;
		private $descripcion;


		function verCodigo() {
			return $this->$codigo;
		}

		function setCodigo($codigo) {
			$this->$codigo = $codigo;
		}

		function verDescripcion() {
			return $this->$descripcion;
		}

		function setDescripcion($descripcion) {
			$this->$descripcion = $descripcion;
		}


		function listaAlimentos(){	
			$db = new Database();
			$sql = "SELECT codigo, descripcion
					FROM alimento 
					
				   ";
			$db->query($sql);
			$datos = $db->resultado();
			return $datos;
		}	

		function listaCompletaAlimentos(){
			$db = new Database();
			$sql = "SELECT a.codigo, a.descripcion,da.id, da.fecha_vencimiento, da.contenido, da.peso_unitario,
					da.stock, da.reservado,da.unidad
					FROM alimento as a 
					INNER JOIN detalle_alimento AS da ON a.codigo = da.alimento_codigo and da.stock > 0
				   ";
			$db->query($sql);
			$datos = $db->resultado();
			return $datos;
		}	
		

		function listaDetalle($codigo){	
			$db = new Database();
			$sql = "SELECT id, alimento_codigo, fecha_vencimiento, contenido, peso_unitario, stock, reservado, unidad
					FROM detalle_alimento WHERE alimento_codigo = :codigo
				   ";
			$db->query($sql);
			$db->bind(':codigo', $codigo, PDO::PARAM_STR);
			$datos = $db->resultado();
			return $datos;
		}	

		function agregarAlimento($codigo, $descripcion,$fecha_vencimiento, $contenido, $peso_unitario, $stock, 
								 $reservado, $unidad){

			require_once('Database.php');
			$db = new Database();
			$sql="INSERT INTO alimento (codigo, descripcion)
				  VALUES (:codigo, :descripcion)";
			$db->query($sql);
			$db->bind(':codigo', $codigo, PDO::PARAM_STR);
			$db->bind(':descripcion', $descripcion, PDO::PARAM_STR);
			$db->ejecutar();				
			
			$sql="INSERT INTO detalle_alimento SET 
					alimento_codigo = :codigo, 
					fecha_vencimiento = :fecha_vencimiento, 
					contenido = :contenido, 
					peso_unitario = :peso_unitario, 
					stock = :stock, 
					reservado = :reservado,
					unidad = :unidad ";

				$db->query($sql);
				
				$db->bind(':codigo', $codigo, PDO::PARAM_STR);
				$db->bind(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
				$db->bind(':contenido', $contenido, PDO::PARAM_STR);
				$db->bind(':peso_unitario', $peso_unitario, PDO::PARAM_STR);
				$db->bind(':stock', $stock, PDO::PARAM_STR);
				$db->bind(':reservado', $reservado, PDO::PARAM_STR);
				$db->bind(':unidad', $unidad, PDO::PARAM_STR);
				$db->ejecutar();



				
			}	

			function agregarNuevoDetalle($codigo,$fecha_vencimiento, $contenido,
                        $peso_unitario, $stock, $reservado, $unidad){

					require_once('Database.php');
					$db = new Database();
					$sql="INSERT INTO detalle_alimento SET 
					alimento_codigo = :codigo, 
					fecha_vencimiento = :fecha_vencimiento, 
					contenido = :contenido, 
					peso_unitario = :peso_unitario, 
					stock = :stock, 
					reservado = :reservado,
					unidad = :unidad ";

					$db->query($sql);
					$db->bind(':codigo', $codigo, PDO::PARAM_STR);
					$db->bind(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
					$db->bind(':contenido', $contenido, PDO::PARAM_STR);
					$db->bind(':peso_unitario', $peso_unitario, PDO::PARAM_STR);
					$db->bind(':stock', $stock, PDO::PARAM_STR);
					$db->bind(':reservado', $reservado, PDO::PARAM_STR);
					$db->bind(':unidad', $unidad, PDO::PARAM_STR);
					$db->ejecutar();
					
			}

			function mostrarInformacionAlimento($id){
				require_once('Database.php');
				$db = new Database();
				$sql="SELECT id, alimento_codigo,fecha_vencimiento, contenido, peso_unitario, stock, reservado,unidad
					  FROM  detalle_alimento  
					  WHERE id = :id	";

				
				$db->query($sql);
				$db->bind('id',$id, PDO::PARAM_STR);
				$alimento = $db->resultado();
				return $alimento;	

		}

			function mostrarInformacionDetalle($codigo){
				require_once('Database.php');
				$db = new Database();
				$sql="SELECT id, alimento_codigo, fecha_vencimiento, contenido, peso_unitario,
					stock, reservado,unidad
					FROM detalle_alimento   
					WHERE alimento_codigo = :codigo	";

				
				$db->query($sql);
				$db->bind('codigo',$codigo, PDO::PARAM_STR);
				$alimento = $db->resultado();
				return $alimento;	

		}


		function modificarAlimento($id, $fecha_vencimiento, $contenido, $peso_unitario, $stock, 
								 $reservado, $unidad){

			require_once('Database.php');
			$db = new Database();
					$sql="UPDATE detalle_alimento SET 
					fecha_vencimiento = :fecha_vencimiento, 
					contenido = :contenido, 
					peso_unitario = :peso_unitario, 
					stock = :stock, 
					reservado = :reservado,
					unidad = :unidad WHERE id = :id";
			
				$db->query($sql);
				
				$db->bind(':id', $id, PDO::PARAM_STR);
				$db->bind(':fecha_vencimiento', $fecha_vencimiento, PDO::PARAM_STR);
				$db->bind(':contenido', $contenido, PDO::PARAM_STR);
				$db->bind(':peso_unitario', $peso_unitario, PDO::PARAM_STR);
				$db->bind(':stock', $stock, PDO::PARAM_STR);
				$db->bind(':reservado', $reservado, PDO::PARAM_STR);
				$db->bind(':unidad', $unidad, PDO::PARAM_STR);
				$db->ejecutar();



		}

		function modificarStock($cantidad){
			require_once('Database.php');
			$db = new Database();
			$sql = "UPDATE detalle_alimento
					SET stock = stock + :cantidad ";
			$db->query($sql);
			$db->bind(':cantidad', $cantidad, PDO::PARAM_STR);
			$db->ejecutar();
					
			
		}

		function baja($id, $codigo) {
			require_once('Database.php');
			$db = new Database();
			$sql = "DELETE FROM alimento_donante WHERE detalle_alimento_id = :id" ;
			$db->query($sql);
			$db->bind(':id',$id, PDO::PARAM_STR);
			$db->ejecutar();

			$sql="DELETE FROM detalle_alimento WHERE alimento_codigo = :codigo";
			$db->query($sql);
			$db->bind(':codigo',$codigo, PDO::PARAM_STR);
			$db->ejecutar();

			$sql = "DELETE FROM alimento WHERE codigo= :codigo" ; 
			$db->query($sql);
			$db->bind(':codigo',$codigo, PDO::PARAM_STR);
			$db->ejecutar();

		}

		function bajaDetalle($id){
			require_once('Database.php');
			$db = new Database();
			$sql = "DELETE FROM detalle_alimento WHERE id = :id" ;
			$db->query($sql);
			$db->bind(':id',$id, PDO::PARAM_STR);
			$db->ejecutar();

		}



			



}
	




?>

