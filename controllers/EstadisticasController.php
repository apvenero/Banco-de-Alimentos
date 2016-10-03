<?php    
       
    require_once ('./models/login.php');
    require_once ("models/EntidadReceptora.php");
    require_once ('./models/Estadisticas.php');
    require_once ("twig.php");

    if (isset($_SESSION['idUsuario'])) {
        
        $idUsuario = $_SESSION['idUsuario']; 
        if (((rol($idUsuario)) == 'Administrador') or ((rol($idUsuario)) == 'Gestion'))  { 
            $modeloEst= new Estadisticas();
            $modeloEntidad = new EntidadReceptora();

            switch ($_GET['action']) {
                case 'impresiones':                       
                    $entidades= $modeloEntidad->mostrarEntidades();
                    $alimentos=$modeloEst->mostrarAlimentosVencidos();

                    $variables = array(
                        "titulo"=> "ESTADISTICAS",
                        "usuario" => $_SESSION['usuario'],
                        "alimentos" =>$alimentos,
                        "entidades"=> $entidades);
                    if (rol($idUsuario) == 'Administrador'){
                   echo $twig->render('estadisticas.html.twig', $variables);
                }else{
                   echo $twig->render('estadisticasGestion.html.twig', $variables);
                }

                   
                break;

 
                case 'estadisticasA':
                    function test_input($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }

                    $errores = array();

                    if ($_POST['fechaInicial'] == ''){
                        $errores['fechaInicial'] = 'Falta ingresar la fecha inicial';
                    }else{
                        $fechaInicial = test_input($_POST['fechaInicial']);
                    }

                    if ($_POST['fechaFinal'] == ''){
                        $errores['fechaFinal'] = 'Falta ingresar la fecha final';
                    }else{
                        $fechaFinal = test_input($_POST['fechaFinal']); 
                    }

                    $variables = array("titulo" => "","errores" =>$errores);
                  
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                         if (rol($idUsuario) == 'Administrador'){
                   echo $twig->render('estadisticas.html.twig', $variables);
                }else{
                   echo $twig->render('estadisticasGestion.html.twig', $variables);
                }
                    
                    } else {

                        $pedidos= $modeloEst ->pedidosEntreFechas($fechaInicial, $fechaFinal); 
                        $i=0;
                        foreach ($pedidos as $p):
                            $arreglo[$i]= $modeloEst -> cantKilosPedido($p['numero']); 
                            $i++;
                        endforeach;   
                        if(isset($_POST['boton1'])){
                            $variables = array("titulo"=> "ESTADISTICAS",
                                                "usuario" => $_SESSION['usuario'],
                                                "pedidos"=>$pedidos,
                                                "arreglo" => $arreglo,
                                                "fechaInicial"=>$fechaInicial,
                                                "fechaFinal"=>$fechaFinal);
                             if (rol($idUsuario) == 'Administrador'){
                               echo $twig->render('graficoBarra.html.twig', $variables);
                            }else{
                             echo $twig->render('graficoBarraGestion.html.twig', $variables);
                  }
                            
                        }
                        if(isset($_POST['boton2'])){
                        
                            require_once('./lib/fpdf/pdf.php');
                            $pdf = new PDF();
                             
                            $pdf->AddPage();

                            $miCabecera = array('Numero Pedido', 'Kilos entregados');
                              foreach ($arreglo as $a) { 
                                  $misDatos[$i]= array( $a[0]['pedido_numero'], $a[0]['totalKilos']);
                                $i++;
                             } 
                            $pdf->SetFont('Arial','',14);
                            $pdf->BasicTable($miCabecera,$misDatos);
                            $pdf->Output('listado.pdf', 'D');
             
                    }}
                break;

                case 'verGraficoTorta':
                    
                   function test_input($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }

                    $errores = array();

                    if($_POST['entidad_receptora_id'] == ''){
                        $errores['entidad_receptora_id'] = 'Falta ingresar la razon social de la entidad';
                    }else{
                        $entidad_receptora_id = test_input($_POST['entidad_receptora_id']);
                    }


                    if ($_POST['fechaInicial'] == ''){
                        $errores['fechaInicial'] = 'Falta ingresar la fecha inicial';
                    }else{
                        $fechaInicial = test_input($_POST['fechaInicial']);
                    }

                    if ($_POST['fechaFinal'] == ''){
                        $errores['fechaFinal'] = 'Falta ingresar la fecha final';
                    }else{
                        $fechaFinal = test_input($_POST['fechaFinal']); 
                    }

                    $variables = array("titulo" => "","errores" =>$errores);
                  
                    if (count($errores) > 0) {
                        $variables['errores'] = $errores;
                        if (rol($idUsuario) == 'Administrador'){
                        echo $twig->render('estadisticas.html.twig', $variables);
                            }else{
                                echo $twig->render('estadisticasGestion.html.twig', $variables);
                            }
            
                    } else {
                        $pedidos= $modeloEst -> pedidosEntidad($fechaInicial, $fechaFinal,$entidad_receptora_id); 
                       
                        if(isset($_POST['boton1'])){
                            $variables = array("titulo"=> "ESTADISTICAS",
                                                "usuario" => $_SESSION['usuario'],
                                                "pedidos"=>$pedidos,
                                                "fechaInicial"=>$fechaInicial,
                                                "fechaFinal"=>$fechaFinal,
                                                "entidad_receptora_id"=>$entidad_receptora_id);
                            if (rol($idUsuario) == 'Administrador'){
                                 echo $twig->render('graficoTorta.html.twig', $variables);
                            }else{
                                echo $twig->render('graficoTortaGestion.html.twig', $variables);
                            }
                        }
                        if(isset($_POST['boton2'])){
                        
                            require_once('./lib/fpdf/pdf.php');
                            $pdf = new PDF();
                             
                            $pdf->AddPage();

                            $miCabecera = array('Descripcion', 'Contenido', 'Total');
                            $i=0;
                            foreach ($pedidos as $p) { 
                                  $misDatos[$i]= array( $p['descripcion'], $p['contenido'], $p['kgs']);
                                $i++;
                            } 
                            $pdf->SetFont('Arial','',14);
                            $pdf->BasicTable($miCabecera,$misDatos);
                            $pdf->Output('listado.pdf', 'D');
             

                    }}
                break;

                case 'vencidos':
                    
                    require_once('./lib/fpdf/pdf.php');
                    $alimentos=$modeloEst->mostrarAlimentosVencidos();

                    $pdf = new PDF();
                             
                    $pdf->AddPage();
                    $miCabecera = array('Codigo', 'Descripcion','Vencimiento','Paquetes vencidos');
                    $i=0;
                    foreach ($alimentos as $a) { 
                        $misDatos[$i]= array( $a['codigo'], $a['descripcion'],$a['fecha_vencimiento'],$a['stock']);
                        $i++;
                    } 
                    $pdf->SetFont('Arial','',14);
                    $pdf->BasicTable($miCabecera,$misDatos);
                    $pdf->Output('listado.pdf', 'D');
             
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

