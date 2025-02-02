<?php
namespace Lib;

use Controllers\ErrorController;
use Lib\Pages;


class Router {

    //
    private static $routes = [];

    public static function add(string $method, string $action, Callable $controller):void{

        $action = trim($action, '/'); //Quito la barra del final por si está.

        self::$routes[$method][$action] = $controller;

    }

    // Este método se encarga de obtener el sufijo de la URL que permitirá seleccionar
    // la ruta y mostrar el resultado de ejecutar la función pasada al metodo add para esa ruta
    // usando call_user_func()
    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Eliminar BASE_URL del inicio de la URI
        $baseUrlPattern = '#^' . preg_quote(BASE_URL, '#') . '#';
        $action = preg_replace($baseUrlPattern, '', $uri);
        $action = trim($action, '/');

        $segments = explode('/', $action);
        $routeFound = false;
        $param = null;

        foreach (self::$routes[$method] as $route => $handler) {
            $routeSegments = explode('/', $route);
            if (count($segments) === count($routeSegments)) {
                $match = true;
                for ($i = 0; $i < count($segments); $i++) {
                    if ($routeSegments[$i] !== $segments[$i] && $routeSegments[$i] !== ':id') {
                        $match = false;
                        break;
                    }
                    if ($routeSegments[$i] === ':id') {
                        $param = $segments[$i];
                    }
                }
                if ($match) {
                    $routeFound = true;
                    echo call_user_func($handler, $param);
                    break;
                }
            }
        }

        if (!$routeFound) {
            ErrorController::show_Error404();
        }
    }


    public static function index(): string { //void { //} string  { //: array
        $pages = new Pages();
        $pages->render('/public/index/');

        return "";
        //$this->pages ->render('src/init.php');
        //return "<p>Hola</p>";
    }
}
