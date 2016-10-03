<?php

	class Alertas{

	function mostrarPedidosEntregar($hora) {
		require_once('Database.php');
		$db = new Database();
		$diaHoy= date("y-m-d");

	
		$sql = "SELECT p.numero, p.entidad_receptora_id, p.fecha_ingreso,p.turno_entrega_id, t.id,t.fecha, t.hora, e.razon_social,
				es.descripcion
				FROM pedido_modelo as p 
				INNER JOIN entidad_receptora as e ON (p.entidad_receptora_id=e.id)
				INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id)
				INNER JOIN estado_pedido as es ON (p.estado_pedido_id=es.id)
				WHERE t.fecha= '$diaHoy' and t.hora > :hora ";
		$db->query($sql);
		$db->bind(':hora', $hora, PDO::PARAM_STR);
		$pedidos= $db->resultado();
		return $pedidos;
	}


	function mostrarPedidosVencidos($hora) {
		require_once('Database.php');
		$db = new Database();
		$diaHoy= date("y-m-d");
		$sql = "SELECT p.numero, p.entidad_receptora_id, p.fecha_ingreso,p.turno_entrega_id, t.id,t.fecha, t.hora, e.razon_social,
				es.descripcion
				FROM pedido_modelo as p 
				INNER JOIN entidad_receptora as e ON (p.entidad_receptora_id=e.id)
				INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id)
				INNER JOIN estado_pedido as es ON (p.estado_pedido_id=es.id)
				WHERE t.fecha= '$diaHoy' and t.hora < :hora ";
		$db->query($sql);
		$db->bind(':hora', $hora, PDO::PARAM_STR);
		$vencidos= $db->resultado();
		return $vencidos;
	}


}

?>

