<?php
     if(isset($_SESSION['errorPedido']))
     {
         echo "<div class='alert alert-info'>" . $_SESSION['errorPedido'] . "</div>";
         unset($_SESSION['errorPedido']);
     }

?>

<body>

<h2 class="container mt-4 listar h2">Lista de Usuarios</h2>

<?php if (empty($todos_los_usuarios)) : ?>
    <p>No hay usuarios disponibles.</p>
<?php else : ?>

    <div class="table-responsive">
        <table id="listarUsuarios" style="width:100%;">
            <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 15%;">Nombre</th>
                <th style="width: 15%;">Apellidos</th>
                <th style="width: 20%;">Correo</th>
                <th style="width: 15%;">Fecha Expira</th>
                <th style="width: 5%;">Confirmado</th>
                <th style="width: 10%;">Rol</th>
                <th colspan="2" style="width: 10%;">Administrar</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($todos_los_usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario->getId()) ?></td>
                    <td><?= htmlspecialchars($usuario->getNombre()) ?></td>
                    <td><?= htmlspecialchars($usuario->getApellidos()) ?></td>
                    <td><?= htmlspecialchars($usuario->getCorreo()) ?></td>
                    <td><?= htmlspecialchars($usuario->getFechaExpiracion() ? $usuario->getFechaExpiracion()->format('Y-m-d H:i:s') : '') ?></td>
                    <td><?= htmlspecialchars($usuario->isConfirmado()) ?></td>
                    <td><?= htmlspecialchars($usuario->getRol()) ?></td>
                    <td>
                        <a class="enlace" href="<?= BASE_URL?>Auth/editarUsuario/<?= htmlspecialchars($usuario->getId()) ?>">Usuario</a>
                    </td>
                    <td>
                        <a class="enlace" href="<?=BASE_URL?>Pedido/verPedidoXUsuario/<?= urlencode($usuario->getId()) ?>">Pedidos</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif;

?>
</body>
