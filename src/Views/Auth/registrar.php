<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<div id="registrar" class="container mt-4">
    <?php if(isset($_SESSION['registrado'])): ?>

        <div class="alert alert-success" role="alert">Usuario registrado con éxito. Revise su correo.</div>

    <?php elseif(isset($_SESSION['falloDatos'])): ?>
        <div class="alert alert-danger" role="alert">Los datos no se han enviado correctamente</div>
        <p><a href="<?= BASE_URL ?>Auth/registrarUsuario" class="btn btn-primary">Volver</a></p>

    <?php elseif(isset($errores['login'])): ?>
        <div class="alert alert-danger" role="alert">Los datos no se han enviado correctamente</div>
        <div class="text-danger"><?= $errores['login']; ?></div>
        <p><a href="<?= BASE_URL ?>Auth/registrarUsuario" class="btn btn-primary">Volver</a></p>

    <?php else: ?>
        <h2>Formulario de Registro</h2>
        <form action="<?= BASE_URL ?>Auth/insertarUsuario" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="data[nombre]" id="nombre" class="form-control" value="<?=(isset($user))?$user->getNombre():""?>">
                <?php if (isset($errores['nombre'])): ?>
                    <div class="text-danger"><?= $errores['nombre']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos:</label>
                <input type="text" name="data[apellidos]" id="apellidos" class="form-control" value="<?=(isset($user))?$user->getApellidos():""?>">
                <?php if (isset($errores['apellidos'])): ?>
                    <div class="text-danger"><?= $errores['apellidos']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="text" name="data[email]" id="email" class="form-control" value="<?=(isset($user))?$user->getCorreo():""?>">
                <?php if (isset($errores['email'])): ?>
                    <div class="text-danger"><?= $errores['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="contrasena" class="form-label">Contraseña:</label>
                <input type="password" name="data[contrasena]" id="contrasena" class="form-control">
                <?php if (isset($errores['contrasena'])): ?>
                    <div class="text-danger"><?= $errores['contrasena']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña:</label>
                <input type="password" name="data[confirmar_contrasena]" id="confirmar_contrasena" class="form-control">
                <?php if (isset($errores['confirmar_contrasena'])): ?>
                    <div class="text-danger"><?= $errores['confirmar_contrasena']; ?></div>
                <?php endif; ?>
            </div>

            <?php if(isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                <button type="submit" class="btn btn-primary">Registrar</button>
            <?php elseif(!isset($_SESSION['usuario'])): ?>
                <button type="submit" class="btn btn-primary">Registrarse</button>
                <p>Si ya tienes una cuenta <a href="<?= BASE_URL ?>Auth/iniciarSesion">Inicia sesión</a></p>
            <?php endif; ?>

            <p><a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Volver al Inicio</a></p>
        </form>
    <?php endif; ?>
</div>
