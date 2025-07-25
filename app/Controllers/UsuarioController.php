<?php
// /controllers/UsuarioController.php

ini_set('display_errors', 0); // Desactivar en producción para no mostrar errores HTML
error_reporting(E_ALL);
ob_start();
header('Content-Type: application/json');

session_start();
var_dump($_SESSION); // <-- AÑADE ESTA LÍNEA TEMPORALMENTE


$modelo_path = @include_once __DIR__ . '/../Models/UsuarioModel.php';
$config_path = @include_once __DIR__ . '/../config/Conexion.php'; // Asegúrate que esta ruta es correcta

if (!$modelo_path || !$config_path) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error Crítico: Faltan archivos internos del servidor.']);
    exit();
}

class UsuarioController
{
    private $modelo;
    private $conexion;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->conexion = new mysqli("localhost:3307", "root", "72830723", "inventario");
            $this->conexion->set_charset("utf8mb4");
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
            exit();
        }
        $this->modelo = new UsuarioModel($this->conexion);
    }

    public function index()
    {
        if ($this->esAdmin()) {
            ob_end_clean();
            require_once __DIR__ . '/../Views/Usuario/usuario.php';
        } else {
            http_response_code(403);
            ob_end_clean();
            echo "<h1>Acceso Denegado</h1><p>No tienes permiso para acceder a esta página.</p>";
        }
        exit();
    }

    public function listar()
    {
        if (!$this->esAdmin()) {
            $this->responderError(403, 'Acceso denegado');
            return;
        }
        try {
            $this->responderExito($this->modelo->obtenerTodosLosUsuarios());
        } catch (Exception $e) {
            $this->responderError(500, $e->getMessage());
        }
    }

    public function obtener($id)
    {
        if (!$this->esAdmin()) {
            $this->responderError(403, 'Acceso denegado');
            return;
        }
        try {
            $usuario = $this->modelo->obtenerUsuarioPorId($id);
            if (!$usuario) throw new Exception("Usuario no encontrado.");
            $this->responderExito($usuario);
        } catch (Exception $e) {
            $this->responderError(404, $e->getMessage());
        }
    }

    public function registrar()
    {
        if (!$this->esAdmin()) {
            $this->responderError(403, 'Acceso denegado');
            return;
        }
        $datos = $_POST;
        if (empty($datos['nombre']) || empty($datos['apellidos']) || empty($datos['user']) || empty($datos['contrasena']) || empty($datos['rol'])) {
            $this->responderError(400, 'Todos los campos son obligatorios.');
            return;
        }
        if (!empty($datos['telefono']) && (!ctype_digit($datos['telefono']) || strlen($datos['telefono']) > 9)) {
            $this->responderError(400, 'El teléfono debe contener solo números y tener máximo 9 dígitos.');
            return;
        }
        try {
            $nuevoId = $this->modelo->registrarNuevoUsuario($datos);
            $this->responderExito(['id' => $nuevoId, 'mensaje' => 'Usuario creado con éxito.']);
        } catch (Exception $e) {
            $this->responderError(400, $e->getMessage());
        }
    }

    public function actualizar()
    {
        if (!$this->esAdmin()) {
            $this->responderError(403, 'Acceso denegado');
            return;
        }
        $datos = $_POST;
        if (empty($datos['id']) || empty($datos['nombre']) || empty($datos['apellidos']) || empty($datos['user']) || empty($datos['rol'])) {
            $this->responderError(400, 'Faltan datos obligatorios.');
            return;
        }
        if (!empty($datos['telefono']) && (!ctype_digit($datos['telefono']) || strlen($datos['telefono']) > 9)) {
            $this->responderError(400, 'El teléfono debe contener solo números y tener máximo 9 dígitos.');
            return;
        }
        try {
            $this->modelo->actualizarUsuario((int)$datos['id'], $datos);
            $this->responderExito(['mensaje' => 'Usuario actualizado con éxito.']);
        } catch (Exception $e) {
            $this->responderError(400, $e->getMessage());
        }
    }

    public function cambiarEstado()
    {
        if (!$this->esAdmin()) {
            $this->responderError(403, 'Acceso denegado');
            return;
        }
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            $this->responderError(400, 'ID de usuario no proporcionado.');
            return;
        }
        try {
            $usuario = $this->modelo->obtenerUsuarioPorId((int)$id);
            if (!$usuario) throw new Exception("Usuario no encontrado.");
            $nuevoEstado = $usuario['estado'] == 1 ? 0 : 1;
            $this->modelo->cambiarEstadoUsuario((int)$id, $nuevoEstado);
            $this->responderExito(['mensaje' => 'Estado del usuario cambiado con éxito.']);
        } catch (Exception $e) {
            $this->responderError(400, $e->getMessage());
        }
    }

    private function esAdmin()
    {
        return isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador';
    }
    private function responderJSON($data)
    {
        ob_end_clean();
        echo json_encode($data);
        exit();
    }
    private function responderExito($datos)
    {
        $this->responderJSON(['success' => true, 'data' => $datos]);
    }
    private function responderError($codigoHttp, $mensaje)
    {
        http_response_code($codigoHttp);
        $this->responderJSON(['success' => false, 'error' => $mensaje]);
    }
    public function __destruct()
    {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}

try {
    $action = $_GET['action'] ?? 'index';
    $controlador = new UsuarioController();

    $rutasValidas = ['index', 'listar', 'obtener', 'registrar', 'actualizar', 'cambiarEstado'];
    if (!in_array($action, $rutasValidas)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => "La acción '$action' no es válida."]);
        exit();
    }

    if ($action === 'index') {
        $controlador->index();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controlador->$action();
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $controlador->$action($id);
    }
} catch (Exception $e) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Error inesperado en el enrutador: ' . $e->getMessage()]);
    exit();
}
