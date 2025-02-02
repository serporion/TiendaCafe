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
    public function selectOrder(): array {
        return $this->pedidoRepository->selectOrder();
    }

    /**
     * Metodo que llamar al repository para ver todos los pedidos.
     * @return array
     */
    public function verPedido(): array {
        return $this->pedidoRepository->verPedido();
    }
}