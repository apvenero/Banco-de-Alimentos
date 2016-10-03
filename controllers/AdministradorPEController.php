<?php

    require_once ('./models/login.php');
    require_once ('./models/Pedido.php');
    require_once('./models/Configuracion.php');
    require_once('./models/EntregaDirecta.php');
    require_once ("twig.php");
 
  if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
        if (((rol($idUsuario)) == 'Administrador') or (rol($idUsuario)) == 'Gestion') {   
           
            $modeloPedido = new Pedido();
            $configuracion = new Configuracion();
            $entregaDirecta= new EntregaDirecta();

        switch ($_GET['action']) {       

               case 'cargar':
                    $entidades= $modeloPedido-> mostrarEntidades();
                    $alimentos= $modeloPedido ->mostrarAlimentos();
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "CARGAR PEDIDO",
                        "entidades"=>$entidades,
                        "alimentos"=>$alimentos
                        );
                     if ((rol($idUsuario)) == 'Administrador'){
                    echo $twig->render('cargarPedidos.html.twig', $variables);
                }else{
                    echo $twig->render('cargarPedidosGestion.html.twig', $variables);
                }
                break;

                case 'altaPedido':
                    function test_input($data) {
                      $data = trim($data);
                      $data = stripslashes($data);
                      $data = htmlspecialchars($data);
                      return $data;
                    }
                    $errores = array();

                 
                    if($_POST['entidad_receptora_id'] == ''){
                        $errores['entidad_receptora_id'] = 'Falta ingresar la razon social de la entidad';
                    }else{
                        $entidad_receptora_id = test_input($_POST['entidad_receptora_id']);
                    }


                    if($_POST['fecha'] == ''){
                        $errores['fecha'] = 'Falta ingresar la fecha del pedido';
                    }else{
                        $fecha = test_input($_POST['fecha']);
                    }

                    if($_POST['hora'] == ''){
                        $errores['hora'] = 'Falta ingresar la hora del pedido';
                    }else{
                        $hora = test_input($_POST['hora']);
                    }

                    if($_POST['con_envio'] == ''){
                        $errores['con_envio'] = 'Falta ingresar la razon social de la entidad';
                    }else{
                        $con_envio = test_input($_POST['con_envio']);
                    }
                  


                    $variables = array("errores" => $errores);
                    
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        if ((rol($idUsuario)) == 'Administrador'){
                    echo $twig->render('cargarPedidos.html.twig', $variables);
                }else{
                    echo $twig->render('cargarPedidosGestion.html.twig', $variables);
                }

                    } else {

                        $paso=array();
                        foreach ($_POST["check_list"] as $check) {
                            $stock=  $entregaDirecta->obtenerStock($check);
                            $stock = $stock["stock"];
                            $cantidad = $_POST["cantidad_".$check];
                            if( $cantidad > $stock ){
                                $paso['paso'] = "La cantidad ingresada es mayor al stock disponible";
                            }
                        }

                      if( empty($paso)){
                        

                            $pedido_numero= $modeloPedido->altaPedido($entidad_receptora_id,$fecha,$hora,$con_envio);
                            $alimentos=$_POST["check_list"]; 
                            $i=0;
                            foreach ( $alimentos as $a) {
                              $cantidad = $_POST["cantidad_".$a];
                              $detalle_alimento_id = $a;
                              $modeloPedido-> altaAlimento( $pedido_numero, $detalle_alimento_id, $cantidad);
                              $i++;  
                             }

                            header('Location: index.php?controller=AdministradorPEController&action=verTodos');

                        }else{
                            $entidades= $modeloPedido-> mostrarEntidades();
                            $alimentos= $modeloPedido ->mostrarAlimentos();
                            $variables = array(
                                "usuario" => $_SESSION['usuario'],
                                "titulo" => "CARGAR PEDIDO",
                                "entidades"=>$entidades,
                                "alimentos"=>$alimentos,
                                "msj"=>"La cantidad ingresada es mayor al stock disponible");
                             if ((rol($idUsuario)) == 'Administrador'){
                                echo $twig->render('cargarPedidos.html.twig', $variables);
                            }else{
                                echo $twig->render('cargarPedidosGestion.html.twig', $variables);
                            }
                            
                                     
                            
                            }
                }
                break;
  
                
               case 'verTodos':
                    $pedidos= $modeloPedido-> mostrarPedidos();
                    $variables = array(
                      "usuario" => $_SESSION['usuario'],
                      "titulo" => "LISTADO PEDIDOS",
                      "pedidos" => $pedidos);
                     if ((rol($idUsuario)) == 'Administrador'){
                   echo $twig->render('listadoPedidosTodos.html.twig', $variables);
                }else{
                    echo $twig->render('listadoPedidosTodosGestion.html.twig', $variables);
                }
                    
                break;

                case 'verAlimentos':
                    $id= $_GET['id'];
                    $alimentos= $modeloPedido->verAlimentosPedido($id);
                    $variables= array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo"=> "ALIMENTOS DEL PEDIDO",
                        "alimentos" => $alimentos);
                      if ((rol($idUsuario)) == 'Administrador'){
                  echo $twig->render('alimentosPedido.html.twig', $variables);
                }else{
                   echo $twig->render('alimentosPedidoGestion.html.twig', $variables);
                }
                    
                break;

                case 'modificarPedido':
                    $id= $_GET['id'];
                    $pedido= $modeloPedido-> mostrarInfoPedido($id);
                    $alimentos= $modeloPedido-> verAlimentosPedido($id);
                    $alimentosAgregar= $modeloPedido-> mostrarAlimentos();

                    $entidades= $modeloPedido ->mostrarEntidades();

                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR PEDIDO",
                        "entidades" => $entidades,
                        "alimentos"=> $alimentos,
                        "alimentosAgregar"=>$alimentosAgregar,
                        "pedido" => $pedido );
                    if ((rol($idUsuario)) == 'Administrador'){
                  echo $twig->render('modificarPedido.html.twig', $variables);
                }else{
                   echo $twig->render('modificarPedidoGestion.html.twig', $variables);
                }
                
                break;


                case 'guardarModificado':
                    $id = $_GET['id'];
                    $pedido= $modeloPedido-> mostrarInfoPedido($id);
                       
                        
                    
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                        $errores = array();

                        
                        $pedido[0]['fecha'] = $_POST['fecha'];
                        if($pedido[0]['fecha'] == '')
                            $errores['fecha'] = 'Falta ingresar la fecha de entrega';
                
                        $pedido[0]['hora'] = $_POST['hora'];
                        if($pedido[0]['hora'] == '')
                            $errores['hora'] = 'Falta ingresar la hora de entrega';

                        $pedido[0]['con_envio'] = $_POST['con_envio'];
                        if($pedido[0]['con_envio'] == '')
                            $errores['con_envio'] = 'Falta ingresar si es con envio o no';

                         $variables = array("errores" => $errores);

                        if (count($errores) > 0) {
                            $variables['errores'] = $errores;
                           if ((rol($idUsuario)) == 'Administrador'){
                  echo $twig->render('modificarPedido.html.twig', $variables);
                }else{
                   echo $twig->render('modificarPedidoGestion.html.twig', $variables);
                }
                        } else {

                            $paso=array();
                            foreach ($_POST["check_list"] as $check) {
                                $stock=  $entregaDirecta->obtenerStock($check);
                                $stock = $stock["stock"];
                                $cantidad = $_POST["cantidad_".$check];
                                if( $cantidad > $stock ){
                                    $paso['paso'] = "La cantidad ingresada es mayor al stock disponible";
                                }
                            }

                        if( empty($paso)){

                            if ($modeloPedido->modificarPedido($pedido[0]['numero'], $pedido[0]['entidad_receptora_id'],
                                                               $pedido[0]['fecha'],$pedido[0]['hora'],
                                                               $pedido[0]['con_envio'], $pedido[0]['turno_entrega_id'])) {
                                

                                $alimentos=$_POST["check_list"];
                                $i=0;
                                foreach ( $alimentos as $a) {
                                  $detalle_alimento_id = $a; 
                                  $modeloPedido-> eliminarAlimento($pedido[0]['numero'], $detalle_alimento_id);
                                  $i++;                   
                                }
                                
                                $alimentosAgregar=$_POST["check_listA"]; 
                                foreach ( $alimentosAgregar as $a) {
                                  $cantidadAlimento = $_POST["cantidad_".$a];
                                  $detalle_alimento_id = $a;
                                  if($modeloPedido-> estaAlimento ($id, $detalle_alimento_id)){
                                     $modeloPedido-> actualizarAlimento($id, $detalle_alimento_id, $cantidadAlimento);
                                  }else{
                                    $modeloPedido-> altaAlimento( $id, $detalle_alimento_id, $cantidadAlimento);
                                  }  
                                }  

                                header('Location: index.php?controller=AdministradorPEController&action=verTodos');

                            }
                        }else{
                             $id= $_GET['id'];
                            $pedido= $modeloPedido-> mostrarInfoPedido($id);
                            $alimentos= $modeloPedido-> verAlimentosPedido($id);
                            $alimentosAgregar= $modeloPedido-> verAlimentosNoPedido($id);

                            $entidades= $modeloPedido ->mostrarEntidades();

                            $variables = array(
                                "usuario" => $_SESSION['usuario'],
                                "titulo" => "MODIFICAR PEDIDO",
                                "entidades" => $entidades,
                                "alimentos"=> $alimentos,
                                "alimentosAgregar"=>$alimentosAgregar,
                                "pedido" => $pedido,
                                "msj"=>"La cantidad ingresada es mayor al stock disponible" );
                            if ((rol($idUsuario)) == 'Administrador'){
                  echo $twig->render('modificarPedido.html.twig', $variables);
                }else{
                   echo $twig->render('modificarPedidoGestion.html.twig', $variables);
                }
                       

                        }
                   }
               }
                break;

               case 'baja':
                    $numero = $_GET['id'];
                    $alimentos= $modeloPedido->verAlimentosPedido($numero);
                    $i=0;
                    foreach ( $alimentos as $a) {
                        $modeloPedido-> eliminarAlimento($numero,$a['detalle_alimento_id']);
                        $i++;
                    }  
                    $modeloPedido->bajaPedido($numero);
                    header('Location: index.php?controller=AdministradorPEController&action=verTodos')   ;                 
                break;
            }

        } else{
            $variables = array(
                    "error" => "Inicie sesion como Admnistrador");
            echo $twig->render('login.html.twig', $variables);
        } 
            
            
    } else{
        $variables = array(
                "error" => "Inicie sesion ");
        echo $twig->render('login.html.twig', $variables);
    }
    
        
  
?> 