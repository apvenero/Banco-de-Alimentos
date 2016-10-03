<?php

   
    require_once ("twig.php");
	 require_once ("models/Configuracion.php");
	 
	 	$configuracion = new Configuracion();
    		switch ($_GET['action']) { 
    			
    			
    			case 'linkedin':
                   
    				$obtenerCpr=$configuracion->obtenerClaveValor('idClavePrivada');
                    $cpri = $obtenerCpr;
    				$obtenerCpu=$configuracion->obtenerClaveValor('idClavePublica');
    				$cpu = $obtenerCpu;
    			   	$oauth = new OAuth($cpu, $cpri);
    			   	$oauth->setToken("30261f7b-2c97-482a-a07d-86f0fe34fc32", "deceb593-3f77-4239-b4e4-4e7df82bcd69");
 
					$params = array();
					$headers = array();
					$method = OAUTH_HTTP_METHOD_GET;
					// Specify LinkedIn API endpoint to retrieve your own profile
					$url = "http://api.linkedin.com/v1/people/~:(first-name,last-name,headline,picture-url,main-address,phone-numbers,summary,email-address)?format=json";

					//"https://api.linkedin.com/v1/people/~";
 
					// By default, the LinkedIn API responses are in XML format. If you prefer JSON, simply specify the format in your call
					// $url = "https://api.linkedin.com/v1/people/~?format=json";
 
					// Make call to LinkedIn to retrieve your own profile
					$oauth->fetch($url, $params, $method, $headers);
  					
  					$datos = json_decode($oauth->getLastResponse());
					
					$variables = array(
                        "titulo" => "LINKEDIN",
                        "datos"=> $datos
					);
                    echo $twig->render('linkedin.html.twig', $variables);

    			   	break;   

    		}
    	
    
?>  
