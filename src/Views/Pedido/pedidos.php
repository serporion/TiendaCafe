<div class="container mt-4 pedido">
    <ul class="list-group">
        <?php if(empty($orders)): ?>
            <h2>Aun no tiene pedidos realizados</h2>
        <?php else: ?>
        <?php
            //var_dump($orders);
        //var_dump($ordersLine);
        //die();
        ?>
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
                                <td><?= $order['coste'] ?>€</td>
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
                                <th>Coste Individual</th>
                                <th>Total Producto</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ordersLine as $line):
                            foreach($line as $singerline):
                                if ($singerline['pedido_id'] == $order['id']): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $productId = $singerline['producto_id'];
                                            echo isset($_SESSION['productsOrders'][$productId])
                                                ? $_SESSION['productsOrders'][$productId]
                                                : 'Producto desconocido';
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($singerline['unidades']) ?></td>
                                        <td><?= number_format($singerline['precio'], 2) ?>€</td>
                                        <td><?= number_format($singerline['precio'] * $singerline['unidades'], 2) ?>€</td>
                                    </tr>
                                <?php endif; endforeach; endforeach; ?>
                        </tbody>
                    </table>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>