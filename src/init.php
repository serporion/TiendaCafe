
<?php

session_start();

use Routes\Routes;

//require_once  '../src/views/layout/header.php';

require_once dirname(__DIR__ ) . '/vendor/autoload.php';
require_once dirname(__DIR__ ) . '/config/config.php';


$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv -> safeLoad();


Routes::index();

//require_once '../src/views/layout/footer.php';

