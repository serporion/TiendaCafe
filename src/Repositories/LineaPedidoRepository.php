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
     * Metodo que guarda las lineas de pedido en la base de datos
     * @var int $idPedido entero con el pedido al que guardarle la linea de pedido
     * @return bool|string
     */
    public function grabarLinea(int $idPedido): bool|string {
        try {

            $this->conexion->beginTransaction();

            $stmt = $this->conexion->prepare(
                "INSERT INTO lineas_pedidos (pedido_id, producto_id, unidades)
                 VALUES (:pedido_id, :producto_id, :unidades)"
            );

            foreach ($_SESSION['carrito'] as $producto) {
                $stmt->bindValue(':pedido_id', $idPedido, PDO::PARAM_INT);
                $stmt->bindValue(':producto_id', $producto['id'], PDO::PARAM_INT);
                $stmt->bindValue(':unidades', $producto['cantidad'], PDO::PARAM_INT);

                $stmt->execute();
            }

            $this->conexion->commit();
            return true;
        } 
        catch (PDOException $e) {
            $this->conexion->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Metodo que obtiene todas las lineas de pedido de un pedido de la base de datos
     * @var int $id entero con el pedido a obtener sus lineas de pedido
     * @return array
     */
    public function verLineaPedido(int $id): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM lineas_pedidos WHERE pedido_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener las lineas del pedido: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Metodo que obtiene todas las lineas de pedido con un producto de la base de datos
     * @var int $id entero con el producto a obtener sus lineas de pedido
     * @return array
     */
    public function verProductoLineaPedido(int $id): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM lineas_pedidos WHERE producto_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener las lineas del pedido: " . $e->getMessage());
            return [];
        }
    }



}