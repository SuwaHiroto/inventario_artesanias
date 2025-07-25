<?php
require_once __DIR__ . '/../Models/HistorialVentasModel.php';

class HistorialVentasController
{
    private $model;

    public function __construct($conexion)
    {
        $this->model = new HistorialVentasModel($conexion);
    }

    public function obtenerVentas()
    {
        header('Content-Type: application/json');

        try {
            $fechaInicio = $_GET['fecha_inicio'] ?? null;
            $fechaFin = $_GET['fecha_fin'] ?? null;
            $codigoProducto = $_GET['codigo'] ?? null;

            $ventas = $this->model->obtenerVentas($fechaInicio, $fechaFin, $codigoProducto);
            echo json_encode(['success' => true, 'ventas' => $ventas]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function obtenerProductos()
    {
        header('Content-Type: application/json');

        try {
            $productos = $this->model->obtenerProductos();
            echo json_encode(['success' => true, 'productos' => $productos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

// Uso del controlador
$conexion = new mysqli("localhost:3307", "root", "72830723", "inventario");
if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Conexión fallida']);
    exit;
}

$controller = new HistorialVentasController($conexion);

// Determinar qué acción ejecutar
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'obtener_productos':
        $controller->obtenerProductos();
        break;
    default:
        $controller->obtenerVentas();
        break;
}

$conexion->close();
