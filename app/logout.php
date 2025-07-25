<?php
// /logout.php (o la ruta donde lo tengas)

// 1. Iniciar la sesión para poder manipularla.
// Es crucial que sea la primera instrucción, sin espacios ni texto antes.
session_start();

// 2. Vaciar el array $_SESSION.
// Esto elimina inmediatamente todas las variables de sesión (id_usuario, rol_usuario, etc.).
$_SESSION = array();

// 3. Invalidar la cookie de sesión del navegador.
// Esta es la parte que le faltaba a tu script. Le dice al navegador
// que "olvide" la sesión por completo.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Finalmente, destruir la sesión en el servidor.
// Esto elimina el archivo de sesión del servidor.
session_destroy();

// 5. Redirigir al usuario a la página de login.
header("Location: /app/Views/Login/login.php");

// 6. Detener la ejecución para asegurar que no se procese más código.
exit();
