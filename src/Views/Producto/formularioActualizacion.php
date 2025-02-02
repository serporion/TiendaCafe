<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div id="product" class="container mt-4 anadir">

    <?php
        if(isset($_SESSION['actualizado'])):
    ?>
        <div class="alert alert-success" role="alert">Producto actualizado con éxito</div>
        <p><a href="<?= BASE_URL ?>" class="btn btn-primary">Volver</a></p>
    <?php unset($_SESSION['actualizado']) ?>
<?php else: ?>


    <form action="<?= BASE_URL ?>Producto/actualizarProducto/<?= $product[0]['id'] ?>" method="POST" enctype="multipart/form-data">
        <h2>Actualizar producto</h2>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría:</label>
            <select name="categoria" id="categoria" class="form-select">
                <?php foreach($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= isset($_POST['nombre']) ? $_POST['nombre'] : $product[0]['nombre'] ?>">
            <?php if (isset($errores['nombre'])): ?>
                <div class="text-danger"><?= $errores['nombre']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control"><?= isset($_POST['descripcion']) ? $_POST['descripcion'] : $product[0]['descripcion'] ?></textarea>
            <?php if (isset($errores['descripcion'])): ?>
                <div class="text-danger"><?= $errores['descripcion']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" name="precio" id="precio" class="form-control" value="<?= isset($_POST['precio']) ? $_POST['precio'] : $product[0]['precio'] ?>">
            <?php if (isset($errores['precio'])): ?>
                <div class="text-danger"><?= $errores['precio']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" name="stock" id="stock" class="form-control" value="<?= isset($_POST['stock']) ? $_POST['stock'] : $product[0]['stock'] ?>">
            <?php if (isset($errores['stock'])): ?>
                <div class="text-danger"><?= $errores['stock']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="oferta" class="form-label">Oferta:</label>
            <input type="text" name="oferta" id="oferta" class="form-control" value="<?= isset($_POST['oferta']) ? $_POST['oferta'] : $product[0]['oferta'] ?>">
            <?php if (isset($errores['oferta'])): ?>
                <div class="text-danger"><?= $errores['oferta']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
    </form>
<?php endif; ?>
</div>