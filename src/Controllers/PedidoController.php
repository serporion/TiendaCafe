<?php

namespace Controllers;

use Lib\Pages;
use Lib\Mail;
use Models\Pedido;
use Models\LineaPedido;
use Lib\Utilidades;
use Services\PedidoService;
use Services\LineaPedidoService;
use Services\ProductoService;

/**
 * Clase que controlan los pedidos.
 */

class PedidoController {

    private Pages $pages;
    private Mail $mail;
    private Utilidades $utiles;
    private PedidoService $pedidoService;
    private LineaPedidoService $lineaPedidoService;
    private ProductoService $productoService;
    
    public function __construct() {
        $this->pages = new Pages();
        $this->mail = new Mail();
        $this->utiles = new Utilidades();
        $this->pedidoService = new PedidoService();
        $this->lineaPedidoService = new LineaPedidoService();
        $this->productoService = new ProductoService();
    }

    /**
     * Metodo que guarda un pedido pidiendo los datos de localizacion,
     * actualizando el stock tras guardar el pedido y sus lineas y enviando
     * un correo para con los datos del pedido
     * @return void
     */
    public function guardarPedido(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_SESSION['usuario'])) {

                $this->pages->render('Pedido/formularioPedidos', [
                    'mensajeInicioSesion' => true
                ]);
                return;
            }

            $this->pages->render('Pedido/formularioPedidos');
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $order = new Pedido(
                null,                        
                0,                            
                $_POST['provincia'],          
                $_POST['localidad'],          
                $_POST['direccion'],          
                0.0,                          
                '',                           
                '',                           
                ''                           
            );

                $order->sanitizarDatos();

                $errores = $order->validarDatos();

                if (empty($errores)) {
                    
                    $orderData = [
                        'usuario_id' => $_SESSION['usuario']['id'],
                        'provincia' => $order->getProvincia(),
                        'localidad' => $order->getLocalidad(),
                        'direccion' => $order->getDireccion(),
                        'coste' => $_SESSION['totalCost'],
                        'estado' => 'confirmado',
                        'fecha' => (new \DateTime())->format('Y-m-d'),
                        'hora' => (new \DateTime())->format('H:i:s'),
                    ];

                    $resultado = $this->pedidoService->guardarPedido($orderData);
                    

                    if ($resultado === true) {
                        $stock = $this->productoService->updateStockProduct();
                        if($stock === true){
                            $order = $this->pedidoService->selectOrder();
                            $this->mail->sendMail($order);
                            $_SESSION['order'] = true;
                            unset($_SESSION['carrito']);
                            unset($_SESSION['totalCost']);
                            unset($_SESSION['orderID']);
                            $this->pages->render('Pedido/formularioPedidos');
                            exit;
                        }
                        else{
                            $errores['db'] = "Error al actualizar el stock" . $stock;
                            $this->pages->render('Pedido/formularioPedidos', ["errores" => $errores]);
                        }
                    } 
                    else {
                        $errores['db'] = "Error al guardar el pedido: " . $resultado;
                        $this->pages->render('Pedido/formularioPedidos', ["errores" => $errores]);
                    }
                } 
                else {
                    $this->pages->render('Pedido/formularioPedidos', ["errores" => $errores]);
                }
        }
    }

   /**
    * Metodo que renderiza la vista con los pedidos, sus lineas y los productos
    * de cada linea de pedido
    * @return void
    */
    public function verPedido(): void
    {
        if(!$this->utiles->comprueboSesion()){
            header("Location: " . BASE_URL ."");
        } else {
            $orders = $this->pedidoService->verPedido();
            $ordersLine = [];
            $products = [];
            foreach ($orders as $order) {
                $ordersLineIndividual = $this->lineaPedidoService->verLineaPedido($order['id']);

                foreach ($ordersLineIndividual as &$line) {
                    $product = $this->productoService->detalleProducto($line['producto_id']);

                    if (!empty($product) && $product[0]['id'] == $line['producto_id']) {
                        $products[$line['producto_id']] = $product[0]['nombre'];
                        $line['precio'] = $product[0]['precio'];
                    }
                }

                $ordersLine[] = $ordersLineIndividual;
            }

            $_SESSION['productsOrders'] = $products;

            $this->pages->render('Pedido/pedidos', [
                'orders' => $orders,
                'ordersLine' => $ordersLine,
                'productsOrders' => $_SESSION['productsOrders']
            ]);
        }
    }



}
