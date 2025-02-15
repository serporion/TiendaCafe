<?php

namespace Repositories;

use Lib\BaseDatos;
use PDO;
use Models\Carrito;

class CarritoRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    /**
     * Método que se encarga los datos del carrito desde la base de datos.
     * @param int $usuario_id del usuario logado.
     * @return Carrito|null
     */
    public function cargarCarritoDeBaseDatos(int $usuario_id): ?array {

        try {

            $stmt = $this->conexion->prepare("SELECT * FROM carritos_guardados WHERE usuario_id = :usuario_id");
            $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {

                $carrito = json_decode($resultado['carrito'], true) ?? [];
                return $carrito;
            }

            $_SESSION['carrito_info'] = "El usuario no tiene carrito guardado.";

            return null;

        } catch (\PDOException $e) {

            $_SESSION['error_carrito'] = "Ocurrió un error al intentar recuperar el carrito. Por favor, inténtalo más tarde.";
            error_log("Ocurrió un error al intentar recuperar el carrito. Por favor, inténtalo más tarde." . $e->getMessage());

            return null;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que guarda los datos del carrito en la base de datos para un usuario específico.
     * Si ya existe un carrito asociado al usuario, se actualiza; de lo contrario, se inserta uno nuevo.
     *
     * @param int $usuario ID del usuario asociado al carrito.
     * @param array $carrito Datos del carrito a guardar.
     * @return bool Devuelve true si el carrito fue guardado correctamente, o false en caso de error.
     */
    public function guardarCarrito(int $usuario, array $carrito): bool {

        try {
            $carritoJson = json_encode($carrito);

            // Con esta forma de proceder primero se intenta actualizar.
            $stmtUpdate = $this->conexion->prepare("
            UPDATE carritos_guardados 
            SET carrito = :carrito 
            WHERE usuario_id = :usuario_id
        ");
            $stmtUpdate->bindParam(":usuario_id", $usuario, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":carrito", $carritoJson, PDO::PARAM_STR);
            $stmtUpdate->execute();

            // Si no se actualiza ninguna fila, se procede a insertar una nueva, ya que se entiende
            // que no hay un carrito para ese usuario.
            if ($stmtUpdate->rowCount() == 0) {

                $stmtInsert = $this->conexion->prepare("
                INSERT INTO carritos_guardados (usuario_id, carrito) 
                VALUES (:usuario_id, :carrito)
            ");
                $stmtInsert->bindParam(":usuario_id", $usuario, PDO::PARAM_INT);
                $stmtInsert->bindParam(":carrito", $carritoJson, PDO::PARAM_STR);
                $stmtInsert->execute();
            }

            return true;

        } catch (\PDOException $e) {
            error_log("Error al guardar carrito: " . $e->getMessage());
            $_SESSION['error_carrito'] = "No se pudo grabar el carrito del usuario.";
            return false;

        } finally {
            if (isset($stmtUpdate)) $stmtUpdate->closeCursor();
            if (isset($stmtInsert)) $stmtInsert->closeCursor();
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que elimina el carrito guardado de un usuario en la base de datos.
     *
     * @param int $usuario_id ID del usuario cuyo carrito se desea eliminar.
     * @return bool Devuelve true si se eliminó el carrito correctamente, o false
     * si no se encontró un carrito o se produjo un error.
     */
    public function borrarCarrito(int $usuario_id): bool {

        try {
            $stmt = $this->conexion->prepare("DELETE FROM carritos_guardados WHERE usuario_id = :usuario_id");
            $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;

            } else {
                $_SESSION['carrito_info'] = "No se encontró un carrito guardado para este usuario.";
                return false;
            }

        } catch (\PDOException $e) {
            $_SESSION['error_carrito'] = "Ocurrió un error al intentar borrar el carrito.";
            error_log("Error al borrar el carrito: " . $e->getMessage());
            return false;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
        }
    }

}
