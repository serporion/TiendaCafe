<?php

namespace Services;

use Models\Pedido;
use Repositories\PedidoRepository;

/**
 * Clase que recibe peticiones de PedidoController y se pone en contacto con PedidoRepository
 */
class PedidoService {

    private PedidoRepository $pedidoRepository;

    public function __construct() {
        $this->pedidoRepository = new PedidoRepository();
    }

    /**
     * Metodo que llama al repository para guardar un pedido
     * @var array $orderData con los datos del pedido a guardar
     * @return bool|string
     */
    public function guardarPedido(array $orderData): bool|string {
        try {
            $order = new Pedido(
                null,
                $orderData['usuario_id'],
                $orderData['provincia'],
                $orderData['localidad'],
                $orderData['direccion'],
                $orderData['coste'],
                $orderData['estado'],
                $orderData['fecha'],
                $orderData['hora']
            );

            return $this->pedidoRepository->guardarPedido($order);
        } 
        catch (\Exception $e) {
            error_log("Error al guardar el pedido: " . $e->getMessage());
            return false;
        }
    }

    
    /**
     * Metodo que llamar al repository para ver todos los datos de un pedido.
     * @return array
     */
    public function selectOrder(int $pedidoId): array {
        return $this->pedidoRepository->selectOrder($pedidoId);
    }

    /**
     * Metodo que llamar al repository para ver todos los pedidos.
     * @return array
     */
    public function verPedido(): array {
        return $this->pedidoRepository->verPedido();
    }

    // Dentro de tu clase PedidoService

    /**
     * Método que comunica el controlador con el repositorio para pedir los pedidos del usuario
     * @param int $usuario_id id del usuario.
     * @return array
     */
    public function verPedidoXUsuario(int $usuario_id): array {

        return $this->pedidoRepository->verPedidoXUsuario($usuario_id);

    }

    /**
     * Metodo para obtener un pedido por su ID comunica el controlador con el servicio.
     * @param int $id ID del pedido a buscar
     * @return array|null Array con los datos del pedido o null si no se encuentra
     */
    public function mostrarPedidoPorId(int $id): ?array {

        return $this->pedidoRepository->mostrarPedidoPorId($id);

    }

    /**
     * Método para actualizar el estado de un pedido en la base de datos
     * @param int $id ID del pedido a actualizar
     * @param string $estado Nuevo estado del pedido
     * @return bool True si se actualizó correctamente, false si no
     */
    public function actualizarEstadoPedido(int $id, string $estado): bool {

        return $this->pedidoRepository->actualizarEstadoPedido($id, $estado);

    }

    /**
     * Método que recibe datos de PayPalController para actualizar el campo
     * de la base de datos referente a si el pedido está pagado o no.
     */
    public function marcarPedidoPagado(string $pedidoId, string $transactionId): bool {

        return $this->pedidoRepository->marcarPedidoPagado($pedidoId, $transactionId);

    }

}