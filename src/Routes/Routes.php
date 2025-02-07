<?php
namespace Routes;
use Lib\Router;


use Controllers\ErrorController;
use Controllers\AuthController;
use Controllers\ProductoController;
use Controllers\CategoriaController;
use Controllers\CarritoController;
use Controllers\PedidoController;


/**
 * Clase que controla las rutas de la aplicacion
 */
// para almacenar las rutas que configuremos desde el archivo index.php
class Routes
{
    public static function index() : void
    {

        /*
        Router::add('GET', '/', function () {
            return "Bienvenido";
        });
        */
        
        Router::add('GET','/',function(){
            (new ProductoController())->inicio();
        });
        



        Router::add('GET','/Auth/registrarUsuario',function(){
            (new AuthController())->registrarUsuario();
        });

        Router::add('POST','/Auth/insertarUsuario',function(){
            (new AuthController())->insertarUsuario();
        });

        Router::add('GET','/Auth/iniciarSesion',function(){
            (new AuthController())->iniciarSesion();
        });

        Router::add('POST','/Auth/iniciarSesion',function(){
            (new AuthController())->iniciarSesion();
        });

        Router::add('GET','/Auth/logout',function(){  
            (new AuthController())->logout();
        });

        Router::add('GET','/Auth/verTusDatos',function(){
            (new AuthController())->verTusDatos();
        });

        Router::add('GET','/Auth/testToken',function(){
            (new AuthController())->testToken();
        });

        Router::add('GET','/Auth/confirmarCuenta/:id',function(string $token){
            (new AuthController())->confirmarCuenta($token);
        });




        Router::add('GET','/Categoria/categorias',function(){
            (new CategoriaController())->categorias();
        });

        Router::add('GET','/Categoria/almacenarCategoria',function(){
            (new CategoriaController())->almacenarCategoria();
        });

        Router::add('POST','/Categoria/almacenarCategoria',function(){
            (new CategoriaController())->almacenarCategoria();  
        });

        Router::add('GET','/Categoria/ProductXCategory/:id',function(int $id){
            (new CategoriaController())->ProductXCategory($id);
        });

        Router::add('GET','/Categoria/actualizarCategoria',function(){
            (new CategoriaController())->actualizarCategoria();
        });

        Router::add('POST','/Categoria/actualizarCategoria',function(){
            (new CategoriaController())->actualizarCategoria();
        });

        Router::add('GET','/Categoria/borrarCategoria',function(){
            (new CategoriaController())->borrarCategoria();
        });

        Router::add('POST','/Categoria/borrarCategoria',function(){
            (new CategoriaController())->borrarCategoria();
        });




        Router::add('GET','/Producto/inicio',function(){
            (new ProductoController())->inicio();
        });







        Router::add('GET','/Producto/guardarProductos',function(){
            (new ProductoController())->guardarProductos();
        });

        Router::add('POST','/Producto/guardarProductos',function(){
            (new ProductoController())->guardarProductos();
        });

        Router::add('GET','/Producto/detalleProducto/:id',function(int $id){
            (new ProductoController())->detalleProducto($id);
        });

        Router::add('GET','/Producto/borrarProducto/:id',function(int $id){
            (new ProductoController())->borrarProducto($id);
        });

        Router::add('GET','/Producto/actualizarProducto/:id',function(int $id){
            (new ProductoController())->actualizarProducto($id);
        });

        Router::add('POST','/Producto/actualizarProducto/:id',function(int $id){
            (new ProductoController())->actualizarProducto($id);
        });


        

        Router::add('GET','/Carrito/cargarCarrito',function(){
            (new CarritoController())->cargarCarrito();
        });

        Router::add('GET','/Carrito/addProduct/:id',function(int $id){
            (new CarritoController())->addProduct($id);
        });

        Router::add('GET','/Carrito/clearCart',function(){
            (new CarritoController())->clearCart();
        });

        Router::add('GET','/Carrito/removeItem/:id',function(int $id){
            (new CarritoController())->removeItem($id);
        });

        /*
        Router::add('GET','/Carrito/disminuir/:id',function(int $id){
            (new CarritoController())->disminuir($id);
        });

        Router::add('GET','/Carrito/aumentar/:id',function(int $id){
            (new CarritoController())->aumentar($id);
        });
        */

        Router::add('POST','/Carrito/disminuir/:id',function(int $id){
            (new CarritoController())->disminuir($id);
        });

        Router::add('POST','/Carrito/aumentar/:id',function(int $id){
            (new CarritoController())->aumentar($id);
        });




        Router::add('GET','/Pedido/guardarPedido',function(){
            (new PedidoController())->guardarPedido();
        });

        Router::add('POST','/Pedido/guardarPedido',function(){
            (new PedidoController())->guardarPedido();
        });

        Router::add('GET','/Pedido/Pedidos',function(){
            (new PedidoController())->verPedido();
        });


        Router::add('GET','/not-found',function(){
            ErrorController::show_Error404();
        });



        Router::dispatch();

    }

}