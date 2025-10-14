<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: login.php?err=2');
    exit;
}

require_once 'conexion.php';
require_once __DIR__ . '/fpdf/fpdf.php'; // Asegúrate de tener la librería en /fpdf

class PDF extends FPDF {
    // Encabezado
    function Header() {
        // Logo (si tenés un archivo logo.png en la carpeta)
        if (file_exists('logo.png')) {
            $this->Image('logo.png', 10, 6, 20);
        }
        // Fuente y título
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Listado de Productos - Fabrica BA', 0, 1, 'C');
        $this->Ln(5);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i') . ' - Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Cabecera de tabla
$pdf->SetFillColor(50, 50, 50);
$pdf->SetTextColor(255);
$pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(80, 10, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Costo', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Stock', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0);

// Datos
$res = $conexion->query("SELECT * FROM productos ORDER BY nombre ASC");
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(20, 10, $row['id_producto'], 1);
    $pdf->Cell(80, 10, utf8_decode($row['nombre']), 1);
    $pdf->Cell(40, 10, '$' . number_format($row['costo'], 2, ',', '.'), 1, 0, 'R');
    $pdf->Cell(30, 10, $row['stock'], 1, 1, 'C');
}

$pdf->Output('D', 'productos.pdf');
exit;
