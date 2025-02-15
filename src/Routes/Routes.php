<?php
namespace Routes;
use Controllers\ApiProductoController;
use Controllers\PayPalController;
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

        Router::add('GET','/',function(){
            (new ProductoController())->inicio();
        });



        Router::add('GET','/Auth/registrarUsuario',function(){
            (new AuthController())->registrarUsuario();
        }, 'admin');

        Router::add('POST','/Auth/insertarUsuario',function(){
            (new AuthController())->insertarUsuario();
        }, 'admin');

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
        }, 'admin');

        Router::add('GET','/Auth/confirmarCuenta/:id',function(string $token){
            (new AuthController())->confirmarCuenta($token);
        });

        /*
        Router::add('GET','Auth/extraer_todos',function(){
            (new AuthController())->extraer_todos();
        });
        */

        //Rutas puestas como ADMIN
        Router::add('GET','Auth/extraer_todos',function(){
            (new AuthController())->extraer_todos();
        }, 'admin'); // Agregado el rol 'admin'


        Router::add('GET','Auth/editarUsuario/:id',function(string $id){
            (new AuthController())->editarUsuario($id);
        }, 'admin');

        Router::add('POST','Auth/editarUsuario/:id',function(string $id){
            (new AuthController())->editarUsuario($id);
        }, 'admin');

        Router::add('POST','Auth/guardarUsuarios',function(){
            (new AuthController())->guardarUsuarios();
        }, 'admin');

        Router::add('GET','Auth/enviarCorreoRecuperacion',function(){
            (new AuthController())->enviarCorreoRecuperacion();
        });

        Router::add('POST','Auth/envioCorreoRestablecerContrasena',function(){
            (new AuthController())->envioCorreoRestablecerContrasena();
        });

        Router::add('GET','/Auth/restablecerContrasena/:id',function(string $token){
            (new AuthController())->restablecerContrasena($token);
        });

        Router::add('GET','/Auth/regenerarContrasena',function(){
            (new AuthController())->regenerarContrasena();
        });

        Router::add('POST','/Auth/regenerarContrasena',function(){
            (new AuthController())->regenerarContrasena();
        });




        Router::add('GET','/Categoria/categorias',function(){
            (new CategoriaController())->categorias();
        });

        Router::add('GET','/Categoria/almacenarCategoria',function(){
            (new CategoriaController())->almacenarCategoria();
        }, 'admin');

        Router::add('POST','/Categoria/almacenarCategoria',function(){
            (new CategoriaController())->almacenarCategoria();  
        },'admin');

        Router::add('GET','/Categoria/ProductXCategory/:id',function(int $id){
            (new CategoriaController())->ProductXCategory($id);
        });

        Router::add('GET','/Categoria/actualizarCategoria',function(){
            (new CategoriaController())->actualizarCategoria();
        },'admin');

        Router::add('POST','/Categoria/actualizarCategoria',function(){
            (new CategoriaController())->actualizarCategoria();
        },'admin');

        Router::add('GET','/Categoria/borrarCategoria',function(){
            (new CategoriaController())->borrarCategoria();
        },'admin');

        Router::add('POST','/Categoria/borrarCategoria',function(){
            (new CategoriaController())->borrarCategoria();
        },'admin');




        Router::add('GET','/Producto/inicio',function(){
            (new ProductoController())->inicio();
        });

        Router::add('GET','/Producto/guardarProductos',function(){
            (new ProductoController())->guardarProductos();
        },'admin');

        Router::add('POST','/Producto/guardarProductos',function(){
            (new ProductoController())->guardarProductos();
        },'admin');

        Router::add('GET','/Producto/detalleProducto/:id',function(int $id){
            (new ProductoController())->detalleProducto($id);
        });

        Router::add('GET','/Producto/borrarProducto/:id',function(int $id){
            (new ProductoController())->borrarProducto($id);
        },'admin');

        Router::add('GET','/Producto/actualizarProducto/:id',function(int $id){
            (new ProductoController())->actualizarProducto($id);
        },'admin');

        Router::add('POST','/Producto/actualizarProducto/:id',function(int $id){
            (new ProductoController())->actualizarProducto($id);
        },'admin');


        

        Router::add('GET','/Carrito/cargarCarrito',function(){
            (new CarritoController())->cargarCarrito();
        });

        Router::add('GET','/Carrito/addProduct/:id',function(int $id){
            (new CarritoController())->addProduct($id);
        });

        Router::add('GET','/Carrito/borrarCarrito',function(){
            (new CarritoController())->borrarCarrito();
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

        Router::add('GET','/Pedido/modificarPedido/:id',function(int $id){
            (new PedidoController())->modificarPedido($id);
        },'admin');

        Router::add('POST','/Pedido/grabarModificacion',function(){
            (new PedidoController())->grabarModificacion();
        },'admin');

        Router::add('GET','/Pedido/verPedidoXUsuario/:id',function(int $id){
            (new PedidoController())->verPedidoXUsuario($id);
        },'admin');

        Router::add('GET', '/PayPal/iniciarPago/:id', function(int $id) {
            (new PayPalController())->iniciarPago($id);
        });

        Router::add('GET', '/PayPal/pagoCancelado/:id', function(int $id) {
            (new PayPalController())->pagoCancelado($id);
        });


        /*
        Router::add('GET', '/PayPal/pagoExitoso/:id', function(string $id) {
            (new PayPalController())->pagoExitoso($id);
        });

        Router::add('POST', '/PayPal/pagoExitoso/:id', function(string $id) {
            (new PayPalController())->pagoExitoso($id);
        });

        Router::add('GET', '/PayPal/pagoExitoso', function() {
            (new PayPalController())->pagoExitoso();
        });

        Router::add('POST', '/PayPal/pagoExitoso', function() {
            (new PayPalController())->pagoExitoso();
        });


        Router::add('GET', '/PayPal/pagoExitoso/{pedidoId}/[{token}/{payerId}]', function(string $pedidoId, ?string $token = null, ?string $payerId = null) {
            (new PayPalController())->pagoExitoso($pedidoId, $token, $payerId);
        });

        */

        Router::add('GET', '/PayPal/pagoExitoso', function() {
            $pedidoId = $_GET['pedidoId'] ?? null;
            $token = $_GET['token'] ?? null;
            $payerId = $_GET['PayerID'] ?? null;

            (new PayPalController())->pagoExitoso($pedidoId, $token, $payerId);
        });




        Router::add('GET','/api/productos',function(){
            (new ApiProductoController())->obtenerTodos();
        });

        Router::add('GET','/api/productos/:id',function(int $id){
            (new ApiProductoController())->obtenerPorId($id);
        });

        Router::add('POST','/api/productos',function(){
            (new ApiProductoController())->crear();
        });

        Router::add('PUT','/api/productos/:id',function(int $id){
            (new ApiProductoController())->actualizar($id);
        });

        Router::add('DELETE','/api/productos/:id',function(int $id){
            (new ApiProductoController())->borrar($id);
        });



        Router::add('GET','/not-found',function(){
            ErrorController::show_Error404();
        });


        Router::dispatch();

    }

}