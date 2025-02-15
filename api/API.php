<?php


header('Content-Type: application/json');
//Habilitar el acceso CORS(politicas de seguridad). Necesario para que no haya problemas.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


$archivo_json = 'productos.json';

/**
 * Funcion para leer los datos del archivo JSON.
 * @return array
 */
function leerProductosDesdeJSON(): array {
    global $archivo_json;
    if (file_exists($archivo_json)) {
        $json_data = file_get_contents($archivo_json);
        return json_decode($json_data, true) ?: [];
    } else {
        return [];
    }
}

/**
 * Funcion para guardar los datos en el archivo JSON.
 * @param array $productos
 * @return void
 */
function guardarProductosEnJSON(array $productos): void {
    global $archivo_json;
    $json_data = json_encode($productos, JSON_PRETTY_PRINT);
    file_put_contents($archivo_json, $json_data);
}


// Obtener la ruta solicitada desde la URL
$ruta = $_SERVER['REQUEST_URI'];

// Obtener el método HTTP utilizado
$metodo = $_SERVER['REQUEST_METHOD'];

// Analizar la ruta para extraer el ID del producto (si está presente)
$partes_ruta = explode('/', trim($ruta, '/'));
$endpoint = $partes_ruta[0]; //El primer elemento será el endpoint principal
$id = $partes_ruta[1] ?? null; //El segundo elemento será el id si existe
$categoria_id = $partes_ruta[2] ?? null; //El tercer elemento será el id de categoria si existe

// Filtrar los productos por categoría si categoria_id está presente en la URL.
if ($endpoint === 'productos' && $metodo === 'GET' && $categoria_id !== null) {
    $productos = leerProductosDesdeJSON();
    $productosFiltrados = array_filter($productos, function ($producto) use ($categoria_id) {
        return isset($producto['categoria_id']) && $producto['categoria_id'] == $categoria_id;
    });
    echo json_encode(array_values($productosFiltrados)); // Retorna los productos filtrados por categoria_id
    exit;
}
//Enrutamiento básico
switch ($metodo) {
    case 'GET':
        // Obtener todos los productos
        if ($endpoint === 'productos' && $id === null) {
            $productos = leerProductosDesdeJSON();
            echo json_encode($productos);
            //Obtener un producto especifico por el id
        } elseif ($endpoint === 'productos' && $id !== null) {
            $productos = leerProductosDesdeJSON();
            $producto = array_filter($productos, fn($producto) => $producto['id'] == $id);
            echo json_encode(array_shift($producto));
        } else {
            http_response_code(404);
            echo json_encode(['mensaje' => 'Ruta no encontrada']);
        }
        break;
    case 'POST':
        // Crear un nuevo producto
        if ($endpoint === 'productos') {
            // Obtener los datos del producto desde el cuerpo de la solicitud
            $datos = json_decode(file_get_contents("php://input"), true);
            if ($datos) {
                $productos = leerProductosDesdeJSON();
                // Asignar un nuevo ID al producto
                $nuevo_id = count($productos) > 0 ? max(array_column($productos, 'id')) + 1 : 1;
                $datos['id'] = $nuevo_id;
                // Agregar el nuevo producto al array de productos
                $productos[] = $datos;
                // Guardar el array actualizado en el archivo JSON
                guardarProductosEnJSON($productos);
                http_response_code(201); // Creado
                echo json_encode(['mensaje' => 'Producto creado con éxito', 'id' => $nuevo_id]);
            } else {
                http_response_code(400); // Solicitud incorrecta
                echo json_encode(['mensaje' => 'Datos de producto no válidos']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['mensaje' => 'Ruta no encontrada']);
        }
        break;
    case 'PUT':
        // Actualizar un producto existente
        if ($endpoint === 'productos' && $id !== null) {
            // Obtener los datos actualizados del producto desde el cuerpo de la solicitud
            $datos = json_decode(file_get_contents("php://input"), true);
            if ($datos) {
                $productos = leerProductosDesdeJSON();
                // Buscar el índice del producto a actualizar
                $indice = array_search($id, array_column($productos, 'id'));
                if ($indice !== false) {
                    // Actualizar los datos del producto
                    $productos[$indice] = array_merge($productos[$indice], $datos);
                    // Guardar el array actualizado en el archivo JSON
                    guardarProductosEnJSON($productos);
                    echo json_encode(['mensaje' => 'Producto actualizado con éxito']);
                } else {
                    http_response_code(404); // No encontrado
                    echo json_encode(['mensaje' => 'Producto no encontrado']);
                }
            } else {
                http_response_code(400); // Solicitud incorrecta
                echo json_encode(['mensaje' => 'Datos de producto no válidos']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['mensaje' => 'Ruta no encontrada']);
        }
        break;
    case 'DELETE':
        // Borrar un producto existente
        if ($endpoint === 'productos' && $id !== null) {
            $productos = leerProductosDesdeJSON();
            // Buscar el índice del producto a borrar
            $indice = array_search($id, array_column($productos, 'id'));
            if ($indice !== false) {
                // Eliminar el producto del array
                array_splice($productos, $indice, 1);
                // Guardar el array actualizado en el archivo JSON
                guardarProductosEnJSON($productos);
                echo json_encode(['mensaje' => 'Producto borrado con éxito']);
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(['mensaje' => 'Producto no encontrado']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['mensaje' => 'Ruta no encontrada']);
        }
        break;
    default:
        http_response_code(405); // Método no permitido
        echo json_encode(['mensaje' => 'Método no permitido']);
        break;
}

