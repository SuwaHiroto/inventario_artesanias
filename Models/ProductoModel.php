<?php
class ProductoModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarActivos()
    {
        // Esta consulta ya trae el 'id', está perfecta.
        $resultado = $this->conexion->query("SELECT p.*, c.nombre AS categoria FROM producto p JOIN categoria c ON p.id_categoria = c.id WHERE p.estado = 'activo'");
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function listarInactivos()
    {
        // Esta consulta también está bien.
        $resultado = $this->conexion->query("SELECT p.*, c.nombre AS categoria FROM producto p JOIN categoria c ON p.id_categoria = c.id WHERE p.estado = 'inactivo'");
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function registrar($datos)
    {
        $stmt = $this->conexion->prepare("INSERT INTO producto (nombre, descripcion, precio, stock, id_categoria) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssddi",
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['stock'],
            $datos['id_categoria']
        );
        return $stmt->execute();
    }

    public function editar($id, $datos)
    {
        // Usamos $id en lugar de $codigoOriginal
        $stmt = $this->conexion->prepare("UPDATE producto SET nombre = ?, descripcion = ?, precio = ?, stock = ?, id_categoria = ? WHERE id = ?");
        $stmt->bind_param(
            "ssddii", // Cambiamos el último tipo a 'i' para el id
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['stock'],
            $datos['id_categoria'],
            $id // Usamos el id numérico
        );
        return $stmt->execute();
    }

    // ¡NUEVO! Función específica y segura para actualizar solo el stock.
    public function actualizarStock($id, $nuevoStock)
    {
        $stmt = $this->conexion->prepare("UPDATE producto SET stock = ? WHERE id = ?");
        $stmt->bind_param("di", $nuevoStock, $id); // 'd' para decimal/int, 'i' para id
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        // Usamos $id
        $stmt = $this->conexion->prepare("UPDATE producto SET estado = 'inactivo' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function reactivar($id)
    {
        // Usamos $id
        $stmt = $this->conexion->prepare("UPDATE producto SET estado = 'activo' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscar($q, $inactivos = false)
    {
        $estado = $inactivos ? 'inactivo' : 'activo';
        $q = $this->conexion->real_escape_string($q);
        // La búsqueda por código ahora puede ser menos relevante, pero la dejamos por si acaso.
        $sql = "SELECT p.*, c.nombre AS categoria FROM producto p JOIN categoria c ON p.id_categoria = c.id WHERE p.estado = '$estado' AND (p.nombre LIKE '%$q%'  OR c.nombre LIKE '%$q%')";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
