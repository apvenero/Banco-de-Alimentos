<?php
	

	class Turnos{


		function mostrarTurnos() {
			$db = new Database();
			$sql = "SELECT t.id, t.fecha, t.hora, p.numero,p.turno_entrega_id, p.estado_pedido_id, ep.descripcion
					FROM turno_entrega as t
					INNER JOIN pedido_modelo as p ON (t.id=p.turno_entrega_id)
					INNER JOIN estado_pedido as ep ON (p.estado_pedido_id= ep.id)
					WHERE t.id=p.turno_entrega_id";
	 		$db->query($sql);
			$turnos = $db->resultado();
			return $turnos;
		}

		function buscarTurnos($fecha) {
			$db = new Database();
			$sql = "SELECT t.id, t.fecha, t.hora, p.numero,p.turno_entrega_id, p.estado_pedido_id, ep.descripcion
					FROM turno_entrega as t
					INNER JOIN pedido_modelo as p ON (t.id=p.turno_entrega_id)
					INNER JOIN estado_pedido as ep ON (p.estado_pedido_id= ep.id)
					WHERE t.id=p.turno_entrega_id and t.fecha='$fecha'";
	 		$db->query($sql);
			$turnos = $db->resultado();
			return $turnos;
		}

		function mostrarInfoTurno ($idTurno) {

			$db = new Database();
			$sql = "SELECT t.id, t.fecha, t.hora, p.numero, ep.descripcion, p.estado_pedido_id
					FROM turno_entrega as t
					INNER JOIN pedido_modelo as p ON (t.id=p.turno_entrega_id)
					INNER JOIN estado_pedido as ep ON (p.estado_pedido_id= ep.id)
					WHERE t.id= :idTurno";
	 		$db->query($sql);
 	    	$db->bind(':idTurno', $idTurno, PDO::PARAM_STR);

			return $db->resultado();
		}

		function modificarTurno ($id , $fecha, $hora, $estado_pedido_id){
			
			$db = new Database();	

			$sql = "UPDATE turno_entrega SET fecha=:fecha,hora=:hora
					WHERE id = '". $id ."'" ;
			$db->query($sql);

			$db->bind(':fecha', $fecha, PDO::PARAM_STR);
			$db->bind(':hora', $hora, PDO::PARAM_STR);	
			$db->ejecutar();
			
			$sql = "UPDATE pedido_modelo SET estado_pedido_id=:estado_pedido_id
					WHERE turno_entrega_id = '". $id ."'" ;
			
			$db->query($sql);
			$db->bind(':estado_pedido_id', $estado_pedido_id, PDO::PARAM_STR);
			$db->ejecutar();

			return $db->ejecutar();

	}

}

?>