<?php
class ReportesModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function obtenerDatosReporte($tipo)
    {
        switch ($tipo) {
            case 'ventas':
                // ... (tu caso 'ventas' se mantiene igual)
                $sql = "SELECT 
                            DATE_FORMAT(v.fecha, '%d/%m/%Y %H:%i:%s') AS fecha,
                            p.nombre AS producto,
                            dv.cantidad AS cantidad,
                            dv.precio_unitario AS precio_unitario,
                            (dv.cantidad * dv.precio_unitario) AS total
                        FROM venta v
                        JOIN detalle_venta dv ON dv.id_venta = v.id
                        JOIN producto p ON dv.id_producto = p.id
                        WHERE p.estado = 'activo'
                        ORDER BY v.fecha DESC
                        LIMIT 20";
                break;
            case 'top':
                // ... (tu caso 'top' se mantiene igual)
                $sql = "SELECT 
                            p.nombre AS nombre,
                            SUM(dv.cantidad) AS vendidos
                        FROM detalle_venta dv
                        JOIN producto p ON dv.id_producto = p.id
                        WHERE p.estado = 'activo'
                        GROUP BY p.id
                        ORDER BY vendidos DESC
                        LIMIT 10";
                break;
            case 'stock':
                // ... (tu caso 'stock' se mantiene igual)
                $sql = "SELECT nombre, stock 
                        FROM producto
                        WHERE estado = 'activo' AND stock <= 15
                        ORDER BY stock ASC";
                break;

            // ----- CAMBIOS APLICADOS AQUÍ ABAJO -----
            case 'general':
            default:
                // 1. La consulta ahora calcula el 'valor_inventario' por producto.
                // 2. Formateamos el precio para la vista.
                // 3. Dejamos el precio y stock como números para poder sumarlos después.
                $sql = "SELECT 
                            nombre, 
                            precio,
                            stock,
                            (precio * stock) AS valor_inventario 
                        FROM producto
                        WHERE estado = 'activo'
                        ORDER BY nombre ASC";
                break;
        }

        $result = $this->conn->query($sql);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
