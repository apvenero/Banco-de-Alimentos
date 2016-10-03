<?php

    require_once ('./models/login.php');
    require_once ('./models/Turnos.php');
    require_once ('./models/Pedido.php');

    require_once ("twig.php");
 
	if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
        if (((rol($idUsuario)) == 'Administrador') or (rol($idUsuario)) == 'Gestion') { 
            
            $modeloTurno = new Turnos();
            $modeloPedido = new Pedido();

    		switch ($_GET['action']) {       

                case 'listar':
                    $turnos= $modeloTurno-> mostrarTurnos();
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "LISTADO TURNOS DE ENTREGA",
                        "turnos"=>$turnos);
                     if ((rol($idUsuario)) == 'Administrador'){
                     echo $twig->render('listadoTurnos.html.twig', $variables);
                }else{
                     echo $twig->render('listadoTurnosGestion.html.twig', $variables);
                }
                   
                break;

                case 'buscar':
                    $fecha= $_POST['fecha'];
                    $turnos=$modeloTurno-> buscarTurnos($fecha);
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "LISTADO TURNOS DE ENTREGA",
                        "turnos"=>$turnos);
                    if ((rol($idUsuario)) == 'Administrador'){
                     echo $twig->render('listadoTurnos.html.twig', $variables);
                }else{
                     echo $twig->render('listadoTurnosGestion.html.twig', $variables);
                }
                break;
                
                case 'modificar':
                    $id= $_GET['id'];
                    $turno= $modeloTurno-> mostrarInfoTurno($id);
                    $estados= $modeloPedido ->mostrarEstados();

                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR TURNO",
                        "estados" => $estados,
                        "turno" => $turno );
                    if ((rol($idUsuario)) == 'Administrador'){
                     echo $twig->render('modificarTurno.html.twig', $variables);
                }else{
                     echo $twig->render('modificarTurnosGestion.html.twig', $variables);
                }
                    
                break;
                
                case 'guardarModificacion':
                    $id = $_GET['id'];
                    $turno = $modeloTurno->mostrarInfoTurno($id);
                       
                    function test_input($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                        $errores = array();


                        $turno[0]['fecha']  = test_input($_POST['fecha']);
                        if($turno[0]['fecha'] == '')
                            $errores['fecha'] = 'Falta ingresar la fecha dle turno';
                            

                        $turno[0]['hora'] = test_input($_POST['hora']);
                        if ($turno[0]['hora'] == '')
                            $errores['hora'] = 'Falta ingresar la hora del turno';
                      
                        $turno[0]['estado_pedido_id'] = test_input($_POST['estado_pedido_id']);
                        if ($turno[0]['estado_pedido_id'] == '')
                            $errores['estado_pedido_id'] = 'Falta ingresar el estado del turno';
                      
                                             
                                 
                         $variables = array("errores" => $errores);
                    
                        if (count($errores) > 0) {
                            $variables['errores'] = $errores;
                               if ((rol($idUsuario)) == 'Administrador'){
                     echo $twig->render('modificarTurnos.html.twig', $variables);
                }else{
                     echo $twig->render('modificarTurnosGestion.html.twig', $variables);
                }
                          
                        } else {
                            if ($modeloTurno->modificarTurno($id, $turno[0]['fecha'], 
                                                            $turno[0]['hora'],$turno[0]['estado_pedido_id'])){
                                header('Location: index.php?controller=TurnosEntregaController&action=listar');
                                print 'ana';
                            }
                        }
                   }
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