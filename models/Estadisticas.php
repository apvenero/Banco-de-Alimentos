<?php
	require_once('Database.php');

	class Estadisticas{


		function pedidosEntreFechas ($fechaInicial, $fechaFinal){

			$db = new Database();
			$sql="SELECT p.numero, p.estado_pedido_id, t.fecha
				  FROM  pedido_modelo as p
				  INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id)  
				  WHERE (t.fecha between :fechaInicial and :fechaFinal) AND (p.estado_pedido_id= '2')";
			$db->query($sql);
			$db->bind('fechaInicial',$fechaInicial, PDO::PARAM_STR);
			$db->bind('fechaFinal',$fechaFinal, PDO::PARAM_STR);

			$pedidos = $db->resultado();
			return $pedidos;	
		}

		function cantKilosPedido ($idPedido){

			$db = new Database();
			$sql="SELECT SUM(ap.cantidad*d.peso_unitario) as totalKilos, ap.pedido_numero, ap.detalle_alimento_id
				  FROM alimento_pedido as ap
				  INNER JOIN detalle_alimento as d ON (ap.detalle_alimento_id=d.id)
				  WHERE ap.pedido_numero= :idPedido";
			$db->query($sql);
			$db->bind('idPedido',$idPedido, PDO::PARAM_STR);

			$cantidad = $db->resultado();
			return $cantidad;

		}


		function pedidosEntidad ($fechaInicial, $fechaFinal, $entidad_receptora_id){

			$db = new Database();
			$sql="SELECT al.descripcion as descripcion, da.contenido as contenido,
				  	 SUM(ap.cantidad*da.peso_unitario) as kgs
				  FROM  pedido_modelo as p
				  INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id) 
				  INNER JOIN alimento_pedido as ap ON (ap.pedido_numero = p.numero)  
				  INNER JOIN detalle_alimento as da ON (ap.detalle_alimento_id = da.id)  
				  INNER JOIN alimento as al ON (al.codigo = da.alimento_codigo)  
				  WHERE (t.fecha between :fechaInicial and :fechaFinal) 
				  AND (p.entidad_receptora_id = :entidad_receptora_id)
				  GROUP BY ap.detalle_alimento_id";
			$db->query($sql);
			$db->bind(':fechaInicial',$fechaInicial, PDO::PARAM_STR);
			$db->bind(':fechaFinal',$fechaFinal, PDO::PARAM_STR);
			$db->bind(':entidad_receptora_id',$entidad_receptora_id, PDO::PARAM_STR);

			$pedidos = $db->resultado();
			return $pedidos;	
		}

		function cantKilosPorAlimento ($idPedido, $total){

			$db = new Database();
			$sql="SELECT a.descripcion, (:total *SUM(ap.cantidad*d.peso_unitario)/100) as porcentaje
				  FROM alimento_pedido as ap
				  INNER JOIN detalle_alimento as d ON (ap.detalle_alimento_id=d.id)
  				  INNER JOIN alimento as a ON (d.alimento_codigo=a.codigo)
				  WHERE ap.pedido_numero= :idPedido
				  GROUP BY ap.detalle_alimento_id";
			$db->query($sql);
			$db->bind(':idPedido',$idPedido, PDO::PARAM_STR);
			$db->bind(':total',$total, PDO::PARAM_STR);

			$datos = $db->resultado();
			return $datos;

		}


		function mostrarAlimentosVencidos(){
			$db = new Database();
			$sql = "SELECT a.codigo, a.descripcion, da.fecha_vencimiento,da.stock
					FROM alimento as a 
					INNER JOIN detalle_alimento AS da ON a.codigo = da.alimento_codigo 
					WHERE da.fecha_vencimiento < now() and da.stock > 0   ";
			$db->query($sql);
			$alimentos = $db->resultado();
			return $alimentos;
		}	


}
?>