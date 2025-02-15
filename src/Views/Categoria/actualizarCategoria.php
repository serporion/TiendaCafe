<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div class="container mt-4 pedidoForm">
<?php 
    if(isset($_SESSION['actualizado'])): 
?>
    <div class="alert alert-success" role="alert">Categoría actualizada con éxito</div>
    <p><a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver</a></p>
<?php elseif(isset($_SESSION['falloDatos'])): ?>
    <div class="alert alert-danger" role="alert">Los datos no se han enviado correctamente</div>
    <p><a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver</a></p>
<?php else: ?>
    <h2>Actualizar Categoría</h2>
        <form action="<?= htmlspecialchars(BASE_URL . 'Categoria/actualizarCategoria', ENT_QUOTES, 'UTF-8') ?>" method="POST">

        <div class="mb-3">
            <label for="categoriaSelect" class="form-label">Categoría:</label>
            <select name="categoriaSelect" id="categoriaSelect" class="form-select">
                <?php foreach($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="categoria[nombre]" id="nombre" class="form-control" value="<?=(isset($category))?$category->getNombre():""?>">
            <?php if (isset($errores['nombre'])): ?>
                <div class="text-danger"><?= $errores['nombre']; ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($errores['db'])): ?>
            <div class="text-danger"><?= $errores['db']; ?></div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver atrás</a>
    </form>
<?php 
    endif;
?>
</div>