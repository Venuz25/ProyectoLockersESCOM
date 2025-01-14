<?php
    session_start();
    include("fpdf/fpdf.php");
    include('../conexion.php');

    // Verifica la conexión
    if (!$conn) {
        die('Error: No se pudo conectar a la base de datos.');
    }

    // Verificar si la sesión
    if (!isset($_SESSION['boleta']) || !isset($_SESSION['usuario'])) {
        die('Error: No se encontraron los datos necesarios en la sesión.');
    }

    class PDF extends FPDF {
        // Cabecera del documento
        function header() {
            $this->Image('../../img/acuse/encabezado.png', 10, 10, 190); // (x, y, ancho)
            $this->Ln(30); // Espaciado hacia abajo
        }
    }
    //consulta y variables
    $boleta = $_SESSION['boleta'];
    $sql = "SELECT 
                a.nombre,
                a.primerAp,
                a.segundoAp,
                c.noCasillero
            FROM 
                alumnos a 
            INNER JOIN
                casilleros c ON a.boleta = c.boletaAsignada
            WHERE 
                boleta = '$boleta'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombreCompleto = mb_convert_encoding($row['nombre'] . ' ' . $row['primerAp'] . ' ' . $row['segundoAp'], "ISO-8859-1", "UTF-8");
        $casillero = $row['noCasillero'];
    } else {
        $nombreCompleto = mb_convert_encoding('Información no encontrada', "ISO-8859-1", "UTF-8");
        $casillero = 'No disponible';
    }

    // Crear el objeto PDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Título
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 10, mb_convert_encoding('Escuela Superior de Cómputo', "ISO-8859-1", "UTF-8"), 0, 1, 'C');
    $pdf->Ln(10);

    // Cuerpo
    $pdf->SetFont('helvetica', '', 15);
    $pdf->Cell(0, 10, mb_convert_encoding('Período de uso: Semestre 2024-2025/2', "ISO-8859-1", "UTF-8"), 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(50, 15, mb_convert_encoding('No. de Boleta:', "ISO-8859-1", "UTF-8"), 0, 0, 'L');
    $pdf->Cell(40, 15, $boleta, 0, 1, 'L');
    $pdf->Cell(50, 15, mb_convert_encoding('Nombre del estudiante:', "ISO-8859-1", "UTF-8"), 0, 0, 'L');
    $pdf->Cell(40, 15, $nombreCompleto, 0, 1, 'L');
    // Mensaje inicial
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, mb_convert_encoding('Felicidades, tus documentos han sido correctamente recibidos y validados. Nos complace informarte que se te ha asignado el casillero número:', "ISO-8859-1", "UTF-8"), 0, 'L');

    // Número de casillero en negritas
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(4,10,"#", 0, 0, "L");
    $pdf->Cell(0, 10, $casillero, 0, 1);

    // Mensaje restante
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, mb_convert_encoding(
        "El cual podrás utilizar durante el periodo escolar vigente. Te recordamos que el uso del casillero está sujeto a las normativas establecidas por la institución, por lo que te recomendamos hacer un uso responsable del mismo. En caso de cualquier duda o inconveniente, no dudes en comunicarte con el área de control escolar.", 
        "ISO-8859-1", 
        "UTF-8"
    ), 0, 'L');
    
    $pdf->LN(50);
    $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Firma del Estudiante:', 0, 1, "C");
        $pdf->Ln(10);
        $pdf->Cell(0, 10, '____________________________', 0, 1, "C");
        $pdf->Cell(0, 10, mb_convert_encoding($nombreCompleto, "ISO-8859-1", "UTF-8"), 0, 1, "C");

    // Salida del PDF
    try {
        $pdf->Output();
    } catch (Exception $e) {
        die('Error al generar el PDF: ' . $e->getMessage());
    }
?>
