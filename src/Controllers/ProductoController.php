<?php

namespace Controllers;

use Lib\Pages;
use Models\Producto;
use Lib\Utilidades;
use Services\ProductoService;
use Services\CategoriaService;
use Services\LineaPedidoService;


/**
 * Clase para controlar los productos
 */
class ProductoController {

    private Pages $pages;
    private Utilidades $utiles;
    private ProductoService $productoService;
    private LineaPedidoService $lineaPedidoService;
    private CategoriaService $categoriaService;
    
    public function __construct() {
        $this->pages = new Pages();
        $this->utiles = new Utilidades();
        $this->productoService = new ProductoService();
        $this->lineaPedidoService = new LineaPedidoService();
        $this->categoriaService = new CategoriaService();
    }

    /**
     * Metodo que saca los productos y los renderiza a la vista
     * @return void
     */
    public function inicio() {

        $admin = $this->utiles->comprueboAdministrador(); 
        $productos = $this->productoService->mostrarProductos();
        $hayProductos = !empty($productos);
    
        $this->pages->render('Producto/gestionProductos',
        [
            'admin' => $admin,
            'productos' => $productos,
            'hayProductos' => $hayProductos
        ]);    
    }
    

    /**
     * Metodo que guardar los productos en caso de no haber errores y renderiza la vista
     * @return void
     */

