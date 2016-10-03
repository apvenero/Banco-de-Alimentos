<?php
	require_once('Database.php');
	
	class Envio{

		function mostrarPedidos(){
			$db = new Database();

			$sql = "SELECT p.numero, p.entidad_receptora_id, e.razon_social,p.turno_entrega_id, t.fecha, t.hora
				FROM pedido_modelo as p
				INNER JOIN entidad_receptora as e ON (p.entidad_receptora_id=e.id)
				INNER JOIN estado_pedido as es ON (p.estado_pedido_id=es.id)
				INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id)
				WHERE p.con_envio = 'SI' and es.descripcion='Pendiente'";
	 		$db->query($sql);
			$pedidos = $db->resultado();
			return $pedidos;
		}

		function verAlimentosEnvio($id){
			$db = new Database();
			$sql = "SELECT a.descripcion, de.contenido, ap.cantidad, de.unidad
					FROM alimento_pedido AS ap
					INNER JOIN detalle_alimento as de on(ap.detalle_alimento_id=de.id)
					INNER JOIN alimento as a on (a.codigo=de.alimento_codigo)
					WHERE ap.pedido_numero = :id";
	 		$db->query($sql);
	 		$db->bind(':id', $id, PDO::PARAM_STR);
	 		$envios= $db->resultado();
	 		return $envios;
		}

		function buscarTurnos($fecha) {
			$db = new Database();
			$sql = "SELECT t.id, t.fecha, t.hora, p.numero,p.turno_entrega_id, p.estado_pedido_id, ep.descripcion
					FROM turno_entrega as t
					INNER JOIN pedido_modelo as p ON (t.id=p.turno_entrega_id)
					INNER JOIN estado_pedido as ep ON (p.estado_pedido_id= ep.id)
					WHERE  t.fecha='$fecha'";
	 		$db->query($sql);
			$envios = $db->resultado();
			return $envios;
		}



		function mostrarInfoEntidad($id){
			$db = new Database();
			$sql="SELECT razon_social,latitud,longitud
				  FROM entidad_receptora
				  WHERE id = :id";
			$db->query($sql);
			$db->bind(':id', $id, PDO::PARAM_STR);
			$entidad= $db->resultado();
			return $entidad;
		}
		
	

		function latitudEntidad($id){
			$db = new Database();
			$sql = "SELECT latitud
					FROM entidad_receptora
					WHERE id = :id";
	 		$db->query($sql);
	 		$db->bind(':id', $id, PDO::PARAM_STR);
	 		$latitud= $db->resultado();
	 		return $latitud[0]['latitud'];
		}

		function longitudEntidad($id){
			$db = new Database();
			$sql = "SELECT longitud
					FROM entidad_receptora
					WHERE id = :id";
	 		$db->query($sql);
	 		$db->bind(':id', $id, PDO::PARAM_STR);
	 		$longitud= $db->resultado();
	 		return $longitud[0]['longitud'];
		}

}

?>