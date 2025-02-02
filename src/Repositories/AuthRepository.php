<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Auth;
use PDO;
use PDOException;

/**
 * Clase que se comunica con la base de datos realizando consultas a la tabla usuarios
 */
class AuthRepository {

    private BaseDatos $conexion;

    public function __construct() {
        $this->conexion = new BaseDatos();
    }

    /**
     * Metodo que guarda usuarios nuevos en la base de datos
     * @var Auth $usuario objeto con los datos del usuario a guardar
     * @return bool|string
     */
    public function guardarUsuarios(Auth $usuario): bool|string {

        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO usuarios (nombre, apellidos, email, password, rol, confirmado, fecha_expiracion, token)
             VALUES (:nombre, :apellidos, :correo, :contrasena, :rol, :confirmado, :fecha_expiracion, :token)"
            );

            $stmt->bindValue(':nombre', $usuario->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':apellidos', $usuario->getApellidos(), PDO::PARAM_STR);
            $stmt->bindValue(':correo', $usuario->getCorreo(), PDO::PARAM_STR);
            $stmt->bindValue(':contrasena', $usuario->getContrasena(), PDO::PARAM_STR);
            $stmt->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR);
            $stmt->bindValue(':confirmado', $usuario->isConfirmado(), PDO::PARAM_BOOL);
            $stmt->bindValue(':fecha_expiracion', $usuario->getFechaExpiracion() ? $usuario->getFechaExpiracion()->format('Y-m-d H:i:s') : null, PDO::PARAM_STR);
            $stmt->bindValue(':token', $usuario->getToken(), PDO::PARAM_STR);

            $stmt->execute();
            return true;
        }
        catch (PDOException $e) {
            return $e->getMessage();
        } finally {
            if(isset($stmt)){
                $stmt->closeCursor();
            }
        }
    }


    /**
     * Metodo que comprueba si existe un correo en la base de datos.
     * @var string $correoUsu a verificar si esta en la base de datos
     * @return ?array
     */
    public function obtenerCorreo(string $correoUsu): ?array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindValue(':email', $correoUsu, PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        } 
        catch (PDOException $e) {
            error_log("Error al obtener el usuario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Metodo que comprueba si existe el correo al registrarse.
     * @var string $correoUsu con el correo a comprobar si esta en la base de datos
     * @return bool
     */
    public function comprobarCorreo(string $correoUsu): bool {
        try {
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->bindValue(':email', $correoUsu, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchColumn();
            return $result > 0;
        } 
        catch (PDOException $e) {
            error_log("Error al comprobar el correo: " . $e->getMessage());
            return false;
        }
    }

    /**
     *
     * Método que confirma, si existe un correo, cambiando un campo confirmado a true.
     * @param string $correo del usuario a confirmar.
     * @return bool
     */
    public function confirmarUsuario(string $correo): bool {
        try {
            $stmt = $this->conexion->prepare("UPDATE usuarios SET confirmado = TRUE WHERE email = :email");
            $stmt->bindValue(':email', $correo, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al confirmar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Metodo que recibe los datos del servicio para actualizar la base de datos.
     * @param Auth $usuario
     * @return bool
     */
    public function actualizarUsuario(Auth $usuario): bool {
        try {
            $stmt = $this->conexion->prepare(
                "UPDATE usuarios 
             SET confirmado = :confirmado, 
                 fecha_expiracion = :fecha_expiracion, 
                 token = :token
             WHERE email = :email"
            );

            $stmt->bindValue(':confirmado', $usuario->isConfirmado(), PDO::PARAM_BOOL);
            $stmt->bindValue(':fecha_expiracion', null, PDO::PARAM_NULL);
            $stmt->bindValue(':token', $usuario->getToken(), PDO::PARAM_STR);
            $stmt->bindValue(':email', $usuario->getCorreo(), PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar el usuario: " . $e->getMessage());
            return false;
        } finally {
            if(isset($stmt)){
                $stmt->closeCursor();
            }
        }
    }


}