    public function guardarProductos() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utiles->comprueboAdministrador()) {
                header("Location: " . BASE_URL . "");
                exit;
            } else {
                unset($_SESSION['guardado']);
                $categorias = $this->categoriaService->listarCategorias();
                $this->pages->render('Producto/formularioProducto', [
                    'categorias' => $categorias
                ]);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];
            $imagenNombre = "";
            $rutaCarpeta = '../../public/img';
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];

            // Crear carpeta si no existe
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0777, true);
            }

            // Manejo de la imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tipoArchivo = mime_content_type($_FILES['imagen']['tmp_name']);
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    $errores['imagen'] = "El archivo debe ser un formato válido (JPEG, PNG o GIF).";
                } else {
                    $imagenNombre = uniqid() . '_' . basename($_FILES['imagen']['name']);
                    $rutaArchivo = rtrim($rutaCarpeta, '/') . '/' . $imagenNombre;

                    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo)) {
                        $errores['imagen'] = "No se pudo guardar el archivo de la imagen.";
                    }
                }
            } else if (isset($_FILES['imagen']['error']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errores['imagen'] = "Error al cargar la imagen: " . $_FILES['imagen']['error'];
            }

            // Crear instancia del producto
            $producto = new Producto(
                null,
                $_POST['categoria'],
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['precio'],
                $_POST['stock'],
                $_POST['oferta'] ?? 0,
                $_POST['fecha'],
                $imagenNombre
            );

            // Sanitizar datos
            $producto->sanitizarDatos();

            // Validar datos

            $errores = array_merge($errores, $producto->validarDatosProductos());

            if (empty($errores)) {
                // Preparar datos para guardar
                $productData = [
                    'categoria_id' => $producto->getCategoriaId(),
                    'nombre' => $producto->getNombre(),
                    'descripcion' => $producto->getDescripcion(),
                    'precio' => $producto->getPrecio(),
                    'stock' => $producto->getStock(),
                    'oferta' => $producto->getOferta(),
                    'fecha' => $producto->getFecha(),
                    'imagen' => $producto->getImagen(),
                ];

                // Guardar producto
                $resultado = $this->productoService->guardarProductos($productData);

                if ($resultado === true) {
                    $_SESSION['guardado'] = true;
                    header("Location: " . BASE_URL . "Producto/inicio");
                    exit;
                } else {
                    $errores['db'] = "Error al guardar el producto: " . $resultado;
                    $this->pages->render('Producto/formularioProducto', [
                        "errores" => $errores,
                        "categorias" => $this->categoriaService->listarCategorias()
                    ]);
                }
            } else {
                // Renderizar formulario con errores
                $this->pages->render('Producto/formularioProducto', [
                    "errores" => $errores,
                    "categorias" => $this->categoriaService->listarCategorias()
                ]);
            }
        }
    }


    /**
     * Metodo que obtiene los detalles de un producto
     * @var id id del producto al que obtener los detalles
     * @return void
     */
    public function detalleProducto(int $id){
        $details = $this->productoService->detalleProducto($id);

        //die(var_dump(($details)));

        $this->pages->render('Producto/detalleProducto',
        [
            'admin' => $this->utiles->comprueboAdministrador(),
            'details' => $details    
        ]); 
    }

    /**
     * Metodo que borrar  un producto
     * @var id id del producto a borrar
     * @return void
     */
    public function borrarProducto (int $id){
        if(!$this->utiles->comprueboAdministrador()){
            header("Location: " . BASE_URL ."");
        }
        else{
            $lines = $this->lineaPedidoService->verProductoLineaPedido($id);
            if($lines > 0){
                $update = $this->productoService->updateCategoryProduct($id);
                if ($update === true) {
                    $_SESSION['productoEliminado'] = true;
                    header("Location: " . BASE_URL ."");
                } 
                else {
                    $_SESSION['falloDatos'] = 'fallo';
                    $this->pages->render('Producto/detalleProducto/$id');
                }
            }
            else{
                $resultado = $this->productoService->borrarProducto($id);

                if ($resultado === true) {
                    $_SESSION['productoEliminado'] = true;
                    header("Location: " . BASE_URL ."");
                    exit;
                } 
                else {
                    $_SESSION['falloDatos'] = 'fallo';
                    $this->pages->render('Producto/detalleProducto/$id');
                }
            }

            
        }
                    
    }

    /**
     * Metodo que actualiza los datos de un producto
     * @var id id del producto aactualizar
     * @return void
     */
    public function actualizarProducto(int $id){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if(!$this->utiles->comprueboAdministrador()){
                header("Location: " . BASE_URL ."");
            }
            else{

                unset($_SESSION['actualizado']);

                $categorias = $this->categoriaService->listarCategorias();
                $producto = $this->productoService->detalleProducto($id);


                $this->pages->render('Producto/formularioActualizacion',
                [
                    'categorias' => $categorias,
                    'product' => $producto
                ]);
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            

            $imagenNombre = '';
            $rutaCarpeta = '../../public/img';
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];


            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0777, true); 
            }

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tipoArchivo = mime_content_type($_FILES['imagen']['tmp_name']); 
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    $errores['imagen'] = "El archivo debe ser una formato válido (JPEG, PNG o GIF).";
                } else {
                    $imagenNombre = basename($_FILES['imagen']['name']);
                    $rutaArchivo = rtrim($rutaCarpeta, '/') . '/' . $imagenNombre;
    
                    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo)) {
                        $errores['imagen'] = "No se pudo guardar el archivo de la imagen.";
                    }
                }
            } else if (isset($_FILES['imagen']['error']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errores['imagen'] = "Error al cargar la imagen: " . $_FILES['imagen']['error'];
            }

            $product = $this->productoService->detalleProducto($id);

            if($imagenNombre === ''){
                $imagenNombre = $product[0]['imagen'];
            }


                $producto = new Producto(
                    null,
                    $_POST['categoria'],
                    $_POST['nombre'],
                    $_POST['descripcion'],
                    $_POST['precio'],
                    $_POST['stock'],
                    $_POST['oferta'],
                    "",
                    $imagenNombre
                );

                // Sanitizar datos
                $producto->sanitizarDatos();
                
                // Validar datos
                $errores = $producto->validateUpdate();

                if (empty($errores)) {
                    
                    $productData = [
                        'categoria_id' => $producto->getCategoriaId(),
                        'nombre' => $producto->getNombre(),
                        'descripcion' => $producto->getDescripcion(),
                        'precio' => $producto->getPrecio(),
                        'stock' => $producto->getStock(),
                        'oferta' => $producto->getOferta(),
                        'fecha' => $producto->getFecha(),
                        'imagen' => $producto->getImagen(),
                    ];

                    $resultado = $this->productoService->actualizarProducto($productData, $id);

                    if ($resultado === true) {
                        $_SESSION['actualizado'] = true;
                        $this->pages->render('Producto/formularioActualizacion');
                        exit;
                    } 
                    else {
                        $errores['db'] = "Error al actualizar el producto: " . $resultado;
                        $this->pages->render('Producto/formularioActualizacion', ["errores" => $errores]);
                    }
                } 
                else {
                    $this->pages->render('Producto/formularioActualizacion', ["errores" => $errores]);
                }
            
        }
    }

}
