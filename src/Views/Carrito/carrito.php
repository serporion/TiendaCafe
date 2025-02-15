<div class="container mt-4">
    <div id="cart">
        <?php if(!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])): ?>
            <div class="alert alert-warning" role="alert">El carrito está vacío</div>
            <p><a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Volver al Inicio</a></p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION['carrito'] as $cart): ?>
                        <tr>
                            <td class="imageCartItem">
                                <?php
                                    $imagePath = $cart["imagen"];
                                    $cleanedFileName = substr($imagePath, strpos($imagePath, '_') + 1);
                                ?>
                                <img src="<?=BASE_URL?>public/img/<?= htmlspecialchars($cleanedFileName) ?>" class="imageCart" alt="producto">

                            </td>
                            <td class="nameCartItem">
                                <a href="<?= BASE_URL?>Producto/detalleProducto/<?= htmlspecialchars($cart['id']) ?>"><?= htmlspecialchars($cart["nombre"]) ?></a>
                            </td>
                            <td class="priceCartItem">
                                <?= htmlspecialchars($cart["precio"]) ?> €
                            </td>
                            <td class="amountCartItem">

                                <form method="POST" action="<?= BASE_URL?>Carrito/disminuir/<?= $cart['id'] ?>" style="display:inline;">
                                    <button type="submit" class="btn btn-outline-secondary">-</button>
                                </form>
                                <?= htmlspecialchars($cart["cantidad"]) ?>
                                <form method="POST" action="<?= BASE_URL?>Carrito/aumentar/<?= $cart['id'] ?>" style="display:inline;">
                                    <button type="submit" class="btn btn-outline-secondary">+</button>
                                </form>


                                <?php if(isset($error)): ?>
                                    <span class="text-danger"><?= $error ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="removeCartItem">
                                <a href="<?= BASE_URL?>Carrito/removeItem/<?= $cart['id'] ?>" class="btn btn-danger">Eliminar producto</a>
                                <?php if(isset($errorRemove)): ?>
                                    <span class="text-danger"><?= $errorRemove ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="inforCart">

                <h2 id="totalPrice">Precio total: <?= $_SESSION['totalCost'] ?> €</h2>

                <a href="<?= BASE_URL?>Carrito/borrarCarrito" class="botonesProductos">Vaciar carrito</a>

                <a href="<?= BASE_URL?>Pedido/guardarPedido" class="botonesProductos">Confirmar pedido</a>

                <a href="<?= BASE_URL ?>" class="btn btn-primary">Volver a inicio</a>

            </div>
        <?php endif; ?>
    </div>
</div>