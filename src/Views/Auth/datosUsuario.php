<div class="container mt-4 datos">
    <h2>Datos de <?= $_SESSION['usuario']["nombre"] ?></h2>
    <ul class="list-group">
        <li class="list-group-item"><b>ID:</b> <?php echo $_SESSION['usuario']["id"]; ?></li>
        <li class="list-group-item"><b>Nombre:</b> <?php echo $_SESSION['usuario']["nombre"]; ?></li>
        <li class="list-group-item"><b>Apellidos:</b> <?php echo $_SESSION['usuario']["apellidos"]; ?></li>
        <li class="list-group-item"><b>Correo:</b> <?php echo $_SESSION['usuario']["email"]; ?></li>
        <li class="list-group-item"><b>Rol:</b> <?php echo $_SESSION['usuario']["rol"]; ?></li>
    </ul>

    <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3">Ir al inicio</a>
</div>