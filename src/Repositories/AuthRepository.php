<?php

    namespace Repositories;

    use ErrorException;
    use Lib\BaseDatos;
    use Models\Auth;
    use PDO;
    use PDOException;
    use Lib\Utilidades;

    /**
     * Clase que se comunica con la base de datos realizando consultas a la tabla usuarios
     */
    class AuthRepository {

        private BaseDatos $conexion;
        private Utilidades $utilidad;


        public function __construct() {
            $this->conexion = new BaseDatos();
            $this->utilidad = new Utilidades();

            //set_error_handler([$this->utilidad, "manejadorWarning"], E_WARNING);
        }

    /**
     * Metodo que guarda usuarios nuevos en la base de datos
     * @var Auth $usuario objeto con los datos del usuario a guardar
     * @return bool|string
     */
    public function insertarUsuario(Auth $usuario): bool|string {

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
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            $this->conexion->cierraConexion();
        }
    }


    /**
     * Metodo que comprueba si existe un correo en la base de datos y
     * recupera lo datos del usuario.
     * @var string $correoUsu a verificar si esta en la base de datos
     * @return ?array con los datos del usuario.
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

        }finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
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

        }finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
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

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            $this->conexion->cierraConexion();
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
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            $this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que consula en la base de datos por todos los usuario de la base de datos.
     * @return array|null
     */
    public function extractAll(): ?array
    {
        try {
            $this->conexion->consulta("SELECT * FROM usuarios");

            $UsuariosData = $this->conexion->extraer_todos();

            if (!$UsuariosData) {
                return null;
            }

            $usuarios = [];
            foreach ($UsuariosData as $usuario) {
                $usuarios[] = Auth::fromArray($usuario);
            }

            return $usuarios;
        } catch (PDOException $e) {
            error_log("Error al extraer todos los usuarios: " . $e->getMessage());
            return null;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            $this->conexion->cierraConexion();
        }
    }

    /**
     * Método que devuelve los datos del usuario con el id consultado.
     *
     * @param int $id del usuario.
     * @return Auth|null, si existe devuelve un objeto Auth.
     */
    public function extraer_Usuario(int $id): ?Auth
    {
        $stmt = null;
        try {
            $stmt = $this->conexion->prepare("SELECT * from usuarios where id=:id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $UsuarioUnico = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$UsuarioUnico) {
                return null;
            }

            return Auth::fromArray($UsuarioUnico);

        } catch (PDOException $e) {
            error_log("Error al extraer usuario por ID: " . $e->getMessage());
            return null;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            $this->conexion->cierraConexion();
        }
    }
        /**
         * Método que actualiza la información de un usuario existente en la base de datos,
         * incluyendo nombre, apellidos, email, estado de confirmación y rol. Si se proporciona
         * una nueva contraseña (diferente de 'vacio'), también se actualiza la contraseña.
         *
         * @param Auth $usuario Objeto Auth que contiene los datos actualizados del usuario.
         * @return bool Retorna true si la actualización fue exitosa, false en caso de error.
         * @throws \PDOException Si ocurre un error durante la ejecución de la consulta SQL.
         */
        public function grabarUsuarioModificado(Auth $usuario): bool
        {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            try {

                $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email, confirmado = :confirmado, rol = :rol";

                // Solo si la contraseña no es 'vacio', se incluirá en la sentencia SQL
                if ($usuario->getContrasena() !== 'vacio') {
                    $sql .= ", password = :password";
                }

                $sql .= " WHERE id = :id";

                $stmt = $this->conexion->prepare($sql);

                $stmt->bindValue(':id', $usuario->getId(), PDO::PARAM_INT);
                $stmt->bindValue(':nombre', $usuario->getNombre(), PDO::PARAM_STR);
                $stmt->bindValue(':apellidos', $usuario->getApellidos(), PDO::PARAM_STR);
                $stmt->bindValue(':email', $usuario->getCorreo(), PDO::PARAM_STR);
                $stmt->bindValue(':confirmado', $usuario->isConfirmado(), PDO::PARAM_BOOL);
                $stmt->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR);

                // Si la contraseña no es 'vacio', aplicamos el hash y lo incluimos en el SQL
                if ($usuario->getContrasena() !== 'vacio') {
                    $hashedPassword = password_hash($usuario->getContrasena(), PASSWORD_BCRYPT);
                    $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
                }

                $stmt->execute();

                return true;

            } catch (\PDOException $e) {

                error_log("Error al actualizar usuario: " . $e->getMessage());
                return false;

            } finally {
                if (isset($stmt)) {
                    $stmt->closeCursor();
                }
                $this->conexion->cierraConexion();
            }
        }


        /**
         * Método que actualiza los valores de la contraseña (encriptada) y el token
         * asociados a un usuario identificado por su ID. Maneja errores relacionados
         * con la base de datos y warnings convertidos en excepciones.
         *
         * @param int $idUsuario El ID del usuario cuyo token y contraseña se van a actualizar.
         * @param string $nuevaContrasena La nueva contraseña encriptada del usuario.
         * @param string $nuevoToken El nuevo token generado para el usuario.
         * @return bool Devuelve `true` si la actualización fue exitosa, o `false` si ocurrió algún error.
         *
         * @throws PDOException Si ocurre un error relacionado con la base de datos.
         * @throws ErrorException Si un warning es convertido a excepción durante la ejecución.
         */
    public function actualizarTokenYContrasena(int $idUsuario, string $nuevaContrasena, string $nuevoToken): bool
    {
        try {

            $sql = "UPDATE usuarios SET password = :contrasena, token = :token WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':contrasena', $nuevaContrasena, PDO::PARAM_STR);
            $stmt->bindParam(':token', $nuevoToken, PDO::PARAM_STR);
            $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);


            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error PDO al actualizar el token y la contraseña: " . $e->getMessage());
            return false;
        } catch (ErrorException $e) {
            error_log("Warning convertido a excepción: " . $e->getMessage());
            return false;
        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $this->conexion->cierraConexion();
            }
            //Hay que restaurar el manejador de errores predeterminado.
            restore_error_handler();
        }
    }

}

