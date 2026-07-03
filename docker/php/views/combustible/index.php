<?php
$pageTitle = 'Combustible';
$data = $data ?? [];
$vehiculos = $vehiculos ?? [];
?>
<div class="page-header">
    <div>
        <h1 class="page-title">Control de combustible</h1>
        <p class="page-subtitle">Registro de cargas y rendimiento</p>
    </div>
    <?php if (can('combustible.create')): ?>
    <div class="page-actions">
        <a href="<?= url('formatos/combustible') ?>" class="btn btn-secondary" target="_blank">Formato PDF en blanco</a>
        <a href="<?= url('combustible/create') ?>" class="btn btn-primary">+ Registrar carga</a>
    </div>
    <?php endif; ?>
</div>
<div class="card">
    <form class="filters-bar" method="get">
        <div class="form-group">
            <label class="form-label" for="vehiculo_id">Vehículo</label>
            <select id="vehiculo_id" name="vehiculo_id" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($vehiculos as $v): ?>
                <option value="<?= (int) $v['id'] ?>" <?= ($_GET['vehiculo_id'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= e(catalogo_vehiculo_label($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-info">Filtrar</button>
    </form>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Fecha</th><th>Vehículo</th><th>Tipo</th><th>Litros</th><th>Importe</th><th>Km</th><th>Rendimiento</th><th>Costo/km</th><th>Ticket</th><th></th></tr></thead>
            <tbody>
                <?php if (empty($data)): ?>
                <tr><td colspan="10" class="text-center text-muted">Sin cargas registradas</td></tr>
                <?php else: foreach ($data as $c): ?>
                <tr>
                    <td><?= format_date($c['fecha']) ?></td>
                    <td><?= e($c['numero_economico'] ?? '—') ?></td>
                    <td><?= e($c['tipo_gasolina_nombre'] ?? '—') ?></td>
                    <td><?= number_format((float) $c['litros'], 2) ?> L</td>
                    <td><?= format_money($c['importe']) ?></td>
                    <td><?= number_format((int) $c['kilometraje']) ?></td>
                    <td><?= $c['rendimiento'] !== null ? number_format((float) $c['rendimiento'], 2) . ' km/L' : '—' ?></td>
                    <td><?= $c['costo_por_km'] !== null ? format_money($c['costo_por_km']) : '—' ?></td>
                    <td>
                        <?php if (!empty($c['factura_ruta'])): ?>
                        <a href="<?= url('storage/uploads/' . ltrim((string) $c['factura_ruta'], '/')) ?>" class="btn btn-sm btn-secondary" target="_blank">Ver ticket</a>
                        <?php elseif (!empty($c['folio_ticket'])): ?>
                        <?= e($c['folio_ticket']) ?>
                        <?php else: ?>
                        —
                        <?php endif; ?>
                    </td>
                    <td class="table-actions">
                        <?php if (can('combustible.update')): ?>
                        <a href="<?= url('combustible/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-secondary">Editar</a>
                        <form action="<?= url('combustible/' . $c['id'] . '/eliminar') ?>" method="post" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger" data-confirm="¿Confirma eliminar esta carga de combustible? Esta acción no se puede deshacer.">Eliminar</button>
                        </form>
                        <?php endif; ?>
                        <a href="<?= url('formatos/combustible/' . $c['id']) ?>" class="btn btn-sm btn-info" target="_blank">PDF</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php App\Core\View::component('pagination', ['page' => $page ?? 1, 'total' => $total ?? 0, 'per_page' => $per_page ?? 15]); ?>
</div>
