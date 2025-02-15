<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Producto;
use PDO;
use PDOException;

/**
 * Clase que se comunica con la base de datos realizando consultas a la productos
 */
class ProductoRepository {

    private BaseDatos $conexion;

    public function __construct() {
        $this->conexion = new BaseDatos();
    }

    /**
     * Metodo que guarda las lineas de pedido en la base de datos
     * @var Producto $producto objeto con los datos del producto a guardar
     * @return bool|string
     */
    public function guardarProductos(Producto $producto): bool|string {

        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, oferta, fecha, imagen)
                 VALUES (:categoria_id, :nombre, :descripcion, :precio, :stock, :oferta, :fecha, :imagen)"
            );

            $stmt->bindValue(':categoria_id', $producto->getCategoriaId(), PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $producto->getDescripcion(), PDO::PARAM_STR);
            $stmt->bindValue(':precio', number_format($producto->getPrecio(), 2, '.', ''), PDO::PARAM_STR);
            $stmt->bindValue(':stock', $producto->getStock(), PDO::PARAM_INT);
            $stmt->bindValue(':oferta', $producto->getOferta(), PDO::PARAM_STR);
            $stmt->bindValue(':fecha', $producto->getFecha(), PDO::PARAM_STR);
            $stmt->bindValue(':imagen', $producto->getImagen(), PDO::PARAM_STR);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {

            return $e->getMessage();

        } finally {

            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que obtiene todos los productos de la base de datos
     * @return array
     */
    public function mostrarProductos(): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos where categoria_id != 99");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {
            error_log("Error al obtener los productos: " . $e->getMessage());
            return [];

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que obtiene los productos de una determinada categoria de la base de datos
     * @var int $id entero con el id de la categoria de la que obtener los productos
     * @return array
     */
    public function mostrarProductosXCategoria(int $id): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE categoria_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {

            error_log("Error al obtener los productos de la categoria: " . $e->getMessage());
            return [];
        } finally {

            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que cuenta los productos de una categoria.
     * @param int $id entero con el id de la categoria de la que contar los productos
     * @return int
     */
    public function contarProductosXCategoria(int $id): int {

        try {
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();

        } catch (PDOException $e) {

            error_log("Error al contar los productos de la categoria: " . $e->getMessage());
            return 0;
        } finally {

            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }


    /**
     * Metodo que actualizar la categoria de los productos cuya categoria va a ser borrada
     * @var int entero con el id de la categoria de la que actualizar los productos
     * @return bool|string
     */
    public function actualizarProductosXCategoria(int $id): bool|string {

        try {

            $stmt = $this->conexion->prepare("UPDATE productos SET categoria_id = 99 WHERE categoria_id = :idCategoriaAntigua");
            $stmt->bindValue(':idCategoriaAntigua', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;

        } catch (PDOException $e) {

            return $e->getMessage();
        } finally {

            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que obtiene todos los detalle de un producto especifico
     * @var int entero con el id del que obtener los detalles del producto
     * @return array
     */
    public function detalleProducto(int $id): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE id = :productID");
            $stmt->bindValue(':productID', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {

            error_log("Error al obtener los datos del producto: " . $e->getMessage());
            return [];

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que borra un producto de la base de datos
     * @var int entero con el id del producto a borrar
     */
    public function borrarProducto(int $id): bool {

        try {
            $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = :idProduct");
            $stmt->bindValue(':idProduct', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error al borrar el producto: " . $e->getMessage());
            return false;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que actualizar un producto de la base de datos
     * @var Product $producto datos a actualizar en la base de datos
     * @var int $id entero con el id del producto a actualizar
     * @return bool|string
     */
    public function actualizarProducto(Producto $producto, int $id): bool|string {
        try {
            $stmt = $this->conexion->prepare(
                "UPDATE productos SET categoria_id = :categoria_id, nombre = :nombre, 
                    descripcion = :descripcion, precio = :precio, stock = :stock, oferta = :oferta, 
                    imagen = :imagen  WHERE id = :id"
            );

            $stmt->bindValue(':categoria_id', $producto->getCategoriaId(), PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $producto->getDescripcion(), PDO::PARAM_STR);
            $stmt->bindValue(':precio', number_format($producto->getPrecio(), 2, '.', ''), PDO::PARAM_STR);
            $stmt->bindValue(':stock', $producto->getStock(), PDO::PARAM_INT);
            $stmt->bindValue(':oferta', $producto->getOferta(), PDO::PARAM_STR);
            $stmt->bindValue(':imagen', $producto->getImagen(), PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que actualiza el stock de un producto despues de un pedido
     * @return bool|string
     */
    public function updateStockProduct(): bool|string {
        try {
            $this->conexion->beginTransaction();

            $stmt = $this->conexion->prepare("UPDATE productos SET stock = stock - :cantidad  WHERE id = :id");
            foreach ($_SESSION['carrito'] as $producto) {
                $stmt->bindValue(':cantidad', $producto['cantidad'], PDO::PARAM_INT);
                $stmt->bindValue(':id', $producto['id'], PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {

            $this->conexion->rollBack();
            return $e->getMessage();

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Metodo que actualiza la categoria de los productos que van a ser borrados
     * que está en un pedido.
     * @var int $id entero con el id del producto de la que actualizar la categoria
     * @return bool|string
     */
    public function updateCategoryProduct(int $id): bool|string {

        try {
            $stmt = $this->conexion->prepare("UPDATE productos SET categoria_id = 99 WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            return $e->getMessage();

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }
}