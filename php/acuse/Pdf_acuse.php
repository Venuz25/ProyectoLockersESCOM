<?php
    include("fpdf/fpdf.php");

    class PDF extends FPDF{
        //Cabecera de mi documento
        function header(){
            //Agregamos un banner, imagen o logo
            $this->Image('escudo_ESCOM.png',10,10,30); //(Arriba, abajo, tamaño)
            $this->Image('logotipo_ipn.png',160,10,40); //(Arriba, abajo, tamaño)
            $this->Ln(20); //Alineación texto en vertical (- hacia arriba, + hacia abajo)
        }

        //Pie de página
        function footer(){
            $this->SetY(-20);
            $this->SetFont('helvetica','I','10');
            //Creamos nuestro pie de página
            $this->Cell(0,15,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    //Creación del objeto de la clase heredada
    $pdf = new PDF();
    /*Define un alias para el número total de páginas. 
    el total de las páginas vd={nb}*/
    $pdf->AliasNBPages();
    $pdf->AddPage();
    $pdf->SetFont('helvetica','B',15);
    $pdf->Cell(30,20,'Felicidades',0,0,1);
    $pdf->Cell(40,20,'Luis Miguel',0,1,1);
    $pdf->Cell(63,15,'Tu numero de boleta es: ',0,0,1);
    $pdf->Cell(40,15,'2023630300',0,1,1);
    $pdf->Cell(85,15,'Se te ha asignado el casillero No.',0,0,1);
    $pdf->Cell(40,15,'173',0,1,1);
    $pdf->Cell(68,15,'Durante el periodo escolar:',0,1,1);
    $pdf->Cell(57,15,'Semestre 2024-2025/2',0,0,1);
    $pdf->Cell(40,15,'(febrero - agosto)',0,1,1);
    $pdf->Cell(85,15,'Tus documentos ya fueron recibidos y validados',0,0,1);
    $pdf->Output();
?>