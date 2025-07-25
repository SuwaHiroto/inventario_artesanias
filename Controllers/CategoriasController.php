<?php
// /controllers/CategoriasController.php

session_start(); // Esencial para leer el rol del usuario

header('Content-Type: application/json');
require_once __DIR__ . '/../Models/CategoriasModel.php';

try {
    // La conexión y el modelo se mantienen igual
    $conexion = new mysqli("localhost:3307", "root", "72830723", "inventario");
    if ($conexion->connect_error) {
        throw new Exception('Error de conexión: ' . $conexion->connect_error);
    }

    $modelo = new CategoriasModel($conexion);
    $action = $_GET['action'] ?? '';

    // --- FUNCIÓN DE AYUDA PARA VERIFICAR PERMISOS ---
    // La creamos para no repetir el mismo código if/else en cada método
    function esAdmin()
    {
        return isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador';
    }

    switch ($action) {
        // --- ACCIONES PÚBLICAS (TODOS PUEDEN VER) ---
        case 'listar':
            $categorias = $modelo->obtenerTodas();
            echo json_encode(['success' => true, 'data' => $categorias]);
            break;

        case 'buscar':
            $termino = $_GET['q'] ?? '';
            $resultados = $modelo->buscar($termino);
            echo json_encode(['success' => true, 'data' => $resultados]);
            break;

        // --- ACCIONES RESTRINGIDAS (SOLO ADMIN) ---
        case 'crear':
            if (!esAdmin()) throw new Exception('Acceso denegado. Permisos insuficientes.');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido');

            $datos = json_decode(file_get_contents('php://input'), true);
            $id = $modelo->crear($datos['nombre'], $datos['descripcion']);
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'actualizar':
            if (!esAdmin()) throw new Exception('Acceso denegado. Permisos insuficientes.');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido');

            $datos = json_decode(file_get_contents('php://input'), true);
            $filas = $modelo->actualizar($datos['id'], $datos['nombre'], $datos['descripcion']);
            echo json_encode(['success' => true, 'affected_rows' => $filas]);
            break;

        case 'eliminar':
            if (!esAdmin()) throw new Exception('Acceso denegado. Permisos insuficientes.');
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido');

            $datos = json_decode(file_get_contents('php://input'), true);
            $filas = $modelo->eliminar($datos['id']);
            echo json_encode(['success' => true, 'affected_rows' => $filas]);
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    // Si el error es por "Acceso denegado", usamos el código de error 403 (Prohibido)
    if ($e->getMessage() === 'Acceso denegado. Permisos insuficientes.') {
        http_response_code(403);
    } else {
        http_response_code(500);
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}
