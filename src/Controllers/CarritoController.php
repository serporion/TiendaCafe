<?php

namespace Controllers;

use Lib\Pages;
use Models\Carrito;
use Models\Producto;
use Lib\Utilidades;
use Services\CarritoService;
use Services\ProductoService;
use Services\CategoriaService;


/**
 * Clase que controla el carrito.
 */
class CarritoController {

    private Pages $pages;
    private Utilidades $utiles;
    private ProductoService $productoService;
    private CategoriaService $categoriaService;
    private CarritoService $carritoService;
    
    
    public function __construct() {
        $this->pages = new Pages();
        $this->utiles = new Utilidades();
        $this->productoService = new ProductoService();
        $this->categoriaService = new CategoriaService();
        $this->carritoService = new CarritoService();
    }

    /**
     * Método que renderiza la vista del carrito
     * @return void
     */
    public function cargarCarrito(){

        $total = $this->priceTotal();

        $_SESSION['totalCost'] = $total;

        $this->pages->render('Carrito/carrito');

    }

    /**
     * Metodo que calcula el precio total del carrito
     * @return int $total -> Varaible con el precio total del carrito
     */
    public function priceTotal (){
        $total = 0;


        if(isset($_SESSION['carrito']) || !empty($_SESSION['carrito'])){
            foreach($_SESSION['carrito'] as $item){
                $total += $item['precio'] * $item['cantidad'];
            }
        }

        return $total;
    }


    /**
     * Metodo que añade un produco al carrito si no esta creado y si lo esta
     * aumenta su cantidad y despues renderiza la vista
     * @var id $id del producto a añadir al carrito
     * @return void
     */
    public function addProduct(int $id){

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array(); 
        }

        $productCart = $this->productoService->detalleProducto($id);

        if (isset($_SESSION['carrito'][$id])) {
            if (isset($_SESSION['limite'][$id]) && !$_SESSION['limite'][$id]) {
                $_SESSION['carrito'][$id]['cantidad'] += 1;
            }
        } 
        else {
            $_SESSION['carrito'][$id] = array(
                'id' => $productCart[0]['id'],
                'imagen' => $productCart[0]['imagen'],
                'nombre' => $productCart[0]['nombre'],
                'precio' => $productCart[0]['precio'],
                'stock' => $productCart[0]['stock'],
                'cantidad' => 1  
            );
        }

        $this->cargarCarrito();

    }

    /**
     * Metodo que vacia el carrito tanto de la base de datos como de la variable de sesion
     * establecida anteriormente y redirije a la vista.
     *
     * @return void
     */
    public function borrarCarrito(){

        $this->carritoService->borrarCarrito($_SESSION['usuario']['id']);

        unset($_SESSION['carrito']);

        header("Location: " . BASE_URL . "Carrito/cargarCarrito");
    }

    /**
     * Metodo que elimina un producto del carrito y tras eso renderiza la vista
     * @var id $id del producto a quitar del carrito´
     * @return void
     */
    public function removeItem (int $id){
        if(isset($_SESSION['carrito'][$id])){
            unset($_SESSION['carrito'][$id]);

            header("Location: " . BASE_URL . "Carrito/cargarCarrito");
        }
        else{
            $errorRemove = 'Error al borrar el producto';
            $total = $this->priceTotal();

            $this->pages->render('Carrito/carrito',['errorRemove' => $errorRemove, 'total' => $total]);
        }
    }

    /**
     * Metod que decrementa la cantidad de un producto y renderiza la vista
     * @var id $id del produto a decrementar su cantidad
     * @return void
     */
    public function disminuir(int $id){
        if(isset($_SESSION['carrito'][$id])){
            $_SESSION['carrito'][$id]['cantidad'] -= 1;

            if($_SESSION['carrito'][$id]['cantidad'] === 0){
                unset($_SESSION['carrito'][$id]);
            }

            header("Location: " . BASE_URL . "Carrito/cargarCarrito");
        }
        else{
            $error = 'Error al quitar unidades';
            $total = $this->priceTotal();

            $this->pages->render('Carrito/carrito',['error' => $error, 'total' => $total]);
        }
    }

    /**
     * Metod que aumenta la cantidad de un producto y renderiza la vista
     * @var id $id del produto a aumentar su cantidad
     * @return void
     */
    public function aumentar(int $id){

        if (!isset($_SESSION['limite'][$id])) {
            $_SESSION['limite'] = []; // Inicializa $_SESSION['limite'] como un array
        }


        if(isset($_SESSION['carrito'][$id])){
            if($_SESSION['carrito'][$id]['cantidad'] === $_SESSION['carrito'][$id]['stock']){
                $_SESSION['limite'][$id] = true;
                header("Location: " . BASE_URL . "Carrito/cargarCarrito");
            }
            else{
                $_SESSION['carrito'][$id]['cantidad'] += 1;

                header("Location: " . BASE_URL . "Carrito/cargarCarrito");
            }
        }
        else{
            $error = 'Error al añadir unidades';
            $total = $this->priceTotal();

            $this->pages->render('Carrito/carrito',['error' => $error, 'total' => $total]);
        }
    }

    /**
     * Método que guarda el carrito de un usuario en el sistema, en la base de datos.
     *
     * @param int $usuario Identificador del usuario al que pertenece el carrito.
     * @param array $carrito Contenido del carrito a guardar.
     * @return bool Devuelve true si el carrito se guardó correctamente, false en caso contrario.
     */
    public function guardarCarrito(int $usuario, array $carrito): bool
    {
        return $this->carritoService->guardarCarrito($usuario, $carrito );
    }


    /**
     * Método que recupera el carrito dependiendo del usuario.
     *
     * @param int $usuarioId del id del usuario.
     * @return void
     */
    public function recuperarCarrito(int $usuarioId): void
    {
        $carrito = $this->carritoService->obtenerCarritoPorUsuarioId($usuarioId);

        if ($carrito) {
            $_SESSION['carrito'] = $carrito; //->getCarrito();
        }
    }

}
