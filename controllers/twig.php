<?php
    require_once('./views/Twig/lib/Twig/Autoloader.php');    
    Twig_Autoloader::register();    
    $templateDir="./views/Twig/Templates";  
    $loader = new Twig_Loader_Filesystem($templateDir);
    $twig = new Twig_Environment($loader);
?>