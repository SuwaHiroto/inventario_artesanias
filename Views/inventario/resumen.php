<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen - Inventario</title>
    <link rel="stylesheet" href="/public/css/resumen.css">
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $esAdmin = isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador';
    include '../layouts/barra-lateral.php';
    ?>
    <main class="contenedor">
        <section class="header" role="banner">
            <h1>Resumen de Inventario</h1>
        </section>
        <div class="container">
            <div class="dashboard">


                <div class="card">
                    <h3>Más Vendidos del Mes</h3>
                    <ul id="lista-productos">
                        <!-- Datos cargados por JavaScript -->
                    </ul>
                </div>

                <div class="card">
                    <h3>Ventas por Temporada</h3>
                    <canvas id="graficoVentas" width="400" height="200"></canvas>
                </div>

                <div class="card">
                    <h3>Productos en Stock</h3>
                    <canvas id="graficoProductos" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script src="/public/js/resumen.js" defer></script>
</body>

</html>