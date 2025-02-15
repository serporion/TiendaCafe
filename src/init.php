
<?php

session_start();

use Routes\Routes;
use Lib\Utilidades;

require_once dirname(__DIR__ ) . '/vendor/autoload.php';
require_once dirname(__DIR__ ) . '/config/config.php';



$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv -> safeLoad();

// Exporta las variables al entorno del sistema
foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
}


$utilidad = new Utilidades();
set_error_handler([$utilidad, "manejadorWarning"], E_WARNING);


Routes::index();



