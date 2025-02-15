<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Pedido;
use PDO;
use PDOException;


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
     * Metodo que guarda un nuevo pedido en la base de datos.
     *
     * @param Pedido $pedido El pedido a guardar
     * @return bool|string true si se guardó correctamente, false o un mensaje de error en caso contrario
     */
    public function guardarPedido(Pedido $pedido): bool|string {

        try {
            $this->conexion->beginTransaction();

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

            if ($pedidoId === 0) {
                throw new \Exception("No se pudo obtener el ID del pedido insertado");
            }

            //$_SESSION['orderID'] = $pedidoId;

            $result = $this->lineaPedidoRepository->grabarLinea($pedidoId, $this->conexion);

            if ($result === true) {
                $this->conexion->commit();
                return true;
            } else {
                $this->conexion->rollBack();
                return $result;
            }
        } catch (\Exception $e) {
            $this->conexion->rollBack();
            error_log("Error al guardar el pedido: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Método que obtiene el pedido actual basado en el ID almacenado en la sesión.
     *
     * @return array Un array con los datos del pedido o un array vacío si no se encuentra
     */
    public function selectOrder(int $pedidoId): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE id = :id");
            //$stmt->bindValue(':id', $_SESSION['orderID'], PDO::PARAM_INT);
            $stmt->bindValue(':id', $pedidoId, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {
            error_log("Error al obtener el pedido: " . $e->getMessage());
            return [];

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que obtiene todos los pedidos del usuario actual.
     *
     * @return array Un array con todos los pedidos del usuario o un array vacío si no se encuentran
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

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que obtiene todos los pedidos de un usuario específico.
     *
     * @param int $usuario_id El ID del usuario
     * @return array Un array con todos los pedidos del usuario o un array vacío si no se encuentran
     */
    public function verPedidoXUsuario(int $usuario_id): array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE usuario_id = :usuario_id");
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (PDOException $e) {
            error_log("Error al obtener los pedidos del usuario: " . $e->getMessage());
            return [];

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que obtiene un pedido específico por su ID.
     *
     * @param int $id El ID del pedido a buscar
     * @return array|null Un array con los datos del pedido o null si no se encuentra
     */
    public function mostrarPedidoPorId(int $id): ?array {

        try {
            $stmt = $this->conexion->prepare("SELECT * FROM pedidos WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            return $pedido ?: null;

        } catch (PDOException $e) {
            error_log("Error al obtener el pedido con ID $id: " . $e->getMessage());
            return null;

        } finally {

            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Método que actualiza el estado de un pedido.
     *
     * @param int $id El ID del pedido a actualizar
     * @param string $estado El nuevo estado del pedido
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function actualizarEstadoPedido(int $id, string $estado): bool {

        try {
            $stmt = $this->conexion->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error al actualizar el estado del pedido con ID $id: " . $e->getMessage());
            return false;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }

    /**
     * Marca un pedido como pagado.
     *
     * @param int $pedidoId El ID del pedido a marcar como pagado
     * @return bool true si se actualizó correctamente, false en caso contrario
     */
    public function marcarPedidoPagado(string $pedidoId, string $transactionId): bool {

        try {
            $stmt = $this->conexion->prepare("UPDATE pedidos SET pagado = :pagado, transaction_id = :transaction_id WHERE id = :id");
            $stmt->bindValue(':id', $pedidoId, PDO::PARAM_INT);
            $stmt->bindValue(':pagado', 1, PDO::PARAM_BOOL);
            $stmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error al actualizar el campo pagado con ID pedido $pedidoId: " . $e->getMessage());
            return false;

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
            }
            //$this->conexion->cierraConexion();
        }
    }
}