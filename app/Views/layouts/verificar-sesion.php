<?php
// /app/Views/layouts/verificar_sesion.php

// Si la sesión no se ha iniciado, la iniciamos.
// Esto es vital para poder leer el array $_SESSION.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LA VERIFICACIÓN DE SEGURIDAD CLAVE:
// Comprobamos si la variable 'id_usuario' NO está definida en la sesión.
// Esta es la variable que tú mismo creas en ValidacionController cuando el login es exitoso.
if (!isset($_SESSION['id_usuario'])) {

    // Si no existe, significa que el usuario NO ha iniciado sesión.
    // Destruimos cualquier posible residuo de sesión por seguridad.
    session_destroy();

    // Redirigimos al usuario a la página de login.
    header("Location: /app/Views/Login/login.php?error=acceso_denegado");

    // MUY IMPORTANTE: Detenemos la ejecución del script.
    // Sin exit(), el resto de la página protegida se seguiría mostrando.
    exit();
}

// Si el script llega hasta aquí, significa que la sesión es válida y el usuario
// tiene permiso para ver la página. No se necesita hacer nada más.
