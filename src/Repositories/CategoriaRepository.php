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
     * Metodo que guarda una categoria en la base de datos
     * @var Categoria $categoria objeto con los datos de la categoria a guardar
     * @return bool|string
     */
    public function guardarCategoria(Categoria $categoria): bool|string {
        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO categorias (nombre) VALUES (:nombre)");

            $stmt->bindValue(':nombre', $categoria->getNombre(), PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } 
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }

     /**
     * Metodo que lista las categorias de la base de datos
     * @return array
     */
     public function listarCategorias(): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM categorias");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener las categorias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Metodo que actualiza una categoria en la base de datos
     * @var Categoria $categoria objeto con los datos de la categoria a actualizar
     * @var int $id de la categoria a actualizar
     * @return bool|string
     */
    public function actualizarCategoria(Categoria $categoria, int $id): bool|string{
        try {
            $stmt = $this->conexion->prepare(
                "UPDATE categorias SET nombre = :categoria WHERE id = :idCategoria");

               
            $stmt->bindValue(':categoria', $categoria->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':idCategoria', $id, PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } 
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Metodo que borra una categoria de la base de datos
     * @var int $id de la categoria a borrar
     * @return bool|string
     */
    public function borrarCategoria(int $id): bool|string{
        try {
            $stmt = $this->conexion->prepare(
                "DELETE FROM categorias WHERE id = :idCategoria");

               
            $stmt->bindValue(':idCategoria', $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount() > 0;
        } 
        catch (PDOException $e) {
            error_log("Error al borrar la categoria: " . $e->getMessage());
            return false;
        }
    }

    
}