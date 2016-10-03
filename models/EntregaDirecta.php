<?php
	
	class EntregaDirecta{

		function mostrarAlimentosPorVencer($cantDias) {
		require_once('Database.php');
		$db = new Database();
		$sql = "SELECT de.id, a.codigo, a.descripcion, de.fecha_vencimiento, de.contenido, 
				de.peso_unitario, de.stock
				FROM alimento as a
				INNER JOIN detalle_alimento as de on a.codigo = de.alimento_codigo
				WHERE de.fecha_vencimiento between now() and :cantDias and de.stock > 0
				";
				
 		$db->query($sql);
 		$db->bind('cantDias', $cantDias, PDO::PARAM_STR);
 	
		$alimentos = $db->resultado();
		return $alimentos;
	}


		function determinarDia($cantidad){
		$hoy = date('Y-m-d');
		$nuevafecha = strtotime ('+'.$cantidad.'day', strtotime ($hoy) ) ;
		 return $nuevafecha = date ('Y-m-j',$nuevafecha);
	
	}


	function guardarEntregaD($entidad_id){

		$db = new Database();	
		$sql = "INSERT INTO entrega_directa (entidad_receptora_id, fecha)
				VALUES (:entidad_id, now())";
		$db->query($sql);
		$db->bind('entidad_id', $entidad_id, PDO::PARAM_STR);
		$db->ejecutar();

		return $db->lastInsertId('entrega_directa');

	}

	function agregarAlimentoEntregaDirecta($idEntrega, $id_det, $cantidad){
		$db = new Database();
		$sql = "INSERT INTO alimento_entrega_directa (entrega_directa_id, detalle_alimento_id, cantidad)
				VALUES (:idEntrega, :id_det, :cantidad)";
		$db->query($sql);
		$db->bind('idEntrega', $idEntrega, PDO::PARAM_STR);
		$db->bind('id_det', $id_det, PDO::PARAM_STR);
		$db->bind('cantidad', $cantidad, PDO::PARAM_STR);
		$db->ejecutar();

		$sql = "UPDATE detalle_alimento
				SET stock = stock - :cantidad 
				WHERE id = :id_det";
		$db->query($sql);
		$db->bind(':cantidad', $cantidad, PDO::PARAM_STR);
		$db->bind('id_det', $id_det, PDO::PARAM_STR);
		$db->ejecutar();


	}

	function cantidadProductosAVencer($cantDias){
		$db = new Database();	
		$sql = "SELECT SUM(stock) as cantidadDeProductosAVencer
				FROM detalle_alimento 
				WHERE fecha_vencimiento between now() and :cantDias";
		$db->query($sql);
		$db->bind('cantDias', $cantDias, PDO::PARAM_STR);
		return $db->resultado();
	}

	function obtenerStock($idDA){
		$db = new Database();	
		$sql = "SELECT stock
				FROM detalle_alimento 
				WHERE id = :idDA ";
		$db->query($sql);
		$db->bind('idDA', $idDA, PDO::PARAM_STR);
		$pedido=$db->resultado();
		return $pedido[0];
	}
                      
}
?>
