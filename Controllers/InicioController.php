<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../Models/InicioModel.php';

class InicioController
{
    private $model;
    private $conexion;

    public function __construct()
    {
        $this->conexion = new mysqli("localhost:3307", "root", "72830723", "inventario");

        if ($this->conexion->connect_error) {
            echo json_encode(['error' => 'Conexión fallida']);
            exit;
        }

        $this->model = new InicioModel($this->conexion);
    }

    public function obtenerResumen()
    {
        try {
            $resumen = $this->model->obtenerResumenDashboard();
            echo json_encode($resumen);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        } finally {
            $this->conexion->close();
        }
    }
}

// Uso del controlador
$controller = new InicioController();
$controller->obtenerResumen();
