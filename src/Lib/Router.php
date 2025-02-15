<?php
namespace Lib;

use Controllers\ErrorController;
use Lib\Pages;

class Router {

    private static $routes = [];

    public static function add(string $method, string $action, Callable $controller, string $role = 'todos'):void{
        $action = trim($action, '/'); //Quito la barra del final por si está.
        self::$routes[$method][$action] = ['handler' => $controller, 'role' => $role];
    }

    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        //$uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Elimina los parámetros GET


        // Eliminar BASE_URL del inicio de la URI
        $baseUrlPattern = '#^' . preg_quote(BASE_URL, '#') . '#';
        $action = preg_replace($baseUrlPattern, '', $uri);
        $action = trim($action, '/');

        $segments = explode('/', $action);
        $routeFound = false;
        $param = null;

        foreach (self::$routes[$method] as $route => $routeData) {
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
                    // Verificar el rol del usuario
                    if (self::checkPermissions($routeData['role'])) {
                        echo call_user_func($routeData['handler'], $param);
                    } else {
                        ErrorController::show_Error403(); // Acceso denegado
                    }
                    break;
                }
            }
        }

        if (!$routeFound) {
            ErrorController::show_Error404();
        }
    }

    private static function checkPermissions(string $role): bool {
        switch ($role) {
            case 'admin':
                return Utilidades::comprueboAdministrador();
            case 'usuario':
                return Utilidades::comprueboSesion();
            case 'todos':
                return true; // Acceso público
            default:
                return false; // Acceso denegado por defecto
        }
    }

    public static function index(): string { //void { //} string  { //: array
        $pages = new Pages();
        $pages->render('/public/index/');
        return "";
    }
}
