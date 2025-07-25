<?php
class HistorialVentasModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerVentas($fechaInicio = null, $fechaFin = null, $idProducto = null)
    {
        $sql = "SELECT v.fecha, p.nombre AS producto, dv.cantidad, dv.precio_unitario, 
                       (dv.cantidad * dv.precio_unitario) AS monto_total
                FROM detalle_venta dv
                JOIN venta v ON v.id = dv.id_venta
                JOIN producto p ON p.id = dv.id_producto
                WHERE 1 = 1";

        if ($fechaInicio) {
            $sql .= " AND v.fecha >= '" . $this->conexion->real_escape_string($fechaInicio) . "'";
        }
        if ($fechaFin) {
            $sql .= " AND v.fecha <= '" . $this->conexion->real_escape_string($fechaFin) . "'";
        }
        if ($idProducto) {
            $sql .= " AND p.id = " . intval($idProducto);
        }

        $resultado = $this->conexion->query($sql);
        if (!$resultado) {
            throw new Exception($this->conexion->error);
        }

        $ventas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ventas[] = $fila;
        }

        return $ventas;
    }

    public function obtenerProductos()
    {
        $sql = "SELECT id, nombre FROM producto ORDER BY nombre";
        $resultado = $this->conexion->query($sql);

        if (!$resultado) {
            throw new Exception($this->conexion->error);
        }

        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        return $productos;
    }
}
