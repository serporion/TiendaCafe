
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    $errores = $_SESSION['errores'] ?? [];
    $correoRestablecer = $_SESSION['correoRestablecer'] ?? '';
    if(isset($_SESSION['valores'])){
        $valores = $_SESSION['valores'] ;
    }

    unset($_SESSION['errores'], $_SESSION['valores'], $_SESSION['correoRestablecer']);

?>

<div class="container mt-4 modificarUsuario">
    <h1>Restablezca su contraseña</h1>
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="col-md-12 well">
            <form role="form" id="myForm" action="<?=htmlspecialchars(BASE_URL)?>Auth/envioCorreoRestablecerContrasena" method="post" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="data[nombre]" aria-describedby="nombreHelp" minLength="4" required="true"
                       value="<?php echo htmlspecialchars(
                           isset($mi_usuario) ? $mi_usuario->getNombre() :
                               (isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] :
                                   ($valores['nombre'] ?? ''))
                       ); ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="text" class="form-control" id="email" name="data[email]" aria-describedby="emailHelp" minLength="8" required="true"
                       value="<?php echo htmlspecialchars(
                           isset($mi_usuario) ? $mi_usuario->getCorreo() :
                               (isset($_SESSION['usuario']['correo']) ? $_SESSION['usuario']['correo'] :
                                   ($correoRestablecer ?? '')) // Mostrar el correo electrónico recuperado
                       ); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
            <a class="btn btn-primary" href="<?=BASE_URL . 'Auth/iniciarSesion'?>">Cancelar</a>
        </form>
    </div>
</div>

