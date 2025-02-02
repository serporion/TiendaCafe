<?php

namespace Services;

use Models\Producto;
use Repositories\ProductoRepository;

/**
 * Clase que recibe peticiones de ProductoController y se pone en contacto con ProductoRepository.
 */
class ProductoService {

    private ProductoRepository $productoRepository;

    public function __construct() {
        $this->productoRepository = new ProductoRepository();
    }

    /**
     * Metodo que llama al repository para guardar un producto.
     * @var array $productData los datos del producto a guardar.
     * @return bool|string
     */
    public function guardarProductos(array $productData): bool|string {
        try {
            $producto = new Producto(
                null,
                $productData['categoria_id'],
                $productData['nombre'],
                $productData['descripcion'],
                $productData['precio'],
                $productData['stock'],
                $productData['oferta'],
                $productData['fecha'],
                $productData['imagen']
            );

            return $this->productoRepository->guardarProductos($producto);
        } 
        catch (\Exception $e) {
            error_log("Error al guardar el producto: " . $e->getMessage());
            return false;
        }
    }

     /**
      * Método que llama a repository para ver todos los productos.
      * @return array
      */
    public function mostrarProductos(): array {
        return $this->productoRepository->mostrarProductos();
    }

     /**
      * Método que llama a repository para ver todos los productos de una categoria.
      * @var int $id con el id de la categoria de la que mostrar los productos
      * @return array
      */
    public function mostrarProductosXCategoria(int $id): array {
        return $this->productoRepository->mostrarProductosXCategoria($id);
    }


     /**
      * Método que llama a repository para contar todos los productos de una categoria.
      * @var int $id con el id de la categoria de la que contar los productos
      * @return int
      */
    public function contarProductosXCategoria(int $id): int {
        return $this->productoRepository->contarProductosXCategoria($id);
    }

    /**
      * Método que llama a repository para actualizar los productos de la categoria a borrar.
      * @var int $id con el id de la categoria de la que actualizar los productos
      * @return bool|string
      */
    public function actualizarProductosXCategoria(int $id): bool|string {
        try {

            return $this->productoRepository->actualizarProductosXCategoria($id);
        } 
        catch (\Exception $e) {
            error_log("Error al cambiar la categoria de los productos: " . $e->getMessage());
            return false;
        }
    }

    /**
      * Método que llama a repository para ver todos los datos de un producto.
      * @var int $id con el id del producto del que tomar los detallers.
      * @return array
      */
    public function detalleProducto(int $id): array {
        return $this->productoRepository->detalleProducto($id);
    }

    /**
      * Método que llama a repository para borrar un producto.
      * @var int $id con el id del producto a borrar.
      * @return bool
      */
    public function borrarProducto(int $id): bool {
        return $this->productoRepository->borrarProducto($id);
    }

    /**
      * Método que llama a repository para actualizar un producto.
      * @var array $productData con los datos a actualizar.
      * @var int $id con el id del producto a actualizar.
      * @return bool|string
      */
     public function actualizarProducto(array $productData, int $id): bool|string {
        try {
            $producto = new Producto(
                null,
                $productData['categoria_id'],
                $productData['nombre'],
                $productData['descripcion'],
                $productData['precio'],
                $productData['stock'],
                $productData['oferta'],
                $productData['fecha'],
                $productData['imagen']
            );

            return $this->productoRepository->actualizarProducto($producto, $id);
        } 
        catch (\Exception $e) {
            error_log("Error al actualizar el producto: " . $e->getMessage());
            return false;
        }
    }

    /**
      * Método que llama a repository para actualizar el stock de un producto.
      * @return bool|string
      */
    public function updateStockProduct(): bool|string {
        
        return $this->productoRepository->updateStockProduct();
        
    }


    /**
      * Método que llama a repository para actualizar la categoria de un producto que
      * va a ser borrado.
      * @var int $id con el id del producto de la que actualizar la categoria
      * @return bool|string
      */
      public function updateCategoryProduct(int $id): bool|string {
        try {

            return $this->productoRepository->updateCategoryProduct($id);
        } 
        catch (\Exception $e) {
            error_log("Error al cambiar la categoria de los productos: " . $e->getMessage());
            return false;
        }
    }

}