<?php
class ResumenModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getProductosMasVendidos()
    {
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido 
               FROM detalle_venta dv
               JOIN producto p ON dv.id_producto = p.id
               JOIN venta v ON dv.id_venta = v.id
               WHERE MONTH(v.fecha) = MONTH(CURRENT_DATE())
               AND YEAR(v.fecha) = YEAR(CURRENT_DATE())
               GROUP BY p.nombre
               ORDER BY total_vendido DESC
               LIMIT 3";

        $result = $this->db->query($sql);
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        return $productos;
    }

    public function getVentasPorTrimestre()
    {
        $sql = "SELECT 
                    CASE 
                        WHEN MONTH(fecha) BETWEEN 1 AND 3 THEN 'Ene-Mar'
                        WHEN MONTH(fecha) BETWEEN 4 AND 6 THEN 'Abr-Jun'
                        WHEN MONTH(fecha) BETWEEN 7 AND 9 THEN 'Jul-Sep'
                        ELSE 'Oct-Dic'
                    END as trimestre,
                    SUM(total) as total_ventas
                FROM venta
                GROUP BY trimestre
                ORDER BY FIELD(trimestre, 'Ene-Mar', 'Abr-Jun', 'Jul-Sep', 'Oct-Dic')";

        $result = $this->db->query($sql);
        $ventas = ['Ene-Mar' => 0, 'Abr-Jun' => 0, 'Jul-Sep' => 0, 'Oct-Dic' => 0];
        while ($row = $result->fetch_assoc()) {
            $ventas[$row['trimestre']] = (float)$row['total_ventas'];
        }
        return $ventas;
    }

    public function getStockPorTrimestre()
    {
        $sql = "SELECT SUM(stock) as total FROM producto WHERE estado = 'activo'";
        $result = $this->db->query($sql);
        $total = $result->fetch_assoc()['total'];

        return [
            'Ene-Mar' => round($total / 4),
            'Abr-Jun' => round($total / 4),
            'Jul-Sep' => round($total / 4),
            'Oct-Dic' => round($total / 4)
        ];
    }
}
