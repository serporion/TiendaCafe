<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div id="product" class="container mt-4">

<?php 
    if(isset($_SESSION['guardado'])): 
?>
    <div class="alert alert-success" role="alert">Producto guardado con éxito</div>
<?php else: ?>
    <h2>Añadir producto</h2>
    <form action="<?= BASE_URL ?>Producto/guardarProductos" method="POST" enctype="multipart/form-data">

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
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= $_POST['nombre'] ?? '' ?>">
            <?php if (isset($errores['nombre'])): ?>
                <div class="text-danger"><?= $errores['nombre']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control"><?= $_POST['descripcion'] ?? ''; ?></textarea>
            <?php if (isset($errores['descripcion'])): ?>
                <div class="text-danger"><?= $errores['descripcion']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" name="precio" id="precio" class="form-control" value="<?= $_POST['precio'] ?? '' ?>">
            <?php if (isset($errores['precio'])): ?>
                <div class="text-danger"><?= $errores['precio']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" name="stock" id="stock" class="form-control" value="<?= $_POST['stock'] ?? '' ?>">
            <?php if (isset($errores['stock'])): ?>
                <div class="text-danger"><?= $errores['stock']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Producto</button>
    </form>
<?php endif; ?>
</div>