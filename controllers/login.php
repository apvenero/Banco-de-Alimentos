<?php	
   
    require_once('twig.php');
	require_once('models/login.php');
	require_once('models/Usuario.php');
	
	global $rol;
	global $idUsuario;
	
	$error = false;

		if($_POST) {	
		$usuario = $_POST['usuario'];
		$pass = $_POST['pass'];
			
		$error = !login($usuario,$pass);
		
		if (!$error) {
			//inicio la sesion
			$_SESSION['usuario'] = $usuario;
			$_SESSION['idUsuario'] = $idUsuario;
			decidirDestino($idUsuario,$usuario,$twig);	
		
		}else{
			
            $template = $twig->loadTemplate("login.html.twig");
			$template ->display (array('error' => "Usuario o contraseña incorrectos"));

			}
		}else {
            $template = $twig->loadTemplate("login.html.twig");
			$template ->display (array( 'error' => $error));

		}


	function decidirDestino($idUsuario,$usuario,$twig){
		
		$rol = rol($idUsuario);
		if ($rol == 'Administrador') {
			header('Location: index.php?controller=AlertasController&action=listado');
				
			//$template = $twig->loadTemplate("headerAdministrador.html.twig");
			//$template ->display (array('titulo' => "INICIO", 'usuario' => $usuario));
			
		}
		elseif ($rol == 'Gestion') {
     		$template = $twig->loadTemplate("headerGestion.html.twig");
			$template ->display (array('titulo' => "INICIO", 'usuario' => $usuario));
     		
		}
		elseif ($rol == 'Consulta') {
 	        $template = $twig->loadTemplate("headerConsulta.html.twig");
			$template ->display (array('titulo' => "INICIO", 'usuario' => $usuario));
 	        
		}
		else {
			$template = $twig->loadTemplate("login.html.twig");
			$template ->display (array('error' => "Usuarios"));
	}
	
	}



?>
