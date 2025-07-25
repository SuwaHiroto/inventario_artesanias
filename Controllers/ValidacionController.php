<?php
// /app/Controllers/ValidacionLoginController.php (VERSIÓN FINAL)

// 1. INICIAR LA SESIÓN. DEBE SER LA PRIMERA INSTRUCCIÓN.
// No puede haber NADA antes, ni espacios, ni líneas en blanco, ni HTML.
session_start();

// 2. CONEXIÓN A LA BASE DE DATOS
$host = "localhost:3307";
$usuarioBD = "root";
$contrasenaBD = "72830723";
$base_datos = "inventario";

// Usamos el manejo de errores moderno de mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $usuarioBD, $contrasenaBD, $base_datos);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Si la conexión falla, redirigimos con un error genérico
    header("Location: /app/Views/Login/login.php?error=db_conn");
    exit();
}

// 3. PROCESAMIENTO DEL FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validamos que los datos POST existen
    if (!isset($_POST['usuario']) || !isset($_POST['contrasena'])) {
        header("Location: /app/Views/Login/login.php?error=data");
        exit();
    }

    $usuario_form = $_POST['usuario'];
    $contrasena_form = $_POST['contrasena'];

    // 4. CONSULTA SEGURA CON SENTENCIAS PREPARADAS
    $sql = "SELECT id, nombre, user, contrasena, rol FROM usuario WHERE user = ? AND estado = 1";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario_form);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario_db = $resultado->fetch_assoc();

            // 5. VERIFICACIÓN DE CONTRASEÑA
            if (password_verify($contrasena_form, $usuario_db['contrasena'])) {
                // ÉXITO: Regeneramos el ID de la sesión por seguridad
                session_regenerate_id(true);

                // Guardamos los datos en la sesión
                $_SESSION['id_usuario'] = $usuario_db['id'];
                $_SESSION['nombre_usuario'] = $usuario_db['nombre'];
                $_SESSION['rol_usuario'] = $usuario_db['rol'];

                // 6. REDIRECCIÓN A LA PÁGINA DE INICIO
                header("Location: /app/Views/layouts/inicio.php");
                $stmt->close();
                $conn->close();
                exit();
            } else {
                // La contraseña no coincide
                header("Location: /app/Views/Login/login.php?error=credenciales");
                exit();
            }
        } else {
            // El usuario no existe o está inactivo
            header("Location: /app/Views/Login/login.php?error=credenciales");
            exit();
        }
    } catch (Exception $e) {
        // En caso de un error en la consulta
        header("Location: /app/Views/Login/login.php?error=db_query");
        exit();
    }
} else {
    // Si alguien intenta acceder al script sin enviar datos
    header("Location: /app/Views/Login/login.php");
    exit();
}
