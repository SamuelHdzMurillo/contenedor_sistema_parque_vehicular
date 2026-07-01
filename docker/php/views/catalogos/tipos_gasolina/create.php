<?php
$pageTitle = 'Nuevo tipo de gasolina';
?>
<div class="page-header">
    <div>
        <ul class="breadcrumb"><li><a href="<?= url('catalogos') ?>">Catálogos</a></li><li><a href="<?= url('catalogos/tipos-gasolina') ?>">Tipos de gasolina</a></li><li>/ Nuevo</li></ul>
        <h1 class="page-title">Registrar tipo de gasolina</h1>
    </div>
</div>

<?php App\Core\View::component('catalogo-tabs', ['currentTab' => 'tipos_gasolina']); ?>

<div class="card">
    <div class="card-header">
        <h3>Datos del tipo</h3>
    </div>
    <form action="<?= url('catalogos/tipos-gasolina') ?>" method="post" class="card-body">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre <span class="required">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="100"
                   placeholder="Ej. Magna, Premium, Diésel" value="<?= e((string) old('nombre')) ?>">
        </div>
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="activo" value="1" checked> Tipo activo
            </label>
        </div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary">Registrar</button>
            <a href="<?= url('catalogos/tipos-gasolina') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
