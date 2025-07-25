<?php
class CategoriasModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerTodas()
    {
        $resultado = $this->conexion->query("SELECT id, nombre, descripcion FROM categoria");
        if (!$resultado) {
            throw new Exception('Error en la consulta: ' . $this->conexion->error);
        }
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar($termino)
    {
        $stmt = $this->conexion->prepare("SELECT id, nombre, descripcion FROM categoria 
                                         WHERE nombre LIKE ? OR descripcion LIKE ?");
        $terminoBusqueda = "%$termino%";
        $stmt->bind_param("ss", $terminoBusqueda, $terminoBusqueda);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($nombre, $descripcion)
    {
        $stmt = $this->conexion->prepare("INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $descripcion);
        if (!$stmt->execute()) {
            throw new Exception('Error al crear categoría: ' . $stmt->error);
        }
        return $this->conexion->insert_id;
    }

    public function actualizar($id, $nombre, $descripcion)
    {
        $stmt = $this->conexion->prepare("UPDATE categoria SET nombre = ?, descripcion = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar categoría: ' . $stmt->error);
        }
        return $stmt->affected_rows;
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM categoria WHERE id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar categoría: ' . $stmt->error);
        }
        return $stmt->affected_rows;
    }
}
