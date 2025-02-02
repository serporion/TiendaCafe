<div id="listadoCategorias" class="container mt-4">

    <?php if($admin): ?>
        <div class="btnCategorias mb-3">
            <a href="<?= BASE_URL?>Categoria/almacenarCategoria" class="btn btn-primary">Crear una categoría</a>
            <a href="<?= BASE_URL?>Categoria/actualizarCategoria" class="btn btn-primary">Editar una categoría</a>
            <a href="<?= BASE_URL?>Categoria/borrarCategoria" class="btn btn-primary">Borrar una categoría</a>
        </div>
    <?php endif; ?>

    <h2>Categorías</h2>

    <?php if(!$hayCategorias): ?>
        <div class="alert alert-info">No hay categorías</div>
        <p><a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Volver al Inicio</a></p>
    <?php endif; ?>

    <ul id="lista" class="list-group">
        <?php foreach($categorias as $categoria): ?>
            <?php 
                $descatalogados = ($categoria["id"] == 99  || $categoria["nombre"] === "Descatalogados");
            ?>
            
            <?php if(!$descatalogados || $admin): ?>
                <a class="enlace" href="<?= BASE_URL?>Categoria/ProductXCategory/<?= htmlspecialchars($categoria["id"]) ?>">
                    <li class="list-group-item categoriaLista">
                        <?= htmlspecialchars($categoria["nombre"]) ?>
                    </li>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

</div>