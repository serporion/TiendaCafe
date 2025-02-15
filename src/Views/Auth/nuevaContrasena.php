
<?php
if (isset($_SESSION['token_error'])):
    echo "<div class='alert alert-info'>" . $_SESSION['token_error'] . "</div>";
    echo "<p><a href=\"" . BASE_URL . "Auth/iniciarSesion\" class=\"btn btn-primary\">Volver</a></p>";
    unset($_SESSION['token_error']);
elseif (isset($_SESSION['sesion_token'])):
    echo "<div class='alert alert-danger'>" . $_SESSION['sesion_token'] . "</div>";
    echo "<p><a href=\"" . BASE_URL . "Auth/iniciarSesion\" class=\"btn btn-primary\">Volver</a></p>";
    unset($_SESSION['sesion_token']);
elseif (isset($_SESSION['mensaje_exito'])):
    echo "<div class='alert alert-info'>" . $_SESSION['mensaje_exito'] . "</div>";
    echo "<p><a href=\"" . BASE_URL . "Auth/iniciarSesion\" class=\"btn btn-primary\">Volver</a></p>";
    unset($_SESSION['mensaje_exito']);

elseif (isset($_SESSION['errores'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($_SESSION['errores'] as $campo => $mensaje): ?>
                <li><strong><?= htmlspecialchars($campo) ?>:</strong> <?= htmlspecialchars($mensaje) ?></li>
            <?php endforeach; ?>
        </ul>
        <p><a href="<?= BASE_URL ?>Auth/iniciarSesion" class="btn btn-primary">Volver</a></p>
    </div>
    <?php unset($_SESSION['errores']); ?>

<?php else: ?>


<div >
    <form class="container mt-4 contrasena" action="<?php echo htmlspecialchars(BASE_URL . 'Auth/regenerarContrasena'); ?>" method="post">
        <input type="hidden" name="data[token]" value="<?php echo htmlspecialchars($newToken); ?>">
        <h2>Restrablezca su contraseña</h2>
        <label for="correo">Correo electrónico:</label>
        <input type="email" id="email" name="data[email]" required>
        <br>
        <label for="contraseña">Nueva contraseña:</label>
        <input type="password" id="contrasena" name="data[contrasena]" required>
        <br>
        <label for="contraseñaRepetida">Repita contraseña:</label>
        <input type="password" id="contrasenaRepetida" name="data[contrasenaRepetida]" required>
        <br>
        <button type="submit">Restablecer contraseña</button>

    </form>
</div>
<?php endif; ?>


