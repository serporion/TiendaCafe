<div class="container mt-4 anadir">
    <h2>Modificar Pedido</h2>
    <form action="<?= htmlspecialchars(BASE_URL . 'Pedido/grabarModificacion', ENT_QUOTES, 'UTF-8') ?>" method="post">

    <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
        <div class="mb-3">
            <label for="numeroPedido" class="form-label">Nº de Pedido</label>
            <input type="text" class="form-control" id="numeroPedido" value="<?= $pedido['id'] ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="coste" class="form-label">Total Pedido</label>
            <input type="text" class="form-control" id="coste" value="<?= $pedido['coste'] ?>€" readonly>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="confirmado" <?= $pedido['estado'] == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                <option value="pendiente" <?= $pedido['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="enviado" <?= $pedido['estado'] == 'enviado' ? 'selected' : '' ?>>Enviado</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="text" class="form-control" id="fecha" value="<?= $pedido['fecha'] ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" value="<?= $pedido['direccion'] ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Grabar</button>
        <a href="<?=BASE_URL?>Auth/extraer_todos" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

