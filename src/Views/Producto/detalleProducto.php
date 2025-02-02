<?php if(isset($_SESSION['productoEliminado'])): ?>
    <div class="alert alert-success" role="alert">El producto se ha borrado correctamente</div>
    <p><a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>
    <?php unset($_SESSION['productoEliminado']) ?>
<?php elseif(isset($_SESSION['falloDatos'])): ?>
    <div class="alert alert-danger" role="alert">Error al borrar el producto</div>
    <p><a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>
    <?php unset($_SESSION['falloDatos']) ?>
<?php else: ?>
    <div class="container mt-4 detalle">
        <div id="botones" class="mb-3">
            <?php if($admin): ?>
                <h2>Detalle del Producto</h2>
                <a href="<?= BASE_URL?>Producto/borrarProducto/<?= $details[0]['id']?>" class="btn btn-primary">Borrar producto</a>
                <a href="<?= BASE_URL?>Producto/actualizarProducto/<?= $details[0]['id']?>" class="btn btn-primary">Editar producto</a>
                <a href="<?= BASE_URL ?>" class="btn btn-primary">Volver a inicio</a>
            <?php else: ?>
                <h2>Detalle del Producto</h2>
                <a href="<?= BASE_URL ?>" class="btn btn-primary">Volver a inicio</a>
            <?php endif; ?>
        </div>

        <div id="productDetail" class="row">
            <?php foreach($details as $detail): ?>
                <div class="col-md-6">
                    <div class="card">

                        <?php
                            $imagePath = $detail["imagen"];
                            $cleanedFileName = substr($imagePath, strpos($imagePath, '_') + 1);
                        ?>
                        <img src="<?=BASE_URL?>public/img/<?= htmlspecialchars($cleanedFileName) ?>" class="card-img-top" alt="producto">

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($detail["nombre"]) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($detail["descripcion"]) ?></p>
                            <p class="card-text">Precio: <?= htmlspecialchars($detail["precio"]) ?>€</p>
                            <p class="card-text">Número de Unidades: 
                                <?php 
                                    if($detail["stock"] != 0){
                                        echo htmlspecialchars($detail["stock"]);
                                    } else {
                                        echo "Agotado";
                                    }
                                ?>
                            </p>
                            <p class="card-text">Oferta de <?= htmlspecialchars($detail["oferta"]) ?>%</p>
                            <?php if($detail["stock"] != 0): ?>
                                <a href="<?= BASE_URL?>Carrito/addProduct/<?= htmlspecialchars($detail["id"]) ?>" class="btn btn-success">Añadir al carrito</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>