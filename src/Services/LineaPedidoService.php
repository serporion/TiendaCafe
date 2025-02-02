<?php

namespace Services;

use Repositories\LineaPedidoRepository;

/**
 * Clase que recibe peticiones de PedidoController y se pone en contacto con LineaPedidoRepository
 */
class LineaPedidoService {

    private LineaPedidoRepository $lineaPedidoRepository;

    public function __construct() {
        $this->lineaPedidoRepository = new LineaPedidoRepository();
    }

    /**
     * Metodo que llama al repository para listar todas las lineas
     * de pedido de un pedido.
     * @var int $id del pedido del que sacar las lineas de pedido
     * @return array
     */
    public function verLineaPedido(int $id): array {
        return $this->lineaPedidoRepository->verLineaPedido($id);
    }


    /**
     * Metodo que llama al repository para listar todas las lineas
     * de pedido que contienen un producto.
     * @var int $id del producto del que sacar las lineas de pedido
     * @return array
     */
    public function verProductoLineaPedido(int $id): array {
        return $this->lineaPedidoRepository->verProductoLineaPedido($id);
    }
}