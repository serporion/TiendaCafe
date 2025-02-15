<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
?>

<div class="container mt-4 pedidoForm">
    <div class="guardarCategoria">
        <?php 
            if(isset($_SESSION['borrado'])): 
        ?>
            <div class="alert alert-success" role="alert">Categoría borrada con éxito</div>
            <p><a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver</a></p>
        <?php elseif(isset($_SESSION['falloDatos'])): ?>
            <div class="alert alert-danger" role="alert">Los datos no se han enviado correctamente</div>
            <p><a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver</a></p>
        <?php else: ?>
            <h2>Borrar Categoría</h2>
                <form action="<?= htmlspecialchars(BASE_URL . 'Categoria/borrarCategoria', ENT_QUOTES, 'UTF-8') ?>" method="POST">

                <div class="mb-3">
                    <label for="categoriaSelect" class="form-label">Categoría:</label>
                    <select name="categoriaSelect" id="categoriaSelect" class="form-select">
                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria["id"]) ?>"><?= htmlspecialchars($categoria["nombre"]) ?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <?php if (isset($errores['id'])): ?>
                    <div class="text-danger"><?= $errores['id']; ?></div>
                <?php endif; ?>

                <?php if (isset($errores['db'])): ?>
                    <div class="text-danger"><?= $errores['db']; ?></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Borrar</button>
                <a href="<?= BASE_URL ?>Categoria/categorias" class="btn btn-primary">Volver atrás</a>
            </form>
        <?php 
            endif;
        ?>
    </div>
</div>