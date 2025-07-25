<?php
class InicioModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerVentasHoy()
    {
        $sql = "SELECT SUM(total) as total_dia FROM venta WHERE DATE(fecha) = CURDATE()";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return (float)$row['total_dia'] ?? 0;
    }

    public function obtenerTotalVentas()
    {
        $sql = "SELECT SUM(total) as total FROM venta";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return (float)$row['total'] ?? 0;
    }

    public function obtenerUltimaVenta()
    {
        $sql = "SELECT total FROM venta ORDER BY fecha DESC LIMIT 1";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return (float)$row['total'] ?? 0;
    }

    public function obtenerTotalProductos()
    {
        $sql = "SELECT COUNT(*) as total FROM producto";
        $result = $this->conexion->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'] ?? 0;
    }

    public function obtenerProductosStockBajo()
    {
        $sql = "SELECT nombre, stock FROM producto WHERE estado = 1 AND stock <= 15 ORDER BY stock ASC";
        $result = $this->conexion->query($sql);

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    public function obtenerResumenDashboard()
    {
        return [
            'ventas_dia' => $this->obtenerVentasHoy(),
            'total_ventas' => $this->obtenerTotalVentas(),
            'ultima_venta' => $this->obtenerUltimaVenta(),
            'total_productos' => $this->obtenerTotalProductos(),
            'stock_bajo' => $this->obtenerProductosStockBajo()
        ];
    }
}
