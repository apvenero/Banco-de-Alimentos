<?php

    require_once ('models/login.php');
    require_once ("models/Configuracion.php");
    require_once ("twig.php");
    require_once('models/Csrf.php');
    
	if (isset($_SESSION['idUsuario'])) {
   
    
        $idUsuario = $_SESSION['idUsuario'];        
        if ((rol($idUsuario)) == 'Administrador') {   
            
            $csrf= new CSRF();
            $configuracion = new Configuracion();
                    

    		switch ($_GET['action']) { 

    			case 'verConfig':
                    $idClavePublica =$configuracion-> obtenerClaveValor('idClavePublica');
                    $idClavePrivada= $configuracion->obtenerClaveValor('idClavePrivada');
                    $cantidad = $configuracion->obtenerClaveValor('cantidad');
                    $longitud = $configuracion->obtenerClaveValor('longitud');
                    $latitud = $configuracion->obtenerClaveValor('latitud');
                    $idCPu=$configuracion-> obtenerID('idClavePublica');
                    $idCPr=$configuracion->obtenerID('idClavePrivada');
                    $idCant=$configuracion->obtenerID('cantidad');
                    $idLon= $configuracion->obtenerID('longitud');
                    $idLa=$configuracion->obtenerID('latitud');
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "CONFIGURACION",
                        "idClavePublica"=> $idClavePublica,
                        "idClavePrivada"=> $idClavePrivada,
                        "cantidad"=> $cantidad,
                        "longitud"=> $longitud,
                        "latitud" => $latitud,
                        "idCPu" => $idCPu,
                        "idCPr"=> $idCPr,
                        "idCant"=> $idCant,
                        "idLon"=>$idLon,
                        "idLa"=> $idLa
					);
                    echo $twig->render('verConfig.html.twig', $variables);

    			   	break;   

                case 'modificarConfig':
                    $token = $csrf->generateFormToken("formModificarConfig");
                    $id=$_GET['id'];
                    $config = $configuracion->verConfiguracion($id);
                     $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR CONFIGURACION",
                        "id"=>$id,
                        "token"=>$token,
                        "configuracion"=> $config
                    );
                    echo $twig->render('modificarConfig.html.twig', $variables);
                    break;


                case 'guardarModificado':
                    function test_input($data) {
                      $data = trim($data);
                      $data = stripslashes($data);
                      $data = htmlspecialchars($data);
                      return $data;
                    }
                    $id=$_GET['id'];

                    if ($_POST['clave'] == ''){
                         $errores['clave'] = 'Falta ingresar la clave';
                      }else{
                        $clave = test_input($_POST['clave']); 
                      }

                    if ($_POST['valor'] == ''){
                         $errores['valor'] = 'falta ingresar el';
                      }else{
                        $valor = test_input($_POST['valor']); 
                      }
                    $token= $_POST['token'];
                    $nombreForm="formModificarConfig";
                    $valid = $csrf-> verifyFormToken($nombreForm, $token, 300);
                    if(!$valid){
                        $msj= "El identificador no es v&aacute;lido";
                        $token = $csrf->generateFormToken("formModificarConfig");
                        $variables=array(
                          "titulo"=>"MODIFICAR CONFIGURACION",
                          "msj"=>$msj,
                          "token"=>$token );
                        echo  $twig->render('verConfig.html.twig', $variables);
                        
                      }else{
                        $config = $configuracion->guardarModificado($id,$clave,$valor);
                    /* $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR CONFIGURACION",
                        "configuracion"=> $config
                    );
                    echo $twig->render('modificarConfig.html.twig', $variables);*/
                        header('Location: index.php?controller=AdministradorConfigController&action=verConfig');
                    }
                    break;
                   

    		}
    	}
    }
?>  

