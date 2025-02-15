<?php if(isset($_SESSION['guardado'])): ?>
    <div class="alert alert-success" role="alert">El producto se ha guardado correctamente</div>
    <p><a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>
    <?php unset($_SESSION['guardado']) ?>
<?php endif; ?>


<?php
    if (isset($_SESSION['correoConfirmado'])) {
        echo "<div class='alert alert-info'>" . $_SESSION['correoConfirmado'] . "</div>";
        unset($_SESSION['correoConfirmado']);
    }
?>

<?php
    if (isset($_SESSION['correoConfirmado'])) {
        echo "<div class='alert alert-info'>" . $_SESSION['correoConfirmado'] . "</div>";
        unset($_SESSION['correoConfirmado']);
    }

    if (isset($_SESSION['correctoModificaUsuario'])){
        echo "<div class='alert alert-info'>" . $_SESSION['correctoModificaUsuario'] . "</div>";
        unset($_SESSION['correctoModificaUsuario']);
    }

    if (isset($_SESSION['errorPedido'])) {
        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['errorPedido']) . "</div>";
        unset($_SESSION['errorPedido']);
    }

    if (isset($_SESSION['successPedido'])) {
        echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['successPedido']) . "</div>";
        unset($_SESSION['successPedido']);
    }
?>

<?php if(isset($_SESSION['productoEliminado'])): ?>
    <div class="alert alert-success" role="alert">El producto se ha borrado correctamente</div>
    <p><a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>
    <?php unset($_SESSION['productoEliminado']) ?>

<?php elseif(isset($_SESSION['falloDatos'])): ?>
    <div class="alert alert-danger" role="alert">Error al borrar el producto</div>
    <p><a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>
    <?php unset($_SESSION['falloDatos']) ?>
<?php else: ?>

    <?php
    if (isset($_SESSION['token_error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['token_error']) . '</div>';
        unset($_SESSION['token_error']);
    }
    ?>


    <?php if(isset($_SESSION['restablecerContraseña'])) {
        echo "<div class='alert alert-info'>" . $_SESSION['restablecerContraseña'] . "</div>"; ?>
        <p> <a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>";
        <?php unset($_SESSION['restablecerContraseña']);

        }
        ?>



<div id="producto" class="container mt-4 lista">
    <h2>Listado de Productos</h2>

    <?php if ($hayProductos): ?>
        <div class="row">
            <?php foreach($productos as $producto): ?>
                <?php 
                    $descatalogados = ($producto["categoria_id"] == 99);
                ?>

                <?php if(!$descatalogados || $admin): ?>
                    <div class="col-md-4 mb-3">

                        <a href="<?= BASE_URL?>Producto/detalleProducto/<?= htmlspecialchars($producto["id"]) ?>" class="itemProducto">
                            <div class="card <?= $producto["stock"] == 0 ? : '' ?>">
                                <div class="image">
                                    <?php
                                        $imagePath = $producto["imagen"];
                                        $cleanedFileName = substr($imagePath, strpos($imagePath, '_') + 1);
                                    ?>
                                    <img src="<?=BASE_URL?>public/img/<?= htmlspecialchars($cleanedFileName) ?>" class="card-img-top" alt="producto">


                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($producto["nombre"]) ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($producto["descripcion"]) ?></p>
                                    <p class="card-text">Precio: <?php echo htmlspecialchars($producto["precio"]) ?>€</p>
                                    <p class="card-text">Número de Unidades: 
                                        <?php 
                                            if($producto["stock"] != 0){
                                                echo htmlspecialchars($producto["stock"]);
                                            } else {
                                                echo "Agotado";
                                            }
                                        ?>
                                    </p>
                                    <p class="card-text">Oferta de <?php echo htmlspecialchars($producto["oferta"]) ?>%</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay productos disponibles.</div>
    <?php endif; ?>
</div>
<?php endif; ?>