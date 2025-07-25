<?php
class VentasModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Obtener productos disponibles
    public function obtenerProductosDisponibles()
    {
        $resultado = $this->conexion->query("SELECT id, nombre, precio FROM producto WHERE stock > 0");

        if (!$resultado) {
            throw new Exception('Error en la consulta: ' . $this->conexion->error);
        }

        $productos = [];
        while ($row = $resultado->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    // Registrar una nueva venta
    public function registrarVenta($total, $detalles)
    {
        $this->conexion->begin_transaction();

        try {
            // 1. Registrar venta principal
            $stmt = $this->conexion->prepare("INSERT INTO venta (total) VALUES (?)");
            $stmt->bind_param("d", $total);

            if (!$stmt->execute()) {
                throw new Exception('Error al registrar venta: ' . $stmt->error);
            }

            $idVenta = $this->conexion->insert_id;

            // 2. Registrar detalles y actualizar stocks
            $stmtDetalle = $this->conexion->prepare(
                "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) 
                 VALUES (?, ?, ?, ?)"
            );

            $stmtStock = $this->conexion->prepare(
                "UPDATE producto SET stock = stock - ? WHERE id = ?"
            );

            foreach ($detalles as $item) {
                // Registrar detalle
                $stmtDetalle->bind_param("iiid", $idVenta, $item['id'], $item['cantidad'], $item['precio']);
                if (!$stmtDetalle->execute()) {
                    throw new Exception('Error al registrar detalle: ' . $stmtDetalle->error);
                }

                // Actualizar stock
                $stmtStock->bind_param("ii", $item['cantidad'], $item['id']);
                if (!$stmtStock->execute()) {
                    throw new Exception('Error al actualizar stock: ' . $stmtStock->error);
                }
            }

            $this->conexion->commit();
            return $idVenta;
        } catch (Exception $e) {
            $this->conexion->rollback();
            throw $e;
        }
    }
}
