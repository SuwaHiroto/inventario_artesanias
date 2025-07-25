<?php

class BaseController {
    protected $db;
    protected $view;

    public function __construct() {
        // Inicializar la conexión a la base de datos
        require_once __DIR__ . '/../config/Conexion.php';
        $this->db = $config;
        
        // Método para cargar vistas
        $this->view = function($view, $data = []) {
            extract($data);
            require_once __DIR__ . '/../Views/' . $view . '.php';
        };
    }

    // Método para redireccionar
    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
    }
} 