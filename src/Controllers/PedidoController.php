<?php

namespace Controllers;

use Exception;
use Lib\Pages;
use Lib\Mail;
use Lib\Validar;
use Lib\BaseDatos;
use Models\Pedido;
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
    private BaseDatos $conexion;
    
    public function __construct() {
        $this->pages = new Pages();
        $this->mail = new Mail();
        $this->utiles = new Utilidades();
        $this->pedidoService = new PedidoService();
        $this->lineaPedidoService = new LineaPedidoService();
        $this->productoService = new ProductoService();
        $this->conexion = new BaseDatos();
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

            if (!empty($errores)) {
                $this->pages->render('Pedido/formularioPedidos', ["errores" => $errores]);
                return;
            }

                    
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

            $this->conexion->beginTransaction();

            try {

                $resultado = $this->pedidoService->guardarPedido($orderData);

                if ($resultado !== true) {
                    throw new Exception("Error al guardar el pedido: " . $resultado);
                }

                $stock = $this->productoService->updateStockProduct();
                if ($stock !== true) {
                    throw new Exception("Error al actualizar el stock: " . $stock);
                }

                $this->conexion->commit();

                $_SESSION['order'] = true;
                unset($_SESSION['carrito'], $_SESSION['totalCost']); //, $_SESSION['orderID']);
                $this->pages->render('Pedido/formularioPedidos');
                exit;

            } catch (Exception $e) {

                $this->conexion->rollback();

                error_log($e->getMessage());

                $errores['db'] = $e->getMessage();
                $this->pages->render('Pedido/formularioPedidos', ["errores" => $errores]);
            }

        }
    }


/*
    function guardarPedido(): void
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
                        //unset($_SESSION['orderID']);
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
*/

   /**
    * Metodo que renderiza la vista con los pedidos, sus lineas y los productos
    * de cada linea de pedido mediante su id de pedido.
    *
    * @return void
    */
    public function verPedido(): void
    {
        if (!$this->utiles->comprueboSesion()) {
            header("Location: " . BASE_URL . "");
        } else {
            // Obtener los pedidos
            $orders = $this->pedidoService->verPedido();
            $ordersLine = [];
            $products = [];

            // Recorrer los pedidos y sus líneas
            foreach ($orders as $order) {
                $ordersLineIndividual = $this->lineaPedidoService->verLineaPedido($order['id']);

                foreach ($ordersLineIndividual as &$line) {
                    // Obtener detalles del producto desde la base de datos
                    $product = $this->productoService->detalleProducto($line['producto_id']);

                    // Verificar si el producto existe y asociarlo
                    if (!empty($product) && $product[0]['id'] == $line['producto_id']) {
                        $products[$line['producto_id']] = $product[0]['nombre'];

                        // Asegúrate de obtener el precio desde la tabla lineas_pedidos (precio_unitario)
                        $line['precio'] = $line['precio_unitario']; // Aquí usamos el precio unitario de la línea de pedido
                    }
                }

                $ordersLine[] = $ordersLineIndividual; // Agregar las líneas de este pedido al array de líneas de pedidos
            }

            // Guardar los nombres de productos en la sesión para la vista
            $_SESSION['productsOrders'] = $products;

            // Pasar los datos a la vista
            $this->pages->render('Pedido/pedidos', [
                'orders' => $orders,
                'ordersLine' => $ordersLine,
                'productsOrders' => $_SESSION['productsOrders']
            ]);
        }
    }


    /**
     * Método que muestra si eres administrador, un listado de pedidos por usuario.
     * @param int $usuario_id del usuario logueado para listar sus pedidos.
     *
     * @return void
     */

    public function verPedidoXUsuario(int $usuario_id): void
    {
        if(!$this->utiles->comprueboSesion()){
            header("Location: " . BASE_URL ."");
            exit;
        } else {

            $idUsuPedido = Validar::sanitizeInt($usuario_id);

            if (Validar::validateInt($idUsuPedido)) {


                $orders = $this->pedidoService->verPedidoXUsuario($idUsuPedido);
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

            }else {
                $_SESSION['errorPedido'] = "El pedido no existe";
                $this->pages->render('Auth/listarUsuarios');
            }
        }
    }
    /**
     * Metodo para mostrar el formulario de modificacion de pedido
     * @param int $id id del pedido a modificar
     * @return void
     */
    public function modificarPedido(int $id){
        if(!$this->utiles->comprueboSesion() || !$this->utiles->comprueboAdministrador()){
            header("Location: " . BASE_URL ."");
        } else {
            $pedido = $this->pedidoService->mostrarPedidoPorId($id);
            $this->pages->render('Pedido/modificarPedido', [
                'pedido' => $pedido
            ]);
        }
    }

    /**
     * Metodo para grabar la modificacion del pedido. Llamará al servicio pasando el id
     * y el nuevo estado desde una variable de sesión.
     */

    public function grabarModificacion(){
        if(!$this->utiles->comprueboSesion() || !$this->utiles->comprueboAdministrador()){
            header("Location: " . BASE_URL ."");
        } else {
            $id = $_POST['id'];
            $estado = $_POST['estado'];

            $resultado = $this->pedidoService->actualizarEstadoPedido($id, $estado);

            if($resultado){
                // Obtener nuevamente los pedidos (aquí está la clave)
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
                $_SESSION['modificaPedido'] = 'Estado del pedido Modificado';

                // Renderizar la vista con los pedidos
                $this->pages->render('Pedido/pedidos', [
                    'orders' => $orders,
                    'ordersLine' => $ordersLine,
                    'productsOrders' => $_SESSION['productsOrders']
                ]);
            } else {
                $_SESSION['modificaPedido'] = 'Error al actualizar el pedido';
            }
        }
    }


}
