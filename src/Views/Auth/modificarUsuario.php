
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$errores = $_SESSION['errores'] ?? [];

if(isset($_SESSION['valores'])){
    $valores = $_SESSION['valores'] ;
}

unset($_SESSION['errores'], $_SESSION['valores']);
?>

<div class="container mt-4 modificarUsuario">
    <h1>Editar Usuario</h1>
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
        <form role="form" id="myForm" action="<?= htmlspecialchars(BASE_URL . 'Auth/guardarUsuarios', ENT_QUOTES, 'UTF-8') ?>" method="post" enctype="multipart/form-data">

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
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="data[apellidos]" aria-describedby="apellidosHelp" required="true"
                       value="<?php echo htmlspecialchars(
                           isset($mi_usuario) ? $mi_usuario->getApellidos() :
                               (isset($_SESSION['usuario']['apellidos']) ? $_SESSION['usuario']['apellidos'] :
                                   ($valores['apellidos'] ?? ''))
                       ); ?>">
            </div>


            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="text" class="form-control" id="email" name="data[email]" aria-describedby="emailHelp" minLength="8" required="true"
                       value="<?php echo htmlspecialchars(
                           isset($mi_usuario) ? $mi_usuario->getCorreo() :
                               (isset($_SESSION['usuario']['correo']) ? $_SESSION['usuario']['correo'] :
                                   ($valores['email'] ?? ''))
                       ); ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Confirmado</label>
                <input type="text" class="form-control" id="confirmado" name="confirmado" aria-describedby="confirmadoHelp" minLength="8" required="true"
                       value="<?php echo htmlspecialchars(
                           isset($mi_usuario) ? ($mi_usuario->isConfirmado() ? 'Usuario Confirmado' : 'Usuario No Confirmado') :
                               (isset($_SESSION['usuario']['confirmado']) ? $_SESSION['usuario']['confirmado'] :
                                   ($valores['confirmado'] ?? ''))
                       ); ?>"
                       readonly>
            </div>


            <div class="mb-3 form-check">
                <input type="hidden" name="confirmado" value="0">
                <input type="checkbox" class="form-check-input" id="confirmado" name="data[confirmado]" value="1"
                <?php echo isset($mi_usuario) ? ($mi_usuario->isConfirmado() ? 'checked' : '') : ''; ?>>
                <label class="form-check-label" for="confirmado">Confirmado</label>
            </div>



            <div class="mb-3">
                <label for="password" class="form-label">Password </label>
                <input type="password" class="form-control" id="password" name="data[contrasena]" aria-describedby="passwordHelp" >
                <?php if (isset($errores['password'])): ?> <div class="text-danger"><?php echo htmlspecialchars($errores['password']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="passwordrepetida" class="form-label">Repite la Password</label>
                <input type="password" class="form-control" id="passwordrepetida" name="data[confirmar_contrasena]" aria-describedby="passwordHelp" >
                <?php if (isset($errores['passwordrepetida'])): ?> <div class="text-danger"><?php echo htmlspecialchars($errores['passwordrepetida']); ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
            <a class="btn btn-primary" href="<?=BASE_URL . 'Auth/extraer_todos'?>">Cancelar</a>
        </form>
    </div>
</div>

