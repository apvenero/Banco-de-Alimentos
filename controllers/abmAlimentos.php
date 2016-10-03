<?php	
   
  require_once('./models/login.php');
  require_once('./models/Alimento.php');
  require_once ("twig.php");
  require_once('./models/Csrf.php');


   if (isset($_SESSION['idUsuario'])) {
   
    
        $idUsuario = $_SESSION['idUsuario'];        
        if ((rol($idUsuario)) == 'Administrador') {   
          $alimento= new Alimento();               
          $csrf= new CSRF();
    		    switch ($_GET['action']) {       

                case 'alta':
                    $token = $csrf->generateFormToken("formAltaAlimento");
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ALTA ALIMENTO",
                        "token"=>$token
                    );
                    echo $twig->render('altaAlimento.html.twig', $variables);
                    break;

                   case 'altaDetalle':
                   $token = $csrf->generateFormToken("formAltaDetalle");
                    $codigo= $_GET['codigo'];
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ALTA DETALLE",
                        "codigo"=> $codigo,
                        "token"=>$token
                    );
                    echo $twig->render('altaDetalle.html.twig', $variables);
                    break;

                    case 'agregarDonante':
                      $id=$_GET['id'];
                      $codigo=$_GET['codigo'];
                      require_once('./models/Donante.php');
                      $donante= new Donante();
                      $donantes=$donante-> mostrarDonantes();
                      $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "NUEVO DONANTE",
                        "id"=> $id,
                        "codigo"=>$codigo,
                        "donantes"=>$donantes
                    );
                    echo $twig->render('nuevoDonante.html.twig', $variables);
                    break;

                  case 'listar':
                  $datos=$alimento->listaAlimentos();
                  $variables = array (
                    "usuario" => $_SESSION['usuario'],
                    "titulo"=> "ABM DE ALIMENTOS",
                    "datos" => $datos
                    );
                    echo $twig->render('listadoAlimento.html.twig', $variables);
          
                  break;


                  case 'modificar':
                   $token = $csrf->generateFormToken("formModificarDetalle");
                    $codigo=$_GET['codigo'];
                    $id= $_GET['id'];
                    $alimento = $alimento->mostrarInformacionAlimento($id);
                    $contenido = array(
                      explode("x", $alimento[0]["contenido"])[0],
                      explode("x", $alimento[0]["contenido"])[1]
                    );
                    $alimento[0]["contenido"] = $contenido;

                     $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "codigo" => $codigo,
                        "alimento" =>$alimento,
                        "token" =>$token,
                        "titulo" => "MODIFICAR DETALLE"
                    );
                    echo $twig->render('modificarDetalle.html.twig', $variables);
                    break;
              
                  case 'eliminar':
                    $codigo = $_GET['codigo'];
                    $id=$_GET['id'];
                    $alimento->baja($id,$codigo);
                    header('Location: index.php?controller=abmAlimentos&action=listar');
                    
                   break; 

                   case 'eliminarDetalle':
                    $codigo=$_GET['codigo'];
                    $id=$_GET['id'];
                    $alimento->bajaDetalle($id);
                    header('Location: index.php?controller=abmAlimentos&action=verListaDetalle&codigo='.urlencode($codigo)); 
                   break;

                   case 'verDetalle':
                    require_once('./models/Donante.php');
                    $donante= new Donante();
                    $donantes=$donante-> mostrarDonantes();
                    $id= $_GET['id'];
                    $don=new Donante();
                    $don=$don->listaAlimentos($id);
                    $alimento = $alimento->mostrarInformacionAlimento($id);
                   
                    $variables= array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo"=> "DETALLE DEL ALIMENTO Y DONACIONES",
                        "alimento"=>$alimento,
                        "don"=>$don,
                        "donantes"=>$donantes,
                        
                        );
                    echo $twig->render('verAlimento.html.twig', $variables);
                  
                  break;

                    case 'verListaDetalle':
                    $codigo= $_GET['codigo'];
                    $datos=$alimento->listaDetalle($codigo);
                    $variables = array (
                    "usuario" => $_SESSION['usuario'],
                    "titulo"=> "DETALLES DEL ALIMENTO",
                    "datos" => $datos,
                    "codigo"=>$codigo
                    );
                    echo $twig->render('listaDetalle.html.twig', $variables);
          
                  break;



                  case 'donar':
                    $codigo=$_GET['codigo'];
                    $id=$_GET['id'];
                     function test_input($data) {
                      $data = trim($data);
                       $data = stripslashes($data);
                       $data = htmlspecialchars($data);
                     return $data;
                     }
                      $errores = array();
                    require_once('./models/Donante.php');
                    $donante= new Donante();
                    

                    if ($_POST['donante_id'] == ''){
                     $errores['donante_id'] = 'Falta ingresar el id';
                     }else{
                    $donante_id = test_input($_POST['donante_id']);
                  }

                  if ($_POST['cantidad'] == ''){
                     $errores['cantidad'] = 'Falta ingresar la descripcion';
                  }else{
                    $cantidad = test_input($_POST['cantidad']); 
                  }
                   $variables = array(
                    "titulo" => "",
                    "errores" =>$errores);
                  
                  if (count($errores) > 0) {
                    $variables['errores'] = $errores;
                    echo $twig->render('verAlimento.html.twig', $variables);
                    
                  } else {
                     $donante= $donante->agregarDonacion($id,$donante_id,$cantidad);
                     $alimento=$alimento->modificarStock($cantidad);
                     
                      require_once('./models/Donante.php');
                      $donante= new Donante();
                      $donantes=$donante-> mostrarDonantes();
                      $don=new Donante();
                      $alime=new Alimento();
                      $don=$don->listaAlimentos($id);
                      $alimento = $alime->mostrarInformacionAlimento($id);
                     
                    $variables= array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo"=> "DETALLES DEL ALIMENTO",
                        "alimento"=>$alimento,
                        "don"=>$don,
                        "donantes"=>$donantes,
                        
                        );
                    echo $twig->render('verAlimento.html.twig', $variables);
                   // header('Location: index.php?controller=abmAlimentos&action=verDetalle&codigo="$codigo"&id="$id"');
                    } 
                     break;



                  case 'guardar':
                  function test_input($data) {
                      $data = trim($data);
                       $data = stripslashes($data);
                       $data = htmlspecialchars($data);
                     return $data;
                  }
                   $errores = array();

                  if ($_POST['codigo'] == ''){
                     $errores['codigo'] = 'Falta ingresar el código';
                  }else{
                    $codigo = test_input($_POST['codigo']);
                  }

                  if ($_POST['descripcion'] == ''){
                     $errores['descripcion'] = 'Falta ingresar la descripcion';
                  }else{
                    $descripcion = test_input($_POST['descripcion']); 
                  }
                  if ($_POST['fecha_vencimiento'] == ''){
                     $errores['fecha_vencimiento'] = 'Falta seleccionar una fecha de vencimiento';
                  }else{
                    $fecha_vencimiento = test_input($_POST['fecha_vencimiento']);
                  }

                  if ($_POST['contenido'] == ''){
                     $errores['contenido'] = 'Falta ingresar el contenido';
                  }else{

                    $contenido = test_input($_POST['contenido'][0])."x".test_input($_POST['contenido'][1]);
                  
                  }

                  if ($_POST['peso_unitario'] == ''){
                     $errores['peso_unitario'] = 'Falta ingresar el peso del paquete';
                  }else{
                    $peso_unitario = test_input($_POST['peso_unitario']);
                  }

                  if ($_POST['stock'] == ''){
                     $errores['stock'] = 'Falta ingresar el stock';
                  }else{
                    $stock = test_input($_POST['stock']); 
                  }
                  if ($_POST['reservado'] == ''){
                     $errores['reservado'] = 'Falta ingresar la cantidad reservada';
                  }else{
                    $reservado = test_input($_POST['reservado']);
                  }

                   if ($_POST['unidad'] == ''){
                     $errores['unidad'] = 'Falta ingresar la unidad';
                  }else{
                    $unidad = test_input($_POST['unidad']);
                  }

                  $token= $_POST['token'];
                  $nombreForm="formAltaAlimento";
                  $valid = $csrf-> verifyFormToken($nombreForm, $token, 300);

                  $variables = array(
                    "titulo" => "",
                    "errores" =>$errores);
                  
                  if (count($errores) > 0) {
                    $variables['errores'] = $errores;
                    echo $twig->render('altaAlimento.html.twig', $variables);
                    
                  } else {
                      if(!$valid){
                        $msj= "ERROR, identificador incorrecto";
                        $token = $csrf->generateFormToken("formAltaAlimento");
                        $variables=array(
                          "titulo"=>"ALTA ALIMENTO",
                          "msj"=>$msj,
                          "token"=>$token );
                        echo  $twig->render('altaAlimento.html.twig', $variables);
                        
                      }else{
                    $alimento->agregarAlimento($codigo, $descripcion,$fecha_vencimiento, $contenido,
                        $peso_unitario, $stock, $reservado, $unidad);
                    header('Location: index.php?controller=abmAlimentos&action=listar');
                    }
                  }
                  
                  break;

                  case 'guardarModificado':
                    $token= $_POST['token'];
                    $nombreForm="formModificarDetalle";
                    $codigo=$_GET['codigo'];
                    $id=$_GET['id'];
                    $alimento1 = $alimento->mostrarInformacionAlimento($id);

                     if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        
                          $errores = array();


                          $alimento1[0]['fecha_vencimiento'] = $_POST['fecha_vencimiento'];
                          if($alimento1[0]['fecha_vencimiento'] == ''){ 
                              $errores['fecha_vencimiento'] = 'Falta ingresar la fecha de vencimiento';
                          }  

                          $alimento1[0]['contenido'] = $_POST['contenido'][0]."x".$_POST['contenido'][1];
                          if($alimento1[0]['contenido'] == ''){ 
                              $errores['contenido'] = 'Falta ingresar el contenido';
                          }  

                           $alimento1[0]['peso_unitario'] = $_POST['peso_unitario'];
                          if($alimento1[0]['peso_unitario'] == ''){ 
                              $errores['peso_unitario'] = 'Falta ingresar el peso del paquete';
                          }  

                           $alimento1[0]['stock'] = $_POST['stock'];
                          if($alimento1[0]['stock'] == ''){ 
                              $errores['stock'] = 'Falta ingresar el stock';
                          }  

                           $alimento1[0]['reservado'] = $_POST['reservado'];
                          if($alimento1[0]['reservado'] == ''){ 
                              $errores['reservado'] = 'Falta ingresar el número de reservas';
                          }
                            $alimento1[0]['unidad'] = $_POST['unidad'];
                          if($alimento1[0]['unidad'] == ''){ 
                              $errores['unidad'] = 'Falta ingresar la unidad';
                          }  
                        }
                      $valid = $csrf-> verifyFormToken($nombreForm, $token, 300);
                       $variables = array(
                          "errores" => $errores
                      );
                    
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        echo $twig->render('modificarDetalle.html.twig', $variables);
                          
                    } else {
                       if(!$valid){
                          $msj= "ERROR, identificador incorrecto";
                          $token = $csrf->generateFormToken("formModificarDetalle");
                          $codigo=$_GET['codigo'];
                          $id= $_GET['id'];
                          $alimento = $alimento->mostrarInformacionAlimento($id);
                          $contenido = array(
                            explode("x", $alimento[0]["contenido"])[0],
                            explode("x", $alimento[0]["contenido"])[1]
                          );
                          $alimento[0]["contenido"] = $contenido;

                         $variables = array(
                            "usuario" => $_SESSION['usuario'],
                            "codigo" => $codigo,
                            "alimento" =>$alimento,
                            "token" =>$token,
                            "titulo" => "MODIFICAR DETALLE",
                            "msj"=>$msj
                    );
                       echo  $twig->render('modificarDetalle.html.twig', $variables);
                      }else {
                        $alimento->modificarAlimento($id,$alimento1[0]['fecha_vencimiento'], $alimento1[0]['contenido'], 
                                                      $alimento1[0]['peso_unitario'],$alimento1[0]['stock'], 
                                                      $alimento1[0]['reservado'],$alimento1[0]['unidad']);
                        header('Location: index.php?controller=abmAlimentos&action=verListaDetalle&codigo='.urlencode($codigo));
                       }
                    }
                       
                                
                    break;   


                  case 'guardarDetalle':

                  $codigo=$_GET['codigo'];
                  function test_input($data) {
                      $data = trim($data);
                       $data = stripslashes($data);
                       $data = htmlspecialchars($data);
                     return $data;
                  }
                   $errores = array();

                  if ($_POST['fecha_vencimiento'] == ''){
                     $errores['fecha_vencimiento'] = 'Falta seleccionar una fecha de vencimiento';
                  }else{
                    $fecha_vencimiento = test_input($_POST['fecha_vencimiento']);
                  }

                  if ($_POST['contenido'] == ''){
                     $errores['contenido'] = 'Falta ingresar el contenido';
                  }else{

                    $contenido = test_input($_POST['contenido'][0])."x".test_input($_POST['contenido'][1]);
                  
                  }

                  if ($_POST['peso_unitario'] == ''){
                     $errores['peso_unitario'] = 'Falta ingresar el peso del paquete';
                  }else{
                    $peso_unitario = test_input($_POST['peso_unitario']);
                  }

                  if ($_POST['stock'] == ''){
                     $errores['stock'] = 'Falta ingresar el stock';
                  }else{
                    $stock = test_input($_POST['stock']); 
                  }
                  if ($_POST['reservado'] == ''){
                     $errores['reservado'] = 'Falta ingresar la cantidad reservada';
                  }else{
                    $reservado = test_input($_POST['reservado']);
                  }

                   if ($_POST['unidad'] == ''){
                     $errores['unidad'] = 'Falta ingresar la unidad';
                  }else{
                    $unidad = test_input($_POST['unidad']);
                  }
                   $token= $_POST['token'];
                  $nombreForm="formAltaDetalle";
                  $valid = $csrf-> verifyFormToken($nombreForm, $token, 300);

                  $variables = array(
                    "titulo" => "ALTA DETALLE",
                    "errores" =>$errores);
                  
                  if (count($errores) > 0) {
                    $variables['errores'] = $errores;
                    echo $twig->render('altaDetalle.html.twig', $variables);
                    
                  } else {
                      if(!$valid){
                        $msj= "ERROR, identificador incorrecto";
                        $token = $csrf->generateFormToken("formAltaDetalle");
                        $codigo= $_GET['codigo'];
                      $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "ALTA DETALLE",
                        "codigo"=> $codigo,
                        "token"=>$token,
                        "msj"=>$msj
                    );
                        
                      }else{

                    $alimento->agregarNuevoDetalle($codigo,$fecha_vencimiento, $contenido,
                        $peso_unitario, $stock, $reservado, $unidad);

                    header('Location: index.php?controller=abmAlimentos&action=verListaDetalle&codigo='.urlencode($codigo));
                   
                    }
                }
         
              
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