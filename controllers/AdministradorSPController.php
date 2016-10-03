<?php

    require_once ('./models/login.php');
    require_once ('./models/ServicioPrestado.php');
    require_once ("twig.php");
 
	if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
        if ((rol($idUsuario)) == 'Administrador') {   
            
            $modeloServicio = new ServicioPrestado();

    		switch ($_GET['action']) {       

                case 'listar':
                    $servicios= $modeloServicio->mostrarServicios();
                    $variables = array(
                        "titulo"=> "ABM Servicios Prestados",
                        "usuario" => $_SESSION['usuario'],
                        "servicios" => $servicios);
                    echo $twig->render('ABMServicios.html.twig', $variables);
                break;

                case 'modificar':
                    $id= $_GET['id'];
                    $servicio= $modeloServicio->mostrarInfoServicio($id);

                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR SERVICIO",
                        "servicio" => $servicio);
                    echo $twig->render('modificarServicio.html.twig', $variables);
                break;
        
                case 'guardarModificacion':
                    $idServicio = $_GET['id'];
                    $servicio = $modeloServicio->mostrarInfoServicio($idServicio);
                       
                        
                    
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                        $errores = array();

                        $servicio[0]['descripcionServicio'] = $_POST['descripcionServicio'];
                        if($servicio[0]['descripcionServicio'] == '')
                            $errores['descripcionServicio'] = 'Falta ingresar la descripcion del servicio prestado';
                            
                         $variables = array("errores" => $errores);
                    
                        if (count($errores) > 0) {
                            $variables['errores'] = $errores;
                            echo $twig->render('modificarServicio.html.twig', $variables);
                          
                        } else {
                            if ($modeloServicio->modificarServicio($servicio[0]['id'], $servicio[0]['descripcionServicio'])) {
                                $servicios= $modeloServicio->mostrarServicios();
                                $variables = array(
                                    "usuario" => $_SESSION['usuario'],
                                    "titulo" => "ABM Servicios Prestados",
                                    "servicios" => $servicios);
                                echo $twig->render('ABMServicios.html.twig', $variables);
                            }
                        }
                   }
                break;   


                case 'alta':
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ALTA SERVICIO" );
                    echo $twig->render('altaServicio.html.twig', $variables);
                break;  


                case 'guardarAlta':

                    function test_input($data) {
                      $data = trim($data);
                      $data = stripslashes($data);
                      $data = htmlspecialchars($data);
                      return $data;
                    }
                    $errores = array();
                    
                    if($_POST['descripcionServicio'] == ''){
                        $errores['descripcionServicio'] = 'Falta ingresar la descripcion del servicio';
                    }else{
                        $descripcionServicio = test_input($_POST['descripcionServicio']);
                    }

              

                    $variables = array(
                        "errores" => $errores
                    );
                    
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        echo $twig->render('altaServicio.html.twig', $variables);
                    } else {
                          
                            if ($modeloServicio->altaServicio($descripcionServicio)){

                              header('Location: index.php?controller=AdministradorSPController&action=listar');
                             }
                        }
                break;
                    
                case 'baja':
                    $idServicio = $_GET['id'];
                    $modeloServicio->bajaServicio($idServicio);
                    header('Location: index.php?controller=AdministradorSPController&action=listar');
                    
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