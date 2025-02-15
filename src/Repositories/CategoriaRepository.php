<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Categoria;
use PDO;
use PDOException;

/**
 * Clase que se comunica con la base de datos realizando consultas a la tabla categorias.
 */
class CategoriaRepository {

    private BaseDatos $conexion;

    public function __construct() {
        $this->conexion = new BaseDatos();
    }

    /**
     * Método que guarda una categoría en la base de datos.
     *
     * @param Categoria $categoria Objeto con los datos de la categoría a guardar.
     * @return bool|string Devuelve `true` si se guardó correctamente, en caso de error devuelve un mensaje de error.
     */
    public function guardarCategoria(Categoria $categoria): bool|string {

        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO categorias (nombre) VALUES (:nombre)"
            );

            $stmt->bindValue(':nombre', $categoria->getNombre(), PDO::PARAM_STR);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {

            error_log("Error al guardar la categoría: " . $e->getMessage());
            return $e->getMessage();

        } finally {

            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que lista las categorías de la base de datos.
     *
     * @return array Devuelve un arreglo con las categorías o un arreglo vacío si no hay resultados.
     */
    public function listarCategorias(): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM categorias");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {

            error_log("Error al obtener las categorías: " . $e->getMessage());
            return [];

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que actualiza una categoría en la base de datos.
     *
     * @param Categoria $categoria Objeto con los datos de la categoría a actualizar.
     * @param int $id ID de la categoría a actualizar.
     * @return bool|string Retorna `true` si la actualización fue exitosa o un mensaje de error en caso contrario.
     */
    public function actualizarCategoria(Categoria $categoria, int $id): bool|string {

        try {
            $stmt = $this->conexion->prepare(
                "UPDATE categorias SET nombre = :categoria WHERE id = :idCategoria"
            );

            $stmt->bindValue(':categoria', $categoria->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':idCategoria', $id, PDO::PARAM_INT);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {

            error_log("Error al actualizar la categoría: " . $e->getMessage());
            return $e->getMessage();

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que borra una categoría de la base de datos.
     *
     * @param int $id ID de la categoría a borrar.
     * @return bool Retorna `true` si se eliminó correctamente o `false` en caso contrario.
     */
    public function borrarCategoria(int $id): bool {

        try {
            $stmt = $this->conexion->prepare(
                "DELETE FROM categorias WHERE id = :idCategoria"
            );

            $stmt->bindValue(':idCategoria', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            // Registrar el error en el log.
            error_log("Error al borrar la categoría: " . $e->getMessage());
            return false;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }
}