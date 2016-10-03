<?php

    require_once ('./models/login.php');
    require_once ('./models/Alertas.php');
    require_once('./models/Pedido.php');
    require_once('./models/Configuracion.php');
    require_once('./models/EntregaDirecta.php');

    require_once ("twig.php");
 
	if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
       if (((rol($idUsuario)) == 'Administrador') or ((rol($idUsuario)) == 'Gestion'))  {  
            $entregaDirecta=new EntregaDirecta();  
            $modeloPedido = new Pedido();
            $modeloAlerta = new Alertas();
            $configuracion = new Configuracion();
    		switch ($_GET['action']) {       

                case 'listado':
                    ini_set('date.timezone','America/Argentina/Buenos_Aires'); 
                    $hora = date("H:i:s");
                    $pedidos= $modeloAlerta-> mostrarPedidosEntregar($hora);
                    $vencidos=$modeloAlerta->mostrarPedidosVencidos($hora);
                    $cantidad = $configuracion ->cantidadDias(); 
                    $cant = $cantidad[0]["valor"]; //obtengo la cantidad de dias que estan en la configuracion
                    $cantDias = $entregaDirecta -> determinarDia($cant); //le suma a la fecha actual la cantidad de dias que fueron configurados
                    $verAlimentosAVencer = $entregaDirecta -> mostrarAlimentosPorVencer($cantDias);
                   

                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ALERTAS",
                         "alimentos" => $verAlimentosAVencer,
                        "vencidos"=>$vencidos,
                        "pedidos"=>$pedidos);
                    if (rol($idUsuario) == 'Administrador'){
                         echo $twig->render('listadoPedidosEntregar.html.twig', $variables);
                     }else{
                        echo $twig->render('listadoPedidosEntregarGestion.html.twig', $variables);
                     }

                   
                break;
                     
            }
        }else{
          	$variables = array(
        	"error" => "Inicie sesion como Admnistrador");
           	echo $twig->render('login.html.twig', $variables);
        }
             
             
    }
 ?>