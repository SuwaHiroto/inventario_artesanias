<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../Models/VentasModel.php';

// Configuración de conexión
$conexion = new mysqli("localhost:3307", "root", "72830723", "inventario");
if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB error: ' . $conexion->connect_error]);
    exit;
}

try {
    $modelo = new VentasModel($conexion);
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'obtener_productos':
            // Endpoint: GET /VentaController.php?action=obtener_productos
            $productos = $modelo->obtenerProductosDisponibles();
            echo json_encode(['success' => true, 'productos' => $productos]);
            break;

        case 'registrar_venta':
            // Endpoint: POST /VentaController.php?action=registrar_venta
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método no permitido']);
                break;
            }

            $datos = json_decode(file_get_contents('php://input'), true);
            if (!is_array($datos)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
                break;
            }

            // Calcular total
            $total = array_reduce($datos, function ($sum, $item) {
                return $sum + ($item['cantidad'] * $item['precio']);
            }, 0);

            $idVenta = $modelo->registrarVenta($total, $datos);
            echo json_encode(['success' => true, 'id_venta' => $idVenta]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Acción no especificada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conexion->close();
}
