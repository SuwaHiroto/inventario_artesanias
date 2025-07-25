<?php
require_once __DIR__ . '/../Models/ReportesModel.php';
require_once __DIR__ . '/../Librerias/FPDF/fpdf.php';

class ReportesController
{
    private $model;
    private $conn;

    public function __construct()
    {
        // ... (tu constructor se mantiene igual)
        $this->conn = new mysqli("localhost:3307", "root", "72830723", "inventario");
        $this->conn->set_charset("utf8");
        if ($this->conn->connect_error) {
            throw new Exception("Conexión fallida: " . $this->conn->connect_error);
        }
        $this->model = new ReportesModel($this->conn);
    }

    public function handleRequest()
    {
        // ... (tu handleRequest se mantiene igual)
        $action = $_GET['action'] ?? 'view';
        $tipo = $_GET['tipo'] ?? 'ventas';
        try {
            switch ($action) {
                case 'view':
                    $this->mostrarVista($tipo);
                    break;
                case 'pdf':
                    $this->generarPDF($tipo);
                    break;
                case 'excel':
                    $this->generarExcel($tipo);
                    break;
                default:
                    http_response_code(400);
                    echo "Acción inválida";
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error: " . $e->getMessage();
        } finally {
            $this->conn->close();
        }
    }

    private function mostrarVista($tipo)
    {
        // ... (tu mostrarVista se mantiene igual)
        $datos = $this->model->obtenerDatosReporte($tipo);
        header('Content-Type: application/json');
        echo json_encode($datos);
    }

    private function generarPDF($tipo)
    {
        $datos = $this->model->obtenerDatosReporte($tipo);
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        $this->agregarEncabezadoPDF($pdf);

        $tituloReporte = ($tipo === 'general') ? "Reporte de Inventario" : "Reporte de " . ucfirst($tipo);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(0, 8, utf8_decode($tituloReporte), 0, 1, 'C');
        $pdf->Ln(2);

        // Bloque para el reporte de inventario (ya lo tienes)
        if ($tipo === 'general' && !empty($datos)) {
            $totalStock = 0;
            $valorTotalInventario = 0;
            foreach ($datos as $producto) {
                $totalStock += $producto['stock'];
                $valorTotalInventario += $producto['valor_inventario'];
            }
            $this->generarTablaPDF($pdf, $tipo, $datos, [
                'total_stock' => $totalStock,
                'valor_total_inventario' => $valorTotalInventario
            ]);

            // ----- AÑADE ESTE NUEVO BLOQUE 'ELSEIF' AQUÍ ABAJO -----
        } elseif ($tipo === 'ventas' && !empty($datos)) {
            $totalCantidadVendida = 0;
            $ingresoTotalVentas = 0;

            foreach ($datos as $venta) {
                $totalCantidadVendida += $venta['cantidad'];
                $ingresoTotalVentas += $venta['total'];
            }

            // Pasamos los nuevos totales a la función que dibuja la tabla
            $this->generarTablaPDF($pdf, $tipo, $datos, [
                'total_cantidad' => $totalCantidadVendida,
                'ingreso_total' => $ingresoTotalVentas
            ]);
        } else {
            // Para otros reportes o si no hay datos, se llama como antes.
            $this->generarTablaPDF($pdf, $tipo, $datos);
        }

        $pdf->Output("I", "reporte_{$tipo}.pdf");
        exit;
    }

    private function agregarEncabezadoPDF($pdf)
    {
        // ... (tu agregarEncabezadoPDF se mantiene igual)
        $nombreNegocio = "Artesanías Puma";
        $direccion = "Calle Loreto";
        $fecha = date("d/m/Y H:i");
        $pdf->Cell(0, 5, utf8_decode($nombreNegocio), 0, 1, 'C');
        $pdf->Cell(0, 5, utf8_decode($direccion), 0, 1, 'C');
        $pdf->Cell(0, 5, "Fecha y hora: $fecha", 0, 1, 'C');
        $pdf->Ln(5);
    }

    // ----- CAMBIO: La función ahora acepta un array opcional de totales -----
    private function generarTablaPDF($pdf, $tipo, $datos, $totales = null)
    {
        // Definimos anchos fijos para el reporte de inventario
        $anchos = [
            'ventas' => [35, 35, 40, 30, 30],
            'top'    => [100, 70], // Ajustado para 2 columnas
            'stock'  => [100, 70], // Ajustado para 2 columnas
            'general' => [70, 40, 30, 50] // Nombre, Precio, Stock, Valor Inventario
        ];

        if (empty($datos)) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 10, "No hay datos disponibles.", 1, 1, 'C');
            return; // Salir de la función si no hay datos
        }

