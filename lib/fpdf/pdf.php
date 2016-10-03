<?php
     require_once ('./lib/fpdf/fpdf.php');
 
class PDF extends FPDF
{

    function cabeceraHorizontal($cabecera)
    {
        $this->SetXY(30, 10);
        $this->SetFont('Arial','B',16);
        foreach($cabecera as $fila)
        {
            //Atención!! el parámetro valor 0, hace que sea horizontal
            $this->Cell(50,7, utf8_decode($fila),1, 0 , 'L' );
        }
    }
 
    function datosHorizontal($datos)
    {
        $this->SetXY(30,10); // 77 = 70 posiciónY_anterior + 7 altura de las de cabecera
        $this->SetFont('Arial','',14); //Fuente, normal, tamaño
        foreach($datos as $fila)
        {
            //Atención!! el parámetro valor 0, hace que sea horizontal
            $this->Cell(50,20, utf8_decode($fila),1, 0 , 'L' );
        }
    }

      function tablaHorizontal($cabeceraHorizontal, $datosHorizontal)
    {
        $this->cabeceraHorizontal($cabeceraHorizontal);
        $this->datosHorizontal($datosHorizontal);
    }
    function BasicTable($header, $data)
{
    // Cabecera
    foreach($header as $col)
        $this->Cell(45,7,$col,1);
    $this->Ln();
    // Datos
    foreach($data as $row)
    {
        foreach($row as $col)
            $this->Cell(45,6,$col,1);
        $this->Ln();
    }
}



 
} // FIN Class PDF
?>