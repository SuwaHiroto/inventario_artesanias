<?php
function base_url($path = '') {
    // Obtener el protocolo (http o https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    
    // Obtener el nombre del host
    $host = $_SERVER['HTTP_HOST'];
    
    // Obtener el directorio base de la aplicación
    $baseDir = dirname($_SERVER['SCRIPT_NAME']);
    
    // Asegurarse de que el baseDir termina con /
    $baseDir = rtrim($baseDir, '/') . '/';
    
    // Si el path comienza con /, quitarlo
    $path = ltrim($path, '/');
    
    // Construir y retornar la URL completa
    return $protocol . $host . $baseDir . $path;
} 