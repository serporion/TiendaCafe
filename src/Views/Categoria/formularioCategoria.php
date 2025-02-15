<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div class="container mt-4 pedidoForm">
    <div class="guardarCategoria">
        <h2>Añadir Categoría</h2>
        <form action="<?= htmlspecialchars(BASE_URL . 'Categoria/almacenarCategoria', ENT_QUOTES, 'UTF-8') ?>" method="POST">

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

            <button type="submit" class="btn btn-primary">Añadir</button>
            <a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver atrás</a>
        </form>
    </div>
</div>