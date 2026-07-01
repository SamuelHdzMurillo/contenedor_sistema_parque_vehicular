<?php
$pageTitle = 'Editar tipo de gasolina';
$tipo = $tipo ?? [];
$t = array_merge($tipo, array_intersect_key($_SESSION['_old'] ?? [], array_flip(['nombre', 'activo'])));
?>
<div class="page-header">
    <div>
        <ul class="breadcrumb"><li><a href="<?= url('catalogos') ?>">Catálogos</a></li><li><a href="<?= url('catalogos/tipos-gasolina') ?>">Tipos de gasolina</a></li><li>/ Editar</li></ul>
        <h1 class="page-title">Editar tipo de gasolina</h1>
    </div>
</div>

<?php App\Core\View::component('catalogo-tabs', ['currentTab' => 'tipos_gasolina']); ?>

<div class="card">
    <div class="card-header">
        <h3><?= e($t['nombre']) ?></h3>
    </div>
    <form action="<?= url('catalogos/tipos-gasolina/' . $t['id']) ?>" method="post" class="card-body">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre <span class="required">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="100" value="<?= e($t['nombre']) ?>">
        </div>
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="activo" value="1" <?= (int) ($t['activo'] ?? 1) === 1 ? 'checked' : '' ?>> Tipo activo
            </label>
        </div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= url('catalogos/tipos-gasolina') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
