<?php

use Lib\Utilidades;

if (isset($_SESSION['modificaPedido'])) {
    echo "<div class='alert alert-info'>" . $_SESSION['modificaPedido'] . "</div>";
    unset($_SESSION['modificaPedido']);
}

?>

<div class="container mt-4 pedido">
    <ul class="list-group">

        <?php if (empty($orders)): ?>
            <h2>Aún no tiene pedidos realizados</h2>
            <p><a href="<?php echo Utilidades::comprueboAdministrador() ? BASE_URL . 'Auth/extraer_todos' : BASE_URL; ?>" class="btn btn-primary">Volver</a></p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <li class="list-group-item">
                    <table class="table table-bordered detallesPedidos">
                        <thead>
                        <tr>
                            <th>Nº de Pedido</th>
                            <th>Total Pedido</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Dirección</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="numeroPedido"><?= $order['id'] ?></td>
                            <td><?= number_format($order['coste'], 2) ?>€</td>
                            <td><?= $order['estado'] ?></td>
                            <td><?= $order['fecha'] ?></td>
                            <td><?= $order['direccion'] ?></td>
                        </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered lineasPedidos">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Coste Unitario</th>
                            <th>Total Producto</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ordersLine as $lineas): ?>
                            <?php foreach ($lineas as $line): ?>
                                <?php if ($line['pedido_id'] == $order['id']): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            // Obtener el nombre del producto desde la sesión
                                            $productId = $line['producto_id'];
                                            echo isset($_SESSION['productsOrders'][$productId])
                                                ? $_SESSION['productsOrders'][$productId]
                                                : 'Producto desconocido';
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($line['unidades']) ?></td>
                                        <td><?= number_format($line['precio'], 2) ?>€</td>
                                        <td><?= number_format($line['precio'] * $line['unidades'], 2) ?>€</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="botonesPedidos">
                        <?php if (Utilidades::comprueboAdministrador()): ?>
                            <a href="<?= BASE_URL ?>Pedido/modificarPedido/<?= urlencode($order['id']) ?>" class="btn btn-primary">Modificar</a>
                        <?php endif; ?>

                        <?php if (!$order['pagado']): ?>
                            <a href="<?= BASE_URL ?>PayPal/iniciarPago/<?= urlencode($order['id']) ?>" class="btn btn-success">Pagar con PayPal</a>
                        <?php else: ?>
                            <span class="badge bg-success">Pagado</span>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>


