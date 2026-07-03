<?php
$pageTitle = 'Editar carga de combustible';
$carga = $carga ?? [];
$vehiculos = $vehiculos ?? [];
$proveedores = $proveedores ?? [];
$tipos_gasolina = $tipos_gasolina ?? [];
$c = array_merge($carga, array_intersect_key($_SESSION['_old'] ?? [], array_flip([
    'fecha', 'kilometraje', 'tipo_gasolina_id', 'litros', 'importe',
    'proveedor_id', 'folio_ticket', 'observaciones',
])));
$ticketUrl = !empty($c['factura_ruta'])
    ? url('storage/uploads/' . ltrim((string) $c['factura_ruta'], '/'))
    : '';
?>
<div class="page-header">
    <div>
        <ul class="breadcrumb"><li><a href="<?= url('combustible') ?>">Combustible</a></li><li>/ Editar carga</li></ul>
        <h1 class="page-title">Editar carga de combustible</h1>
        <p class="page-subtitle">
            <?= e($c['numero_economico'] ?? '') ?>
            · <?= format_date($c['fecha'] ?? null) ?>
        </p>
    </div>
</div>
<div class="card">
    <form action="<?= url('combustible/' . (int) $c['id']) ?>" method="post" enctype="multipart/form-data" class="card-body">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Vehículo</label>
                <input type="text" class="form-control" readonly
                    value="<?= e(catalogo_vehiculo_label(['numero_economico' => $c['numero_economico'] ?? '', 'placas' => $c['placas'] ?? ''])) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="fecha">Fecha <span class="required">*</span></label>
                <input type="date" id="fecha" name="fecha" class="form-control" required value="<?= e((string) old('fecha', $c['fecha'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="kilometraje">Kilometraje al cargar <span class="required">*</span></label>
                <input type="number" id="kilometraje" name="kilometraje" class="form-control" required min="0" value="<?= e((string) old('kilometraje', $c['kilometraje'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="tipo_gasolina_id">Tipo de gasolina <span class="required">*</span></label>
                <div class="input-group">
                    <select id="tipo_gasolina_id" name="tipo_gasolina_id" class="form-select" required data-tipo-gasolina-select>
                        <option value="">Seleccione…</option>
                        <?php foreach ($tipos_gasolina as $tg): ?>
                        <option value="<?= (int) $tg['id'] ?>" <?= (string) old('tipo_gasolina_id', $c['tipo_gasolina_id'] ?? '') === (string) $tg['id'] ? 'selected' : '' ?>><?= e($tg['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (can('catalogos.create') || can('combustible.create')): ?>
                    <button type="button" class="btn btn-accent" data-tipo-gasolina-quick-open title="Agregar tipo de gasolina" aria-label="Agregar tipo de gasolina">+</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="litros">Litros <span class="required">*</span></label>
                <input type="number" id="litros" name="litros" class="form-control" required step="0.01" min="0" value="<?= e((string) old('litros', $c['litros'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="importe">Importe <span class="required">*</span></label>
                <input type="number" id="importe" name="importe" class="form-control" required step="0.01" min="0" value="<?= e((string) old('importe', $c['importe'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="proveedor_id">Estación / Proveedor</label>
                <div class="input-group">
                    <select id="proveedor_id" name="proveedor_id" class="form-select" data-proveedor-select data-proveedor-tipo="combustible">
                        <option value="">— Opcional —</option>
                        <?php foreach ($proveedores as $p): ?>
                        <option value="<?= (int) $p['id'] ?>"
                            data-rfc="<?= e($p['rfc'] ?? '') ?>"
                            data-telefono="<?= e($p['telefono'] ?? '') ?>"
                            data-email="<?= e($p['email'] ?? '') ?>"
                            data-direccion="<?= e($p['direccion'] ?? '') ?>"
                            <?= (string) old('proveedor_id', $c['proveedor_id'] ?? '') === (string) $p['id'] ? 'selected' : '' ?>><?= e($p['razon_social']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (can('proveedores.create')): ?>
                    <button type="button" class="btn btn-accent" data-proveedor-quick-open title="Agregar estación / proveedor" aria-label="Agregar estación / proveedor">+</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="folio_ticket">Folio ticket</label>
                <input type="text" id="folio_ticket" name="folio_ticket" class="form-control" value="<?= e((string) old('folio_ticket', $c['folio_ticket'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="archivo_ticket">Archivo del ticket</label>
                <?php if ($ticketUrl !== ''): ?>
                <p class="form-hint mb-1">
                    <a href="<?= e($ticketUrl) ?>" target="_blank" rel="noopener">Ver ticket actual</a>
                </p>
                <?php endif; ?>
                <input type="file" id="archivo_ticket" name="archivo_ticket" class="form-control" accept="application/pdf,image/jpeg,image/png">
                <p class="form-hint">Deje vacío para conservar el archivo actual.</p>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" class="form-textarea"><?= e((string) old('observaciones', $c['observaciones'] ?? '')) ?></textarea>
        </div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="<?= url('formatos/combustible/' . (int) $c['id']) ?>" class="btn btn-secondary" target="_blank">PDF</a>
            <a href="<?= url('combustible') ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php if (can('proveedores.create')): ?>
<?php App\Core\View::component('modal-proveedor-quick', ['tipo' => 'combustible', 'contexto' => 'combustible']); ?>
<?php endif; ?>
<?php if (can('catalogos.create') || can('combustible.create')): ?>
<?php App\Core\View::component('modal-tipo-gasolina-quick'); ?>
<?php endif; ?>
