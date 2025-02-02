<div id="inicioSesion" class="container mt-4">
    <h2>Formulario de Inicio de Sesión</h2>
    <form action="<?= BASE_URL ?>Auth/iniciarSesion" method="POST">
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" class="form-control" value="<?php echo $_POST['correo'] ?? ''; ?>">
            <?php if (isset($errores['correo'])): ?>
                <div class="text-danger"><?= $errores['correo']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" class="form-control">
            <?php if (isset($errores['contrasena'])): ?>
                <div class="text-danger"><?= $errores['contrasena']; ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($errores['login'])): ?>
            <div class="text-danger"><?= $errores['login']; ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>

        <p>Si no tienes una cuenta creada <a href="<?= BASE_URL ?>Auth/registrarUsuario">Regístrate</a></p>

        <p><a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Volver al Inicio</a></p>
    </form>
</div>