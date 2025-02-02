<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<?php if (isset($mensajeInicioSesion) && $mensajeInicioSesion): ?>
    <div id="product" class="container mt-4 pedidoForm">
        <h2>Debes iniciar sesión</h2>
        <p><a href="<?= BASE_URL ?>Auth/iniciarSesion" class="btn btn-primary">Inicia Sesión</a></p>
    </div>
<?php else: ?>



<div id="product" class="container mt-4 pedidoForm">

<?php 
    if(isset($_SESSION['order'])): 
?>
    <div class="alert alert-success" role="alert">Pedido realizado con éxito</div>
        <?php if(isset($_SESSION['mailOk']) && $_SESSION['mailOk']): ?>
        <div class="alert alert-info" role="alert">
            Revise su correo, le hemos enviado el pedido a su buzón.
        </div>
    <?php elseif(isset($_SESSION['mailOk']) && !$_SESSION['mailOk']): ?>
        <div class="alert alert-warning" role="alert">
            El pedido se ha realizado, pero hubo un problema al enviar el correo.
        </div>
    <?php endif; ?>
        <p><a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Volver</a></p>
        <?php
        unset($_SESSION['order']);
        unset($_SESSION['mailOk']);
        ?>
    <?php else: ?>


    <h2>Realizar pedido</h2>
    <form action="<?= BASE_URL ?>Pedido/guardarPedido" method="POST">

        <div class="mb-3">
            <label for="provincia" class="form-label">Provincia:</label>
            <input type="text" name="provincia" id="provincia" class="form-control" value="<?= $_POST['provincia'] ?? '' ?>">
            <?php if (isset($errores['provincia'])): ?>
                <div class="text-danger"><?= $errores['provincia'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="localidad" class="form-label">Localidad:</label>
            <input type="text" name="localidad" id="localidad" class="form-control" value="<?= $_POST['localidad'] ?? '' ?>">
            <?php if (isset($errores['localidad'])): ?>
                <div class="text-danger"><?= $errores['localidad'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección:</label>
            <input type="text" name="direccion" id="direccion" class="form-control" value="<?= $_POST['direccion'] ?? '' ?>">
            <?php if (isset($errores['direccion'])): ?>
                <div class="text-danger"><?= $errores['direccion'] ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($errores['db'])): ?>
            <div class="text-danger"><?= $errores['db'] ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Realizar pedido</button>

        <a href="<?= BASE_URL ?>" class="btn btn-primary">Volver a inicio</a>
    </form>
<?php 
    endif;
?>
</div>
<?php endif; ?>