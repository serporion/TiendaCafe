<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Pedido;
use PDO;
use PDOException;
use Repositories\LineaPedidoRepository;

/**
 * Clase que se comunica con la base de datos realizando consultas a la tabla pedidos.
 */
class PedidoRepository {

    private BaseDatos $conexion;
    private LineaPedidoRepository $lineaPedidoRepository;

    public function __construct() {
        $this->conexion = new BaseDatos();
        $this->lineaPedidoRepository = new LineaPedidoRepository();
    }

    /**
     * Metodo que guarda los pedidos en la base de datos y llama al repository
     * de lineas de pedido para que guarde todas sus lineas de pedido.
     * @var Pedido $pedido objeto con los datos de un pedido a guardar.
     * @return bool|string
     */
    public function guardarPedido(Pedido $pedido): bool|string {
        try {

            $stmt = $this->conexion->prepare(
                "INSERT INTO pedidos (usuario_id, provincia, localidad, direccion, coste, estado, fecha, hora)
                 VALUES (:usuario_id, :provincia, :localidad, :direccion, :coste, :estado, :fecha, :hora)"
            );

            $stmt->bindValue(':usuario_id', $pedido->getUsuarioId(), PDO::PARAM_INT);
            $stmt->bindValue(':provincia', $pedido->getProvincia(), PDO::PARAM_STR);
            $stmt->bindValue(':localidad', $pedido->getLocalidad(), PDO::PARAM_STR);
            $stmt->bindValue(':direccion', $pedido->getDireccion(), PDO::PARAM_STR);
            $stmt->bindValue(':coste', number_format($pedido->getCoste(), 2, '.', ''), PDO::PARAM_STR);
            $stmt->bindValue(':estado', $pedido->getEstado(), PDO::PARAM_STR);
            $stmt->bindValue(':fecha', $pedido->getFecha(), PDO::PARAM_STR);
            $stmt->bindValue(':hora', $pedido->getHora(), PDO::PARAM_STR);

            $stmt->execute();

            $pedidoId = $this->conexion->ultimoIDInsertado();

            $_SESSION['orderID'] = $pedidoId;

            $this->lineaPedidoRepository->grabarLinea($pedidoId);

            return true;

        } 
        catch (PDOException $e) {
            error_log("Error al guardar el pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Metodo que obtiene un pedido en especifico de la base de datos.
     * @return array
     */
    public function selectOrder(): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE id = :id");
            $stmt->bindValue(':id', $_SESSION['orderID'], PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener el pedido: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Metodo que obtiene todos los pedidos de un usuario de la base de datos.
     * @return array
     */
    public function verPedido(): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE usuario_id = :id");
            $stmt->bindValue(':id', $_SESSION['usuario']['id'], PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener los pedidos: " . $e->getMessage());
            return [];
        }
    }



}