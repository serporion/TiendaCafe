<?php

namespace Repositories;

use Lib\BaseDatos;
use PDO;
use PDOException;

/**
 * Clase que se comunica con la base de datos realizando consultas a la tabla lineas_pedidos.
 */
class LineaPedidoRepository {

    private BaseDatos $conexion;

    public function __construct() {
        $this->conexion = new BaseDatos();
    }

    /**
     * Método que guarda las líneas de pedido en la base de datos.
     *
     * @param int $idPedido ID del pedido al que se le guardarán las líneas de pedido.
     * @return bool|string Devuelve `true` si el proceso fue correcto, o mensaje de error en caso contrario.
     */
    public function grabarLinea(int $idPedido, BaseDatos $conexion): bool|string {
        try {
            if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
                return false;
            }

            $stmt = $conexion->prepare(
                "INSERT INTO lineas_pedidos (pedido_id, producto_id, unidades, precio_unitario)
             VALUES (:pedido_id, :producto_id, :unidades, :precio_unitario)"
            );

            foreach ($_SESSION['carrito'] as $producto) {
                $stmt->bindValue(':pedido_id', $idPedido, PDO::PARAM_INT);
                $stmt->bindValue(':producto_id', $producto['id'], PDO::PARAM_INT);
                $stmt->bindValue(':unidades', $producto['cantidad'], PDO::PARAM_INT);
                $stmt->bindValue(':precio_unitario', $producto['precio'], PDO::PARAM_STR);
                $stmt->execute();
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error al grabar la línea del pedido: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Método que obtiene todas las líneas de pedido de un pedido de la base de datos.
     *
     * @param int $id ID del pedido del cual se obtendrán las líneas de pedido.
     * @return array Devuelve un array con las líneas de pedido o un array vacío si no hay resultados.
     */
    public function verLineaPedido(int $id): array {

        try {
            $stmt = $this->conexion->prepare("
            SELECT lp.*, p.nombre 
            FROM lineas_pedidos lp
            JOIN productos p ON lp.producto_id = p.id
            WHERE lp.pedido_id = :id
        ");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {
            error_log("Error al obtener las líneas del pedido: " . $e->getMessage());
            return [];

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
        }
    }


    /**
     * Método que obtiene todas las líneas de pedido relacionadas con un producto.
     *
     * @param int $id ID del producto asociado a las líneas de pedido.
     * @return array Devuelve un array con las líneas de pedido o un array vacío si no hay resultados.
     */
    public function verProductoLineaPedido(int $id): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM lineas_pedidos WHERE producto_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {
            // Registrar error en el log.
            error_log("Error al obtener las líneas relacionadas con el producto: " . $e->getMessage());
            return [];

        } finally {
            // Liberar recursos y cerrar conexión.
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }
}