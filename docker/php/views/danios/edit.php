<?php
$pageTitle = 'Editar daño';
$danio = $danio ?? [];
$d = $danio;
$vehiculos = $vehiculos ?? [];
$preVehiculo = old('vehiculo_id', $d['vehiculo_id'] ?? '');
$preTipo = old('tipo_dano', $d['tipo_dano'] ?? '');
$preUbicacion = old('ubicacion', $d['ubicacion'] ?? '');
$preDescripcion = old('descripcion', $d['descripcion'] ?? '');
?>
<div class="page-header">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?= url('danios') ?>">Daños</a></li>
            <li><a href="<?= url('danios/' . $d['id']) ?>">#<?= (int) ($d['id'] ?? 0) ?></a></li>
            <li>/ Editar</li>
        </ul>
        <h1 class="page-title">Editar daño #<?= (int) ($d['id'] ?? 0) ?></h1>
        <p class="page-subtitle"><?= e($d['numero_economico'] ?? '') ?> — <?= e(str_replace('_', ' ', $d['estado'] ?? '')) ?></p>
    </div>
</div>
<div class="card">
    <form action="<?= url('danios/' . $d['id']) ?>" method="post" class="card-body">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="vehiculo_id">Vehículo <span class="required">*</span></label>
                <select id="vehiculo_id" name="vehiculo_id" class="form-select" required>
                    <option value="">Seleccione…</option>
                    <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= (int) $v['id'] ?>" <?= (string) $preVehiculo === (string) $v['id'] ? 'selected' : '' ?>>
                        <?= e(catalogo_vehiculo_label($v)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="tipo_dano">Tipo de daño <span class="required">*</span></label>
                <select id="tipo_dano" name="tipo_dano" class="form-select" required>
                    <?php foreach (['golpe','rayon','cristal','defensa','faro','interior','llanta'] as $t): ?>
                    <option value="<?= e($t) ?>" <?= $preTipo === $t ? 'selected' : '' ?>><?= e(ucfirst($t)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="ubicacion">Ubicación <span class="required">*</span></label>
                <input type="text" id="ubicacion" name="ubicacion" class="form-control" required placeholder="Ej. Puerta trasera izquierda"
                       value="<?= e((string) $preUbicacion) ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción <span class="required">*</span></label>
            <textarea id="descripcion" name="descripcion" class="form-textarea" required><?= e((string) $preDescripcion) ?></textarea>
        </div>
        <p class="form-hint mb-2">Las fotografías se gestionan desde la ficha del daño.</p>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="<?= url('danios/' . $d['id']) ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
