<?php

    require_once ('models/login.php');
    require_once ("models/Usuario.php");
    require_once ("twig.php");
    require_once('models/Csrf.php');
 
	if (isset($_SESSION['idUsuario'])) {
   
    
        $idUsuario = $_SESSION['idUsuario'];        
        if ((rol($idUsuario)) == 'Administrador') {   
            
            $csrf= new CSRF();
            $usuario = new Usuario();
                    

    		switch ($_GET['action']) {      

                case 'listarUsuarios':
                    $usus=$usuario->mostrarUsuarios();
                    $variables = array(
                        "usuarioReg" => $_SESSION['usuario'],
                        "titulo" => "LISTADO DE USUARIOS",
                        "usus" => $usus
                    );
                    echo $twig->render('listadoUsuarios.html.twig', $variables);
                    break;
 

              case 'alta':
                    $token = $csrf->generateFormToken("formAltaUsuario");
                    $variables = array(
                        "usuarioReg" => $_SESSION['usuario'],
                        "titulo" => "ALTA DE USUARIO",
                        "token"=>$token
                    );
                    echo $twig->render('altaUsuario.html.twig', $variables);
                    break;

                case 'guardar':
                    function test_input($data) {
                      $data = trim($data);
                      $data = stripslashes($data);
                      $data = htmlspecialchars($data);
                      return $data;
                    }
                  
                    $errores = array();

                     if($_POST['nombreUsuario'] == ''){
                        $errores['nombreUsuario'] = 'Falta ingresar el nombre de usuario';
                    }else{
                        $nombreUsuario=test_input($_POST['nombreUsuario']);
                    }

                    if ($_POST['pass'] == ''){
                        $errores['pass'] = 'Falta ingresar la contraseña';
                    }else{
                    $pass = test_input($_POST['pass']);
                    }

                    if($_POST['rol'] == ''){
                        $errores['rol'] = 'Falta seleccionar un rol';
                    }else{
                    $rol= test_input($_POST['rol']);
                    }

                    $rol=$_POST['rol'];
                    $nombreUsu=$_POST['nombreUsuario'];

                         $variables = array(
                            "errores" => $errores,
                            "rol"=>$rol,
                            "nombreUsu"=>$nombreUsu
                    );
                    if (count($errores) > 0) {
                         $variables['errores'] = $errores;
                        echo $twig->render('altaUsuario.html.twig', $variables);
                      
                    } else {
                            if ($usuario->existeUsuario($_POST['nombreUsuario'])) {
                                $variables['errores'] = "Ya existe ese usuario";
                                $variables ['rol'] = $rol;
                                $variables['nombreUsu'] = $nombreUsu;
                                echo $twig->render('altaUsuario.html.twig', $variables);
                               
                            }else{
                                $id=$usuario->altaUsuario($nombreUsuario, $pass, $rol );
                                header('Location: index.php?controller=abmUsuarios&action=listarUsuarios');
                            }
                    }
                        
                    break;

                    case 'modificar':
                    $token = $csrf->generateFormToken("formModificarUsuario");
                    $idUsuario= $_GET['id'];
                    $usua= $usuario->mostrarInformacionUsuario($idUsuario);

                     $variables = array(
                        "usuarioReg" => $_SESSION['usuario'],
                        "usua" =>$usua,
                        "titulo" => "MODIFICAR USUARIO",
                        "token"=>$token
                    );
                    echo $twig->render('modificarUsuario.html.twig', $variables);
                    break;

                    case 'guardarModificacion':
                        $idUsuario = $_GET['id'];
                        $usua = $usuario->mostrarInformacionUsuario($idUsuario);

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                            $errores = array();

                             $usua[0]['usuario'] = $_POST['nombreUsuario'];
                            if ($usua[0]['usuario'] == ''){
                                $errores['nombreUsuario'] = 'Falta ingresar el nombre de usuario';
                            }
                       
                            $usua[0]['pass'] = $_POST['pass'];
                            if ($usua[0]['pass'] == ''){
                                $errores['pass'] = 'Falta ingresar la contraseña';
                            }
                        
                            $usua[0]['rol'] = $_POST['rol'];
                            if ($usua[0]['rol'] == ''){
                                $errores['rol'] = 'Falta seleccionar el rol';
                            } 
                    }

                     $variables = array(
                        "errores" => $errores
                    );
                    $token= $_POST['token'];
                    $nombreForm="formModificarUsuario";
                    $valid = $csrf-> verifyFormToken($nombreForm, $token, 300);

                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        echo $twig->render('modificarUsuario.html.twig', $variables);
                          
                    } else {
                        if(!$valid){
                                $msj= "ERROR, identificador incorrecto";
                                $token = $csrf->generateFormToken("formModificarUsuario");

                                $idUsuario= $_GET['id'];
                                $usua= $usuario->mostrarInformacionUsuario($idUsuario);

                                 $variables = array(
                                    "usuarioReg" => $_SESSION['usuario'],
                                    "usua" =>$usua,
                                    "titulo" => "MODIFICAR USUARIO",
                                    "token"=>$token,
                                    "msj"=>$msj
                                );
                                echo $twig->render('modificarUsuario.html.twig', $variables);
                        } else{ 
                           $usuario->modificarUsuario($idUsuario,$usua[0]['usuario'], $usua[0]['pass'], 
                                                     $usua[0]['rol']);
                           header('Location: index.php?controller=abmUsuarios&action=listarUsuarios');
                          }      
                              
                    }
                    break;

                    case 'eliminar':
                        $id=$_GET['id'];
                        $usuario->baja($id);
                        header('Location: index.php?controller=abmUsuarios&action=listarUsuarios');
                    break;

                        
            
        }
   }else{
            $variables = array(
                    "error" => "Inicie sesion como Admnistrador");
            echo $twig->render('login.html.twig', $variables);}
   } else{
            $variables = array(
            "error" => "Inicie sesion ");
            echo $twig->render('login.html.twig', $variables);
   }

?>