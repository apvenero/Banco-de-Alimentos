<?php

    require_once ('./models/login.php');
    require_once ('./models/EntregaDirecta.php');
    require_once('./models/Pedido.php');
    require_once('./models/Configuracion.php');
    require_once ("twig.php");
    require_once('models/Csrf.php');
 
  if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
         if (((rol($idUsuario)) == 'Administrador') or (rol($idUsuario)) == 'Gestion') { 
            $modeloPedido=new Pedido();
            $entregaDirecta= new EntregaDirecta();
            $configuracion = new Configuracion();
            $csrf= new CSRF();
        switch ($_GET['action']) {       
                 case 'realizarEntregaD':

                  $cantidad = $configuracion ->cantidadDias();
                  $cant = $cantidad[0]["valor"];
                  $cantDias = $entregaDirecta -> determinarDia($cant);
                  $verAlimentosAVencer = $entregaDirecta -> mostrarAlimentosPorVencer($cantDias);
                  $entidades= $modeloPedido-> mostrarEntidades();
                  $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ENTREGA DIRECTA",
                        "entidades" => $entidades,
                        "alimentos" => $verAlimentosAVencer,
                        "mensaje" =>""
                        );
                    if ((rol($idUsuario)) == 'Administrador'){
                    echo $twig->render('entregaDirecta.html.twig', $variables);
                }else{
                     echo $twig->render('entregaDirectaGestion.html.twig', $variables);
                }
                   
                break;

                case 'guardarEntregaD':
                    
                    function test_input($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                     }
                     $errores = array();

                      if ($_POST['entidad_id'] == ''){
                         $errores['entidad_id'] = 'Falta seleccionar una entidad receptora';
                      }else{
                        $entidad_id = test_input($_POST['entidad_id']); 
                      }
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
                        
                        $idEntrega = $entregaDirecta->guardarEntregaD($entidad_id);

                        foreach ($_POST["check_list"] as $check) {
                          //var_dump("cantidad_".$check);
                          $cantidad = $_POST["cantidad_".$check];
                          $id_det = $check;
                          $modeloPedido->agregarAlimentoEntregaDirecta($idEntrega, $id_det, $cantidad);
                        }

                         $cantidad = $configuracion ->cantidadDias();
                         $cant = $cantidad[0]["valor"];
                         $cantDias = $entregaDirecta -> determinarDia($cant);

                        $verAlimentosAVencer = $entregaDirecta -> mostrarAlimentosPorVencer($cantDias);
                        $entidades= $modeloPedido-> mostrarEntidades();
                        $variables = array(
                          "usuario" => $_SESSION['usuario'],
                          "titulo" => "ENTREGA DIRECTA",
                          "entidades" => $entidades,
                          "alimentos" => $verAlimentosAVencer,
                          "mensaje" =>"SU ENTREGA SE REALIZO CON EXITO"
                          );
                         if ((rol($idUsuario)) == 'Administrador'){
                    echo $twig->render('entregaDirecta.html.twig', $variables);
                }else{
                     echo $twig->render('entregaDirectaGestion.html.twig', $variables);
                }
                   
                  }else{
                     $cantidad = $configuracion ->cantidadDias();
                          $cant = $cantidad[0]["valor"];
                          $cantDias = $entregaDirecta -> determinarDia($cant);
                          $verAlimentosAVencer = $entregaDirecta -> mostrarAlimentosPorVencer($cantDias);
                          $entidades= $modeloPedido-> mostrarEntidades();
                          $variables = array(
                                "usuario" => $_SESSION['usuario'],
                                "titulo" => "ENTREGA DIRECTA",
                                "entidades" => $entidades,
                                "alimentos" => $verAlimentosAVencer,
                                "mensaje" =>"La cantidad ingresada es mayor al stock disponible"
                                );
                               if ((rol($idUsuario)) == 'Administrador'){
                    echo $twig->render('entregaDirecta.html.twig', $variables);
                }else{
                     echo $twig->render('entregaDirectaGestion.html.twig', $variables);
                }
                   
               
                     
                  }
                   
                
                     break;

               

                     
                
            }

        } else{
            $variables = array(
            "error" => "Inicie sesion como Admnistrador");
            echo $twig->render('login.html.twig', $variables);
        }
    }
?>
