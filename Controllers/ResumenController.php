<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../Models/ResumenModel.php';

try {
    // 1. Conexión a la base de datos
    $database = new Conexion();
    $db = $database->getConnection();

    // 2. Instanciar el modelo
    $model = new ResumenModel($db);

    // 3. Obtener datos
    $response = [
        'productos_mas_vendidos' => $model->getProductosMasVendidos(),
        'ventas_por_trimestre' => $model->getVentasPorTrimestre(),
        'stock_por_trimestre' => $model->getStockPorTrimestre()
    ];

    // 4. Verificar si hay datos
    if (
        empty($response['productos_mas_vendidos']) &&
        empty($response['ventas_por_trimestre']) &&
        empty($response['stock_por_trimestre'])
    ) {
        throw new Exception("No se encontraron datos en la base de datos");
    }

    echo json_encode($response);
} catch (Exception $e) {
    // Registrar el error en un archivo log
    error_log($e->getMessage());

    // Enviar respuesta de error detallada
    echo json_encode([
        'error' => 'Error al cargar datos: ' . $e->getMessage(),
        'detalle' => 'Verifica la conexión a la base de datos y los datos existentes'
    ]);
}
?>