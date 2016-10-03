<?php	
   
  require_once('./models/login.php');
  require_once ("twig.php");
  require_once('./models/Envio.php');
  require_once('./models/Configuracion.php');


   if (isset($_SESSION['idUsuario'])) {
   
    
        $idUsuario = $_SESSION['idUsuario'];        
        if (((rol($idUsuario)) == 'Administrador') or ((rol($idUsuario)) == 'Gestion'))  {  
                     
            $envios= new Envio();
            $configuracion = new Configuracion();
    		    
            switch ($_GET['action']) {

    		    	case 'verEnvio':
                $envios=$envios->mostrarPedidos();
                $variables = array(
                  "usuario" => $_SESSION['usuario'],
                  "titulo" => "ENVIOS",
                  "envios"=>$envios);
                if (rol($idUsuario) == 'Administrador'){
                    echo $twig->render('verEnvios.html.twig', $variables);
                }else{
                  echo $twig->render('verEnvioGestion.html.twig', $variables);
                }

                break;

                
              case 'buscar':
                    $fecha= $_POST['fecha'];
                    $envios=$envios-> buscarTurnos($fecha);
                    $variables = array(
                        "usuario" => $_SESSION['usuario'],
                        "titulo" => "LISTADO ENVIOS",
                        "envios"=>$envios);
                     if (rol($idUsuario) == 'Administrador'){
                    echo $twig->render('verEnvios.html.twig', $variables);
                }else{
                  echo $twig->render('verEnvioGestion.html.twig', $variables);
                }
              break;
                
                  
              case 'verMapa':
                        $id= $_GET['id'];
                $entidad= $envios->mostrarInfoEntidad($id);
                $latitudBanco= $configuracion->obtenerClaveValor('latitud'); 
                $longitudBanco= $configuracion-> obtenerClaveValor('longitud');
                $latitud= $envios-> latitudEntidad($id); 
                $longitud= $envios-> longitudEntidad($id);
                
                $html = file_get_contents("http://api.openweathermap.org/data/2.5/weather?lat=$latitud&lon=$longitud&units=metric");
                $json = json_decode($html);
                 
                $ciudad = $json->name;
                $maxima = $json->main->temp_max;
                $minima = $json->main->temp_min;
                $presion = $json->main->pressure;
                $humedad = $json->main->humidity;
                $estado_cielo = $json->weather[0]->main;
                $descripcion = $json->weather[0]->description;
               
                $variables= array(
                  "usuario"=>$_SESSION['usuario'],
                  "titulo"=>"RECORRIDO DEL ENVIO",
                  "latitud"=>$latitud,
                  "longitud"=>$longitud,
                  "latitudBanco"=>$latitudBanco,
                  "longitudBanco"=> $longitudBanco,
                  "ciudad"=>$ciudad,
                  "maxima"=> $maxima,
                  "minima"=>$minima,
                  "humedad"=> $humedad,
                  "presion"=>$presion,
                  "estado_cielo"=>$estado_cielo,
                  "descripcion"=>$descripcion);
                echo $twig->render('verRutaMapa.html.twig',$variables);

        
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
