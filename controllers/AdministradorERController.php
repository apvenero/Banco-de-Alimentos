<?php

    require_once ('./models/login.php');
    require_once ('./models/Usuario.php');
    require_once ('./models/EntidadReceptora.php');
    require_once ("twig.php");
    require_once('models/Csrf.php');
 
    if (isset($_SESSION['idUsuario'])) {
       
        $idUsuario = $_SESSION['idUsuario'];        
        if ((rol($idUsuario)) == 'Administrador') {   
            
            $modeloEntidad = new EntidadReceptora();
            $csrf= new CSRF();
            switch ($_GET['action']) {       

                case 'informacion':
                    $entidades= $modeloEntidad->mostrarEntidades();
                    $variables = array(
                        "titulo"=> "ABM ENTIDADES RECEPTORAS",
                        "usuario" => $_SESSION['usuario'],
                        "entidades" => $entidades);
                    echo $twig->render('ABMentidades.html.twig', $variables);
                break;

                case 'ver':
                    $id= $_GET['id'];
                    $modeloEntidad->mostrarMasEntidad($id);
                    $variables= array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo"=> "DATOS DE LA ENTIDAD",
                        "entidad" => $entidad);
                    echo $twig->render('verEntidad.html.twig', $variables);
                  
                break;

       
                case 'modificar':
                    $token = $csrf->generateFormToken("formModificarEntidad");
                    $id= $_GET['id'];
                    $modeloEntidad->mostrarMasEntidad($id);
                    $servicios =$modeloEntidad->mostrarServicios();
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "MODIFICAR ENTIDAD",
                        "entidad" => $entidad,
                        "token"=>$token,
                        "servicios" =>$servicios);
                    echo $twig->render('modificarEntidad.html.twig', $variables);
                break;
                    

                case 'guardarModificacion':
                    $id = $_GET['id'];
                     $token= $_POST['token'];
                    $nombreForm="formModificarEntidad";
                    $entidad= $modeloEntidad->mostrarMasEntidad($id);
                    
                    function test_input($data) {
                                    $data = trim($data);
                                    $data = stripslashes($data);
                                    $data = htmlspecialchars($data);
                                    return $data;
                    }   
                        
                    
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                        $errores = array();

                        
                        $entidad[0]['razon_social'] = test_input($_POST['razon_social']);
                        if($entidad[0]['razon_social'] == ''){ 
                            $errores['razon_social'] = 'Falta ingresar razon social';
                        }    

                        $entidad[0]['telefono'] = test_input($_POST['telefono']);
                        if ($entidad[0]['telefono'] == '')
                            $errores['telefono'] = 'Falta ingresar el telefono';
                      
                        $entidad[0]['domicilio'] = test_input($_POST['domicilio']);
                        if ($entidad[0]['domicilio'] == '')
                            $errores['domicilio'] = 'Falta ingresar el domicilio';
                        
                                               
                        $entidad[0]['servicio_prestado_id'] = test_input($_POST['servicio_prestado_id']);
                        if ($entidad[0]['servicio_prestado_id'] == '')
                            $errores['servicio_prestado_id'] = 'Falta ingresar el servicio prestado';
                        
                                              
                        $entidad[0]['necesidad_entidad_id'] = test_input($_POST['necesidad_entidad_id']);
                        if ($entidad[0]['necesidad_entidad_id'] == '')
                            $errores['necesidad_entidad_id'] = 'Falta seleccionar la necesidad de la entidad';
                        
                        $entidad[0]['estado_entidad_id'] = test_input($_POST['estado_entidad_id']);
                        if ($entidad[0]['estado_entidad_id'] == '')
                            $errores['estado_entidad_id'] = 'Falta seleccionar el estado de la entidad';

                         $entidad[0]['latitud'] = test_input($_POST['latitud']);
                        if ($entidad[0]['latitud'] == '')
                            $errores['latitud'] = 'Falta ingresar la latitud de la entidad';

                         $entidad[0]['longitud'] = test_input($_POST['longitud']);
                        if ($entidad[0]['longitud'] == '')
                            $errores['longitud'] = 'Falta ingresar la longitud de la entidad';
                    }
                    $valid = $csrf-> verifyFormToken($nombreForm, $token, 300);
                     $variables = array("errores" => $errores);
                    
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        echo $twig->render('modificarEntidad.html.twig', $variables);
                    } else {
                       if(!$valid){
                          $msj= "ERROR, identificador incorrecto";
                          $token = $csrf->generateFormToken("formModificarEntidad");
                        $id= $_GET['id'];
                        $modeloEntidad->mostrarMasEntidad($id);
                        $servicios =$modeloEntidad->mostrarServicios();
                        $variables = array(
                            "usuario" => $_SESSION['usuario'],
                            "titulo" => "MODIFICAR ENTIDAD",
                            "entidad" => $entidad,
                            "token"=>$token,
                            "msj"=>$msj,
                            "servicios" =>$servicios);
                        echo $twig->render('modificarEntidad.html.twig', $variables);
                              
                    } else {
                       if ($modeloEntidad->modificarEntidad($id, $entidad[0]['razon_social'], 
                                                            $entidad[0]['telefono'],$entidad[0]['domicilio'],
                                                            $entidad[0]['servicio_prestado_id'],
                                                            $entidad[0]['necesidad_entidad_id'],
                                                            $entidad[0]['estado_entidad_id'],  
                                                            $entidad[0]['latitud'],
                                                            $entidad[0]['longitud'])){
                        $entidades= $modeloEntidad->mostrarEntidades();
                        $variables = array(
                             "titulo"=> "ABM ENTIDADES RECEPTORAS",
                            "usuario" => $_SESSION['usuario'],
                            "entidades" => $entidades);
                        echo $twig->render('ABMentidades.html.twig', $variables); 
                        }
                    }
                    }     
                                
                break;


               case 'eliminar':
                    $idEntidad = $_GET['id'];
                    $modeloEntidad-> eliminarEntidad($idEntidad);
                    header('Location: index.php?controller=AdministradorERController&action=informacion');
                break;   
              


                case 'alta':
                    $servicios =$modeloEntidad->mostrarServicios();
                    $token = $csrf->generateFormToken("formAltaEntidad");
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "token"=> $token,
                        "titulo" => "ALTA ENTIDAD",
                        "servicios" =>$servicios);
                    echo $twig->render('altaEntidad.html.twig', $variables);
                break;

                               
                case 'guardar':

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

                    if($_POST['telefono'] == ''){
                        $errores['telefono'] = 'Falta ingresar el número de telefono';
                    }else{
                        $telefono= test_input($_POST['telefono']);
                    }

                    if($_POST['domicilio'] == ''){
                        $errores['domicilio'] = 'Falta ingresar el domicilio';
                    }else{
                        $domicilio= test_input($_POST['domicilio']);
                    }
           
                   if($_POST['servicio_prestado_id'] == ''){
                        $errores['servicio_prestado_id'] = 'Falta ingresar el servicio prestado de la entidad';
                    }else{
                        $servicio_prestado_id= test_input($_POST['servicio_prestado_id']);
                    }

                      if($_POST['necesidad_entidad_id'] == ''){
                        $errores['necesidad_entidad_id'] = 'Falta ingresar la necesidad de la entidad';
                    }else{
                        $necesidad_entidad_id= test_input($_POST['necesidad_entidad_id']);
                    }


                      if($_POST['estado_entidad_id'] == ''){
                        $errores['estado_entidad_id'] = 'Falta ingresar el estado de la entidad';
                    }else{
                        $estado_entidad_id= test_input($_POST['estado_entidad_id']);
                    }
                    if($_POST['latitud'] == ''){
                        $errores['latitud'] = 'Falta ingresar la latitud de la entidad';
                    }else{
                        $latitud= test_input($_POST['latitud']);
                    }
                    if($_POST['longitud'] == ''){
                        $errores['longitud'] = 'Falta ingresar la longitud de la entidad';
                    }else{
                        $longitud= test_input($_POST['longitud']);
                    }
                  
                    $variables = array(
                        "errores" => $errores
                    );
                    
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        echo $twig->render('altaEntidad.html.twig', $variables);
                    }else {

                          
                            if ($modeloEntidad->altaEntidad ($razon_social, $telefono, $domicilio, $servicio_prestado_id,
                                $necesidad_entidad_id, $estado_entidad_id, $latitud, $longitud)){

                              header('Location: index.php?controller=AdministradorERController&action=informacion');
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