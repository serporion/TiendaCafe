<?php
include_once dirname(__DIR__, 3)."/config/config.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-..." crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="<?=BASE_URL?>public/css/index.css">
    <title>Tienda del CAFE</title>
</head>
<body>
    
<header class="bg-light">
    <div class="container header">
        <h1 id="logo"><a href="<?= BASE_URL?>">Tiempo de <span class="logo__bold">CAFE</span></a></h1>
        <nav class="menu">
            <ul class="nav">
                <?php if(isset($_SESSION['usuario'])):  ?>
                    <li class="nav-item">Hola <?= $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellidos'] ?></li>
                <?php endif;?>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL?>">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL?>Producto/inicio">Productos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL?>Categoria/categorias">Categorías</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL?>Carrito/cargarCarrito">Carrito</a></li>

                <?php if(!isset($_SESSION['usuario'])):  ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL?>Auth/registrarUsuario">Registrarse</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL?>Auth/iniciarSesion">Iniciar Sesión</a></li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Usuario
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if(isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === "admin"):?>
                                <li><a class="dropdown-item" href="<?= BASE_URL?>Auth/extraer_todos">Listar Usuarios</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL?>Auth/registrarUsuario">Registrar Usuarios</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL?>Producto/guardarProductos">Añadir producto</a></li>
                            <?php endif;?>
                            <li><a class="dropdown-item" href="<?= BASE_URL?>Auth/verTusDatos">Ver tus datos</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL?>Pedido/Pedidos">Ver mis pedidos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL?>Auth/logout">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php endif;?>
            </ul>
        </nav>

    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

        
<div id="tienda"> 
        
   