        // Definimos las cabeceras manualmente para tener más control
        $cabeceras = [
            'ventas' => ['Fecha', 'Producto', 'Cantidad', 'Precio Unitario', 'Total'],
            'top'    => ['Nombre', 'Unidades Vendidas'],
            'stock'  => ['Nombre', 'Stock Restante'],
            'general' => ['Nombre', 'Precio Unitario', 'Stock', 'Valor del Inventario']
        ];

        $headers = $cabeceras[$tipo] ?? array_keys($datos[0]);
        $anchoCols = $anchos[$tipo] ?? array_fill(0, count($headers), 30);

        // Encabezado de tabla
        $pdf->SetFillColor(201, 60, 60);
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 10);

        foreach ($headers as $i => $h) {
            $pdf->Cell($anchoCols[$i], 8, utf8_decode($h), 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Filas de tabla
        $pdf->SetFillColor(240, 239, 239);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 9);

        foreach ($datos as $row) {
            // ----- CAMBIO: Formateo de datos para el reporte 'general' -----
            if ($tipo === 'general') {
                $pdf->Cell($anchoCols[0], 8, utf8_decode($row['nombre']), 1, 0, 'L', true);
                $pdf->Cell($anchoCols[1], 8, 'S/ ' . number_format($row['precio'], 2), 1, 0, 'R', true);
                $pdf->Cell($anchoCols[2], 8, $row['stock'], 1, 0, 'C', true);
                $pdf->Cell($anchoCols[3], 8, 'S/ ' . number_format($row['valor_inventario'], 2), 1, 0, 'R', true);
            } else { // Lógica original para otros reportes
                $columnas = array_keys($datos[0]);
                foreach ($columnas as $i => $col) {
                    $pdf->Cell($anchoCols[$i], 8, utf8_decode($row[$col]), 1, 0, 'C', true);
                }
            }
            $pdf->Ln();
        }

        // ----- CAMBIO: Añadir la fila de TOTALES al final -----
        if ($tipo === 'general' && $totales !== null) {
            $pdf->SetFont('Arial', 'B', 10);
            // Celda vacía para 'Nombre' y 'Precio'
            $pdf->Cell($anchoCols[0] + $anchoCols[1], 8, 'TOTALES:', 1, 0, 'R', true);
            // Celda para el total de Stock
            $pdf->Cell($anchoCols[2], 8, $totales['total_stock'], 1, 0, 'C', true);
            // Celda para el valor total del inventario
            $pdf->Cell($anchoCols[3], 8, 'S/ ' . number_format($totales['valor_total_inventario'], 2), 1, 0, 'R', true);
            $pdf->Ln();
        }
        // ----- AÑADE ESTE NUEVO BLOQUE 'ELSEIF' JUSTO AQUÍ -----
        elseif ($tipo === 'ventas' && $totales !== null) {
            $pdf->SetFont('Arial', 'B', 10);
            // Celda vacía para 'Fecha' y 'Producto'
            $pdf->Cell($anchos['ventas'][0] + $anchos['ventas'][1], 8, 'TOTALES:', 1, 0, 'R', true);
            // Celda para la cantidad total de artículos vendidos
            $pdf->Cell($anchos['ventas'][2], 8, $totales['total_cantidad'], 1, 0, 'C', true);
            // Celda vacía para 'Precio Unitario'
            $pdf->Cell($anchos['ventas'][3], 8, '', 1, 0, 'C', true);
            // Celda para el ingreso total de las ventas
            $pdf->Cell($anchos['ventas'][4], 8, 'S/ ' . number_format($totales['ingreso_total'], 2), 1, 0, 'R', true);
            $pdf->Ln();
        }
    }
    private function generarExcel($tipo)
    {
        $datos = $this->model->obtenerDatosReporte($tipo);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=reporte_{$tipo}.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        if (count($datos) > 0) {
            echo "<tr>";
            foreach (array_keys($datos[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            foreach ($datos as $row) {
                echo "<tr>";
                foreach ($row as $col) {
                    echo "<td>" . htmlspecialchars($col) . "</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Sin datos</td></tr>";
        }
        echo "</table>";
        exit;
    }
}

// Uso del controlador
try {
    $controller = new ReportesController();
    $controller->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
