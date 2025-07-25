<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reportes de Inventario</title>
  <link rel="stylesheet" href="/public/css/reportes.css">
  <link rel="stylesheet" href="/public/css/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      <h1>Reportes del Inventario</h1>
    </section>

    <select id="tipoReporte">
      <option value="general">Todos los Productos</option>
      <option value="ventas">Últimas Ventas</option>
      <option value="top">Productos Más Vendidos</option>
      <option value="stock">Stock Bajo</option>
    </select>

    <button onclick="generarReporte()">Generar</button>
    <button onclick="exportarPDF()">Exportar PDF</button>
    <button onclick="exportarExcel()">Exportar Excel</button>

    <table id="tablaReportes" class="table table-striped table-bordered align-middle">
      <thead></thead>
      <tbody></tbody>
    </table>

  </main>
  <script src="/public/js/reportes.js"></script>
</body>

</html>