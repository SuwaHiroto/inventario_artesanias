<?php
// /controllers/ProductosController.php

session_start(); // Esencial para leer el rol del usuario

header('Content-Type: application/json');
require_once __DIR__ . '/../Models/ProductoModel.php';
require_once __DIR__ . '/../config/Conexion.php'; // Asumiendo que esta es tu conexión

try {
    $db = new Conexion();
    $conexion = $db->getConnection();
    $productoModel = new ProductoModel($conexion);

    $accion = $_GET['action'] ?? '';
    $datos = json_decode(file_get_contents("php://input"), true);

    // --- FUNCIÓN DE AYUDA PARA VERIFICAR PERMISOS ---
    function esAdmin()
    {
        return isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador';
    }

    switch ($accion) {
        // --- ACCIONES PÚBLICAS (TODOS PUEDEN VER Y BUSCAR) ---
        case 'listar':
            echo json_encode(["estado" => "ok", "productos" => $productoModel->listarActivos()]);
            break;

        case 'listarInactivos':
            // Opcional: Podrías restringir esto si no quieres que los empleados vean inactivos.
            echo json_encode(["estado" => "ok", "productos" => $productoModel->listarInactivos()]);
            break;

        case 'buscar':
            $q = $_GET['q'] ?? '';
            $productos = $productoModel->buscar($q, isset($_GET['inactivos']));
            echo json_encode(["estado" => "ok", "productos" => $productos]);
            break;

        case 'categorias':
            require_once __DIR__ . '/../Models/CategoriasModel.php';
            $catModel = new CategoriasModel($conexion);
            echo json_encode(["estado" => "ok", "categorias" => $catModel->obtenerTodas()]);
            break;

        // --- ACCIÓN SEMI-PÚBLICA (TODOS PUEDEN ACTUALIZAR STOCK) ---
        case 'actualizarStock':
            // No se requiere ser admin para esta acción específica.
            if ($productoModel->actualizarStock($datos['id'], $datos['stock'])) {
                echo json_encode(["estado" => "ok", "mensaje" => "Stock actualizado correctamente"]);
            } else {
                throw new Exception("Error al actualizar el stock");
            }
            break;

        // --- ACCIONES RESTRINGIDAS (SOLO ADMIN) ---
        case 'reactivar':
            if (!esAdmin()) throw new Exception("Acceso denegado. Permisos insuficientes.");
            if ($productoModel->reactivar($datos['id'])) {
                echo json_encode(["estado" => "ok", "mensaje" => "Producto reactivado correctamente"]);
            } else {
                throw new Exception("Error al reactivar");
            }
            break;

        case 'registrar':
            if (!esAdmin()) throw new Exception("Acceso denegado. Permisos insuficientes.");
            if ($productoModel->registrar($datos)) {
                echo json_encode(["estado" => "ok"]);
            } else {
                throw new Exception("Error al registrar");
            }
            break;

        case 'editar':
            if (!esAdmin()) throw new Exception("Acceso denegado. Permisos insuficientes.");
            if ($productoModel->editar($datos['id'], $datos)) {
                echo json_encode(["estado" => "ok"]);
            } else {
                throw new Exception("Error al editar");
            }
            break;

        case 'eliminar':
            if (!esAdmin()) throw new Exception("Acceso denegado. Permisos insuficientes.");
            if ($productoModel->eliminar($datos['id'])) {
                echo json_encode(["estado" => "ok", "mensaje" => "Producto dado de baja correctamente"]);
            } else {
                throw new Exception("Error al dar de baja");
            }
            break;

        default:
            throw new Exception("Acción no válida");
    }
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Acceso denegado') !== false) {
        http_response_code(403);
    } else {
        http_response_code(400);
    }
    echo json_encode(["estado" => "error", "mensaje" => $e->getMessage()]);
}
