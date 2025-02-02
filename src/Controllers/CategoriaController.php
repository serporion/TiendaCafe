<?php

namespace Controllers;

use Lib\Pages;
use Models\Categoria;
use Lib\Utilidades;
use Services\CategoriaService;
use Services\ProductoService;

/**
 * Clase para controlar las categorias de la tienda
 */
class CategoriaController {

    private Pages $pages;
    private Utilidades $utiles;
    private Categoria $categoria;
    private CategoriaService $categoriaService;
    private ProductoService $productoService;
    
    public function __construct() {
        $this->pages = new Pages();
        $this->utiles = new Utilidades();
        $this->categoria = new Categoria();
        $this->categoriaService = new CategoriaService();
        $this->productoService = new ProductoService();
    }

    /**
     * Metodo que devuelve las categorias que hay en la base de datos y 
     * renderiza la vista
     * @return void
     */
    public function categorias() {
        
        $admin = Utilidades::comprueboAdministrador(); 
        $categorias = $this->categoriaService->listarCategorias();
        $hayCategorias = !empty($categorias);

        $this->pages->render('Categoria/inicioCategorias',
        [
            'admin' => $admin,
            'categorias' => $categorias,
            'hayCategorias' => $hayCategorias    
        ]);

    }
    /**
     * Metodo para guardar una nueva categoria en la base de datos
     * y tras eso te devuelve a la vista con errores en el caso
     * de que haya.
     * @return void
     */
    public function almacenarCategoria() {
        //Obtener datos formularios, sanetizarlos y validarlos
        

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if(!$this->utiles->comprueboAdministrador()){
                header("Location: " . BASE_URL ."");
            }
            else{
                $this->pages->render('Categoria/formularioCategoria');
            }
            
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $data = $_POST['categoria'];

                $categoria = $this->categoria = Categoria::fromArray($data);
                
                $categoria->sanitizarDatos();

                $errores = $categoria->validarDatos();

            
                if (empty($errores)) {

                    $categoryData = [
                        'nombre' => $categoria->getNombre(),
                    ];

                    $resultado = $this->categoriaService->guardarCategoria($categoryData);

                    if ($resultado === true) {
                        $this->categorias();
                        exit;
                    } 
                    else {
                        $errores['db'] = "Error al guardar la categoria: " . $resultado;
                        $this->pages->render('Categoria/formularioCategoria', ["errores" => $errores]);
                    }
                } 
                else {
                    $this->pages->render('Categoria/formularioCategoria', ["errores" => $errores]);
                }
            
        }
    }

    /**
     * Metodo para mostrar los productos de una determinada categoria
     * @var id $id de la categoria a mostrar los productos
     * @return void
     */
    public function ProductXCategory(int $id){
        $productos = $this->productoService->mostrarProductosXCategoria($id);
        $hayProductos = !empty($productos);


        $this->pages->render('Producto/gestionProductos',
        [
            'admin' => $this->utiles->comprueboAdministrador(),
            'productos' => $productos,
            'hayProductos' => $hayProductos
        ]); 
    }
    
    /**
     * Metodo para actualizar los datos de una categoria y te devuelve
     * a la vista si no hay errores.
     * @return void
     */
    public function actualizarCategoria(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){


            if(!$this->utiles->comprueboAdministrador()){
                header("Location: " . BASE_URL ."");
            }
            else{

                unset($_SESSION['falloDatos']);
                unset($_SESSION['actualizado']);

                $categorias = $this->categoriaService->listarCategorias();

                $this->pages->render('Categoria/actualizarCategoria', ['categorias' => $categorias]);
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if($_POST['categoria']){

                $data = $_POST['categoria'];

                $categoria = $this->categoria = Categoria::fromArray($data);

                $idCategoriaCambiar = $_POST['categoriaSelect'];

                $categoria->sanitizarDatos();

                $errores = $categoria->validarDatos();

                if (empty($errores)) {
    
                    $categoryData = [
                        'nombre' => $categoria->getNombre(),
                    ];

                    $resultado = $this->categoriaService->actualizarCategoria($categoryData, $idCategoriaCambiar);

                    if ($resultado === true) {
                        $_SESSION['actualizado'] = true;
                        $this->pages->render('Categoria/actualizarCategoria');
                        exit;
                    } 
                    else {
                        $errores['db'] = "Error al actualizar la categoria: " . $resultado;
                        $this->pages->render('Categoria/actualizarCategoria', ["errores" => $errores]);
                    }
                } 
                else {
                    $this->pages->render('Categoria/actualizarCategoria', ["errores" => $errores]);
                }
            }
            else{
                $_SESSION['falloDatos'] = 'fallo';
                $this->pages->render('Categoria/actualizarCategoria');
            }
        }
    }
    
    /**
     * Metodo para borrar una categoria determinada y te devuelve a la vista 
     * si no hay errores.
     * @return void
     */
    public function borrarCategoria(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){


            if(!$this->utiles->comprueboAdministrador()){
                header("Location: " . BASE_URL ."");
            }
            else{
            
                unset($_SESSION['falloDatos']);
                unset($_SESSION['borrado']);

                $categorias = $this->categoriaService->listarCategorias();

                $this->pages->render('Categoria/borrarCategoria', ['categorias' => $categorias]);
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if($_POST['categoriaSelect']){

                $idCategoriaCambiar = $_POST['categoriaSelect'];

                $this->categoria->sanitizarBorrado($idCategoriaCambiar);

                $errores = $this->categoria->validarBorrado($idCategoriaCambiar);

                if (empty($errores)) {

                    if($this->productoService->contarProductosXCategoria($idCategoriaCambiar) <= 0){
                            $resultado = $this->categoriaService->borrarCategoria($idCategoriaCambiar);

                        if ($resultado === true) {
                            $_SESSION['borrado'] = true;
                            $this->pages->render('Categoria/borrarCategoria');
                            exit;
                        } 
                        else {
                            $errores['db'] = "Error al borrar la categoria: " . $resultado;
                            $this->pages->render('Categoria/borrarCategoria', ["errores" => $errores]);
                        }
                        
                    }
                    else{
                        $cambiarCategoria = $this->productoService->actualizarProductosXCategoria($idCategoriaCambiar);
                        if ($cambiarCategoria === true) {
                            $resultado = $this->categoriaService->borrarCategoria($idCategoriaCambiar);
                            if ($resultado === true) {
                                $_SESSION['borrado'] = true;
                                $this->pages->render('Categoria/borrarCategoria');
                                exit;
                            } 
                            else {
                                $errores['db'] = "Error al borrar la categoria: " . $resultado;
                                $this->pages->render('Categoria/borrarCategoria', ["errores" => $errores]);
                            }
                        } 
                        else {
                            $errores['db'] = "Error al cambiar la categoria de los productos: " . $cambiarCategoria;
                            $this->pages->render('Categoria/borrarCategoria', ["errores" => $errores]);
                        }
                    }
    
                } 
                else {
                    $this->pages->render('Categoria/borrarCategoria', ["errores" => $errores]);
                }
            }
            else{
                $_SESSION['falloDatos'] = 'fallo';
                $this->pages->render('Categoria/borrarCategoria');
            }
        }
    }
}
