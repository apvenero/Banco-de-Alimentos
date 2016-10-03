<?php

    require_once ('./models/login.php');
    require_once ("models/Usuario.php");
    require_once ('./models/Donante.php');
    require_once ("twig.php");
 
	if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
        if ((rol($idUsuario)) == 'Administrador') {   
            
            $modeloDonante = new Donante();

    		switch ($_GET['action']) {       

                case 'informacion':
                    $donantes= $modeloDonante->mostrarDonantes();
                    $variables = array(
                        "titulo"=> "ABM Donantes",
                        "usuario" => $_SESSION['usuario'],
                        "donantes" => $donantes);
                    echo $twig->render('ABMDonantes.html.twig', $variables);
                break;

                case 'modificar':
                    $idDonante= $_GET['id'];
                    $modeloDonante->mostrarInfoDonante($idDonante);

                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR DONANTE",
                        "donante" => $donante );
                    echo $twig->render('modificarDonante.html.twig', $variables);
                break;
        
                case 'guardarModificacion':
                    $idDonante = $_GET['id'];
                    $donante = $modeloDonante->mostrarInfoDonante($idDonante);
                       
                                  
                            function test_input($data) {
                                    $data = trim($data);
                                    $data = stripslashes($data);
                                    $data = htmlspecialchars($data);
                                    return $data;
                            }

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                        $errores = array();


                        $donante[0]['razon_social']  = test_input($_POST['razon_social']);
                        if($donante[0]['razon_social'] == '')
                            $errores['razon_social'] = 'Falta ingresar razon social';
                            

                        $donante[0]['apellido_contacto'] = test_input($_POST['apellido_contacto']);
                        if ($donante[0]['apellido_contacto'] == '')
                            $errores['apellido_contacto'] = 'Falta ingresar el apellido';
                      
                        $donante[0]['nombre_contacto'] = test_input($_POST['nombre_contacto']);
                        if ($donante[0]['nombre_contacto'] == '')
                            $errores['nombre_contacto'] = 'Falta ingresar el nombre';
                      
                        $donante[0]['telefono_contacto'] = test_input($_POST['telefono_contacto']);
                        if ($donante[0]['telefono_contacto'] == '')
                            $errores['telefono_contacto'] = 'Falta ingresar el telefono';
                      
                        $donante[0]['mail_contacto'] = test_input($_POST['mail_contacto']);
                        if ($donante[0]['mail_contacto'] == '')
                        	$errores['mail_contacto'] = 'Falta ingresar el mail';
                                                
                        
                        $donante[0]['domicilio_contacto'] = test_input($_POST['domicilio_contacto']);
                        if ($donante[0]['domicilio_contacto'] == '')
                            $errores['domicilio_contacto'] = 'Falta ingresar el domicilio';
                        

                                               
                                 
                         $variables = array("errores" => $errores);
                    
                        if (count($errores) > 0) {
                            $variables['errores'] = $errores;
                            echo $twig->render('modificarDonante.html.twig', $variables);
                          
                        } else {
                            if ($modeloDonante->modificarDonante($idDonante, $donante[0]['razon_social'], 
                                                            $donante[0]['apellido_contacto'],$donante[0]['nombre_contacto'],
                                                            $donante[0]['telefono_contacto'],$donante[0]['mail_contacto'],
                                                            $donante[0]['domicilio_contacto'])) {
 					            header('Location: index.php?controller=AdministradorDOController&action=informacion');

                            }
                        }
                   }
                break;   


                case 'alta':
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ALTA DONANTE" );
                    echo $twig->render('altaDonante.html.twig', $variables);
                break;  


                case 'guardarAlta':

                    function test_input($data) {
                      $data = trim($data);
                      $data = stripslashes($data);
                      $data = htmlspecialchars($data);
                      return $data;
                    }
                    $errores = array();
                    
                    if($_POST['razon_social'] == ''){
                        $errores['razon_social'] = 'Falta ingresar la razon social de la entidad';
                    }else{
                        $razon_social = test_input($_POST['razon_social']);
                    }

                    if($_POST['apellido_contacto'] == ''){
                        $errores['apellido_contacto'] = 'Falta ingresar el apellido del donante';
                    }else{
                        $apellido_contacto= test_input($_POST['apellido_contacto']);
                    }

                    if($_POST['nombre_contacto'] == ''){
                        $errores['nombre_contacto'] = 'Falta ingresar el nombre del donante';
                    }else{
                        $nombre_contacto= test_input($_POST['nombre_contacto']);
                    }

                    if($_POST['domicilio_contacto'] == ''){
                        $errores['domicilio_contacto'] = 'Falta ingresar el domicilio del donante';
                    }else{
                        $domicilio_contacto= test_input($_POST['domicilio_contacto']);
                    }

                    if($_POST['telefono_contacto'] == ''){
                        $errores['telefono_contacto'] = 'Falta ingresar el número de telefono del donante';
                    }else{
                        $telefono_contacto= test_input($_POST['telefono_contacto']);
                    }

                    if($_POST['mail_contacto'] == ''){
                        $errores['mail_contacto'] = 'Falta ingresar el mail del donante';
                    }else{
                        $mail_contacto= test_input($_POST['mail_contacto']);
                    }
           
                   

                    $variables = array(
                        "errores" => $errores
                    );
                    
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        echo $twig->render('altaDonante.html.twig', $variables);
                        //require_once("views/altausuario.php");  
                    } else {
                          
                            if ($modeloDonante->altaDonante($razon_social, $apellido_contacto, $nombre_contacto,
                                 $domicilio_contacto,$telefono_contacto, $mail_contacto)){

                              header('Location: index.php?controller=AdministradorDOController&action=informacion');
                             }
                        }
                break;
                    
                case 'baja':
                    $idDonante = $_GET['id'];
                    $modeloDonante->bajaDonante($idDonante);
                    header('Location: index.php?controller=AdministradorDOController&action=informacion');
                    
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