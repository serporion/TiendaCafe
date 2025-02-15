<?php
namespace Controllers;

use Repositories\ProductoRepository;
use Models\Producto;
use Lib\Validar;

class ApiProductoController {

    private ProductoRepository $productoRepository;

    public function __construct() {
        $this->productoRepository = new ProductoRepository();
    }

    /**
     * Obtiene y devuelve todos los productos en formato JSON.
     *
     * @return void Muestra la lista de productos en formato JSON
     * directamente en el cuerpo de la respuesta.
     */
    public function obtenerTodos() {

        $productos = $this->productoRepository->mostrarProductos();
        header('Content-Type: application/json');
        echo json_encode($productos);
    }

    /**
     * Obtiene y devuelve los detalles de un producto específico por su ID en formato JSON.
     *
     * @param int $id El identificador del producto a recuperar.
     * @return void Envía los detalles del producto en formato JSON directamente al cuerpo de
     * la respuesta o un mensaje de error con un código de estado 404 si no se encuentra el producto.
     */
    public function obtenerPorId($id) {

        if (!Validar::validateInt($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $producto = $this->productoRepository->detalleProducto((int) $id);

        header('Content-Type: application/json');

        if ($producto) {
            echo json_encode($producto);

        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
        }
    }


    /**
     *  Método que crea un nuevo producto procesando datos de entrada, validándolos
     *  y guardándolos en el repositorio. Genera una respuesta JSON.
     *
     *  @return void Genera un mensaje de éxito con el código HTTP 201
     *  o un mensaje de error con el código de estado HTTP apropiado.
     */
    public function crear() {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        //Validación
        if (
            !isset($data['categoria_id'], $data['nombre'], $data['precio'], $data['stock']) ||
            !Validar::validateInt($data['categoria_id']) ||
            !Validar::validateString($data['nombre']) ||
            !Validar::validateDouble($data['precio']) ||
            !Validar::validateInt($data['stock'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos o incompletos']);
            return;
        }

        //Sanitización
        $categoria_id = Validar::sanitizeInt($data['categoria_id']);
        $nombre = Validar::sanitizeString($data['nombre']);
        $descripcion = isset($data['descripcion']) ? Validar::sanitizeString($data['descripcion']) : '';
        $precio = Validar::sanitizeDouble($data['precio']);
        $stock = Validar::sanitizeInt($data['stock']);
        $oferta = isset($data['oferta']) ? Validar::sanitizeString($data['oferta']) : '';
        $imagen = isset($data['imagen']) ? Validar::sanitizeString($data['imagen']) : 'default.jpg';


        $producto = new Producto();

        $producto->setCategoriaId($categoria_id);
        $producto->setNombre($nombre);
        $producto->setDescripcion($descripcion);
        $producto->setPrecio($precio);
        $producto->setStock($stock);
        $producto->setOferta($oferta);
        $producto->setFecha(date('Y-m-d H:i:s'));
        $producto->setImagen($imagen);

        $resultado = $this->productoRepository->guardarProductos($producto);

        if ($resultado === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Producto creado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear: ' . $resultado]);
        }
    }

    /**
     * Actualiza un producto existente identificado por su ID con los datos proporcionados.
     *
     * @param int $id El identificador único del producto que se actualizará.
     * @return void Genera una respuesta que indica el éxito o el fracaso de la operación de actualización.
     */
    public function actualizar(int $id) {

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (
            !isset($data['categoria_id'], $data['nombre'], $data['precio'], $data['stock']) ||
            !Validar::validateInt($data['categoria_id']) ||
            !Validar::validateString($data['nombre']) ||
            !Validar::validateDouble($data['precio']) ||
            !Validar::validateInt($data['stock'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos o incompletos']);
            return;
        }


        //Sanitización
        $categoria_id = Validar::sanitizeInt($data['categoria_id']);
        $nombre = Validar::sanitizeString($data['nombre']);
        $descripcion = isset($data['descripcion']) ? Validar::sanitizeString($data['descripcion']) : '';
        $precio = Validar::sanitizeDouble($data['precio']);
        $stock = Validar::sanitizeInt($data['stock']);
        $oferta = isset($data['oferta']) ? Validar::sanitizeString($data['oferta']) : '';
        $imagen = isset($data['imagen']) ? Validar::sanitizeString($data['imagen']) : 'default.jpg';


        $producto = new Producto();

        $producto->setCategoriaId($categoria_id);
        $producto->setNombre($nombre);
        $producto->setDescripcion($descripcion);
        $producto->setPrecio($precio);
        $producto->setStock($stock);
        $producto->setOferta($oferta);
        $producto->setFecha(date('Y-m-d H:i:s'));
        $producto->setImagen($imagen);

        $resultado = $this->productoRepository->actualizarProducto($producto, $id);

        if ($resultado === true) {
            http_response_code(200);
            echo json_encode(['message' => 'Producto actualizado']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar: ' . $resultado]);
        }
    }


    /**
     * Elimina un producto existente identificado por su ID.
     *
     * @param int $id El identificador único del producto que se desea eliminar.
     * @return void Genera una respuesta que indica el éxito o el fracaso de la
     * operación de eliminación.
     */
    public function borrar($id) {

        if (!Validar::validateInt($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $resultado = $this->productoRepository->borrarProducto((int) $id);

        if ($resultado === true) {
            http_response_code(200);
            echo json_encode(['message' => 'Producto borrado']);

        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Error al borrar o producto no encontrado']);
        }
    }

}

