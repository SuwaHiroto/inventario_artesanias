<?php
// /models/UsuarioModel.php

class UsuarioModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerTodosLosUsuarios()
    {
        $query = "SELECT id, nombre, apellidos, user, rol, estado FROM usuario ORDER BY nombre ASC";
        $resultado = $this->conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerUsuarioPorId(int $id)
    {
        $query = "SELECT id, nombre, apellidos, telefono, user, rol, estado FROM usuario WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function registrarNuevoUsuario(array $datos)
    {
        $contrasenaHashed = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        $query = "INSERT INTO usuario (nombre, apellidos, telefono, user, contrasena, rol) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($query);
        if (!$stmt) throw new Exception("Error al preparar la consulta: " . $this->conexion->error);

        $stmt->bind_param("ssssss", $datos['nombre'], $datos['apellidos'], $datos['telefono'], $datos['user'], $contrasenaHashed, $datos['rol']);

        if (!$stmt->execute()) {
            if ($this->conexion->errno === 1062) throw new Exception("El nombre de usuario '{$datos['user']}' ya existe.");
            throw new Exception("Error al registrar el usuario: " . $stmt->error);
        }
        return $this->conexion->insert_id;
    }

    public function actualizarUsuario(int $id, array $datos)
    {
        if (!empty($datos['contrasena'])) {
            $contrasenaHashed = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
            $query = "UPDATE usuario SET nombre = ?, apellidos = ?, telefono = ?, user = ?, rol = ?, contrasena = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("ssssssi", $datos['nombre'], $datos['apellidos'], $datos['telefono'], $datos['user'], $datos['rol'], $contrasenaHashed, $id);
        } else {
            $query = "UPDATE usuario SET nombre = ?, apellidos = ?, telefono = ?, user = ?, rol = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("sssssi", $datos['nombre'], $datos['apellidos'], $datos['telefono'], $datos['user'], $datos['rol'], $id);
        }

        if (!$stmt->execute()) {
            if ($this->conexion->errno === 1062) throw new Exception("El nombre de usuario '{$datos['user']}' ya está en uso por otro usuario.");
            throw new Exception("Error al actualizar el usuario: " . $stmt->error);
        }
        return $stmt->affected_rows > 0;
    }

    public function cambiarEstadoUsuario(int $id, int $estado)
    {
        $query = "UPDATE usuario SET estado = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ii", $estado, $id);

        if (!$stmt->execute()) throw new Exception("Error al cambiar el estado del usuario: " . $stmt->error);
        return $stmt->affected_rows > 0;
    }
}
