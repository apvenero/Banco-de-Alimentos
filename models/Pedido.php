<?php
	
	class Pedido{


	function mostrarEntidades() {
		$db = new Database();
		$sql = "SELECT id, razon_social, domicilio
				FROM entidad_receptora";
 		$db->query($sql);
		$entidades = $db->resultado();
		return $entidades;
	}

	function mostrarAlimentos() {
		$db = new Database();
		$sql = "SELECT de.id, a.codigo, a.descripcion, de.fecha_vencimiento, de.contenido, de.unidad,
				de.peso_unitario, de.stock
				FROM alimento as a
				INNER JOIN detalle_alimento as de
				WHERE a.codigo = de.alimento_codigo";
 		$db->query($sql);
		$alimentos = $db->resultado();
		return $alimentos;
	}

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


	function mostrarEstados() {
		$db = new Database();
		$sql = "SELECT id, descripcion
				FROM estado_pedido";
 		$db->query($sql);
		$estados = $db->resultado();
		return $estados;
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


	function altaPedido($entidad_receptora_id, $fecha, $hora, $con_envio){
		require_once('Database.php');
		$db = new Database();	

		$fecha_ingreso=  date('Y-m-d');
		$sql = "INSERT INTO turno_entrega (id, fecha, hora)
				VALUES (NULL, :fecha, :hora)";
		$db->query($sql);
		$db->bind('fecha', $fecha, PDO::PARAM_STR);
		$db->bind('hora', $hora, PDO::PARAM_STR);		
		$db->ejecutar();	

		$turno_entrega_id = $db->lastInsertId('id');


		$sql = "INSERT INTO pedido_modelo (entidad_receptora_id, fecha_ingreso, estado_pedido_id, turno_entrega_id, con_envio)
				VALUES (:entidad_receptora_id, :fecha_ingreso, '1', :turno_entrega_id, :con_envio)";
		$db->query($sql);
		$db->bind('entidad_receptora_id', $entidad_receptora_id, PDO::PARAM_STR);
		$db->bind('fecha_ingreso', $fecha_ingreso, PDO::PARAM_STR);
		$db->bind('turno_entrega_id', $turno_entrega_id, PDO::PARAM_STR);
		$db->bind('con_envio', $con_envio, PDO::PARAM_STR);
 
		$db->ejecutar();

		return $db->lastInsertId('numero');
	}

	function altaAlimento ($pedido_numero, $detalle_alimento_id, $cantidad){
		$db = new Database();	

		$sql = "INSERT INTO alimento_pedido (pedido_numero, detalle_alimento_id, cantidad)
				VALUES (:pedido_numero, :detalle_alimento_id, :cantidad )";
		$db->query($sql);
		$db->bind('pedido_numero', $pedido_numero, PDO::PARAM_STR);
		$db->bind('detalle_alimento_id', $detalle_alimento_id, PDO::PARAM_STR);		
		$db->bind('cantidad', $cantidad, PDO::PARAM_STR);
		$db->ejecutar();

		$sql = "UPDATE detalle_alimento
				SET stock = stock - :cantidad 
				WHERE id = :detalle_alimento_id";

		$db->query($sql);
		$db->bind(':cantidad', $cantidad, PDO::PARAM_STR);
		$db->bind('detalle_alimento_id', $detalle_alimento_id, PDO::PARAM_STR);
		$db->ejecutar();

	}

	function mostrarPedidos() {
		$db = new Database();
		$sql = "SELECT p.numero, p.entidad_receptora_id, e.razon_social, p.fecha_ingreso,
				p.estado_pedido_id, es.descripcion, p.turno_entrega_id, t.fecha, t.hora, p.con_envio
				FROM pedido_modelo as p
				INNER JOIN entidad_receptora as e ON (p.entidad_receptora_id=e.id)
				INNER JOIN estado_pedido as es ON (p.estado_pedido_id=es.id)
				INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id)";
 		$db->query($sql);
		$alimentos = $db->resultado();
		return $alimentos;
	}

	function verAlimentosPedido($id) {
		require_once('Database.php');
		global $alimentos;
		$db = new Database();
		$sql = "SELECT a.descripcion, de.contenido, ap.cantidad,ap.detalle_alimento_id, de.unidad, de.alimento_codigo, de.fecha_vencimiento, de.peso_unitario, de.stock
				FROM alimento_pedido AS ap
				INNER JOIN detalle_alimento as de on(ap.detalle_alimento_id=de.id)
				INNER JOIN alimento as a on (a.codigo=de.alimento_codigo)
				WHERE ap.pedido_numero = :id";
 		$db->query($sql);
 		$db->bind(':id', $id, PDO::PARAM_STR);
 		$alimentos= $db->resultado();
 		return $alimentos;
	}
	
	function mostrarInfoPedido($id) {
		$db = new Database();
		$sql = "SELECT p.numero, p.entidad_receptora_id, e.razon_social, e.domicilio, p.fecha_ingreso, 
				p.estado_pedido_id, es.descripcion, p.turno_entrega_id, t.fecha, t.hora, p.con_envio
				FROM pedido_modelo as p
				INNER JOIN entidad_receptora as e ON (p.entidad_receptora_id=e.id)
				INNER JOIN estado_pedido as es ON (p.estado_pedido_id=es.id)
				INNER JOIN turno_entrega as t ON (p.turno_entrega_id=t.id)
				WHERE p.numero= :id";
 		$db->query($sql);
  		$db->bind(':id', $id, PDO::PARAM_STR);

		$pedido= $db->resultado();
		return $pedido;
	}


	function modificarPedido($numero ,$entidad_receptora_id,$fecha, $hora, $con_envio, $turno_entrega_id){
		require_once('Database.php');
		$db = new Database();	

		
		$sql="UPDATE turno_entrega SET fecha= :fecha, hora=:hora
					 WHERE id = :turno_entrega_id";
		$db->query($sql);
		$db->bind(':fecha', $fecha, PDO::PARAM_STR);
		$db->bind(':hora', $hora, PDO::PARAM_STR);
		$db->bind(':turno_entrega_id', $turno_entrega_id, PDO::PARAM_STR);
		$db->ejecutar();


		$sql = "UPDATE pedido_modelo SET entidad_receptora_id=:entidad_receptora_id, 
				con_envio=:con_envio
				WHERE numero= :numero" ;
		
		$db->query($sql);
		$db->bind(':numero', $numero, PDO::PARAM_STR);
		$db->bind(':entidad_receptora_id', $entidad_receptora_id, PDO::PARAM_STR);
		$db->bind(':con_envio', $con_envio, PDO::PARAM_STR);
		
		return $db->ejecutar();
	}

	function verAlimentosNoPedido($id) {
		require_once('Database.php');
		global $alimentos;
		$db = new Database();
		$sql = "SELECT a.descripcion,a.codigo, de.contenido, de.id, de.unidad,de.alimento_codigo, de.fecha_vencimiento, de.peso_unitario, de.stock
				FROM detalle_alimento as de
				INNER JOIN alimento as a on (a.codigo=de.alimento_codigo)
				WHERE de.id=:id";
 		$db->query($sql);
 		$db->bind(':id', $id, PDO::PARAM_STR);
 		$alimentosAgregar= $db->resultado();
 		return $alimentosAgregar;
	}


	function eliminarAlimento ($numero, $detalle_alimento_id) {
		require_once('Database.php');
		$db = new Database();
		
		$sql = "UPDATE detalle_alimento SET stock = stock + (select cantidad from alimento_pedido WHERE pedido_numero=:numero and detalle_alimento_id=:detalle_alimento_id)
				WHERE id = :detalle_alimento_id"; 
		$db->query($sql);
		$db->bind(':numero', $numero, PDO::PARAM_STR);
		$db->bind('detalle_alimento_id',$detalle_alimento_id, PDO::PARAM_STR);

		$db->ejecutar();


		$sql = "DELETE FROM alimento_pedido WHERE pedido_numero = :numero and detalle_alimento_id = :detalle_alimento_id"; 
		$db->query($sql);
		$db->bind(':numero', $numero, PDO::PARAM_STR);
		$db->bind('detalle_alimento_id',$detalle_alimento_id, PDO::PARAM_STR);

		$db->ejecutar();
	
	}

	function bajaPedido ($numero) {
		require_once('Database.php');
		$db = new Database();

		$sql = "DELETE FROM pedido_modelo WHERE numero = '". $numero ."'"; 
		$db->query($sql);
		$db->bind(':numero', $numero, PDO::PARAM_STR);

		$db->ejecutar();
	
	}

	function estaAlimento ($id, $detalle_alimento_id) {
		$db = new Database();			
		$sql = "SELECT * FROM alimento_pedido WHERE pedido_numero=:id and detalle_alimento_id = :detalle_alimento_id ";
		$db->query($sql);
		$db->bind("id", $id, PDO::PARAM_STR);
		$db->bind("detalle_alimento_id", $detalle_alimento_id, PDO::PARAM_STR);

		return ($db->filas() > 0);
	}
	
	function actualizarAlimento ($id, $detalle_alimento_id, $cantidadAlimento) {
		$db = new Database();			
		$sql = "UPDATE alimento_pedido
				SET cantidad= cantidad + :cantidadAlimento
				WHERE detalle_alimento_id=:detalle_alimento_id and pedido_numero=:id";
		$db->query($sql);

		$db->bind("id", $id, PDO::PARAM_STR);
		$db->bind(':cantidadAlimento', $cantidadAlimento, PDO::PARAM_STR);
		$db->bind('detalle_alimento_id', $detalle_alimento_id, PDO::PARAM_STR);
		$db->ejecutar();

		$sql= "UPDATE detalle_alimento
				SET stock = stock - :cantidadAlimento 
				WHERE id = :detalle_alimento_id";

		$db->query($sql);
		$db->bind(':cantidadAlimento', $cantidadAlimento, PDO::PARAM_STR);
		$db->bind('detalle_alimento_id', $detalle_alimento_id, PDO::PARAM_STR);
		$db->ejecutar();



	}
}

?>