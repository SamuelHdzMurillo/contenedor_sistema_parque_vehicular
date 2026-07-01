<?php
/**
 * Campos de tanque adicional en comisiones (medidor de porcentaje como combustible).
 *
 * @var string $mode 'salida' | 'regreso' | 'completo'
 * @var bool|int|string|null $tieneTanqueAdicional
 * @var string|float|null $combustibleSalida
 * @var string|float|null $combustibleRegreso
 * @var int|string|null $tipoGasolinaId
 * @var array $tipos_gasolina
 * @var string $idPrefix
 */
$mode = $mode ?? 'salida';
$idPrefix = $idPrefix ?? 'tanque_adicional';
$tiposGasolina = $tipos_gasolina ?? [];
$tipoGasolinaSeleccionado = old('tanque_adicional_tipo_gasolina_id', $tipoGasolinaId ?? '');
$tieneTanque = (int) ($tieneTanqueAdicional ?? 0) === 1;
$showPregunta = in_array($mode, ['salida', 'completo'], true);
$showSalida = in_array($mode, ['salida', 'completo'], true);
$showRegreso = in_array($mode, ['regreso', 'completo'], true);
$showTipoCombustible = in_array($mode, ['salida', 'completo'], true);
?>
<div class="form-group tanque-adicional-block mb-0" data-tanque-adicional<?= $tieneTanque ? ' data-tanque-adicional-active' : '' ?>>
    <?php if ($showPregunta): ?>
    <label class="form-label">¿Trae tanque adicional?</label>
    <div class="rating-group mb-2">
        <label class="rating-bueno">
            <input type="radio" name="tanque_adicional" value="0" data-tanque-adicional-toggle <?= !$tieneTanque ? 'checked' : '' ?>>
            <span>No</span>
        </label>
        <label class="rating-regular">
            <input type="radio" name="tanque_adicional" value="1" data-tanque-adicional-toggle <?= $tieneTanque ? 'checked' : '' ?>>
            <span>Sí</span>
        </label>
    </div>
    <?php endif; ?>

    <?php if ($showSalida): ?>
    <div class="tanque-adicional-salida" data-tanque-adicional-salida<?= $tieneTanque ? '' : ' hidden' ?>>
        <?php if ($showTipoCombustible): ?>
        <div class="form-group">
            <label class="form-label" for="<?= e($idPrefix) ?>_tipo_gasolina_id">Tipo de combustible del tanque adicional <span class="required">*</span></label>
            <div class="input-group">
                <select id="<?= e($idPrefix) ?>_tipo_gasolina_id"
                        name="tanque_adicional_tipo_gasolina_id"
                        class="form-select"
                        data-tanque-adicional-tipo
                        data-tipo-gasolina-select
                        <?= $tieneTanque ? 'required' : '' ?>>
                    <option value="">Seleccione…</option>
                    <?php foreach ($tiposGasolina as $tg): ?>
                    <option value="<?= (int) $tg['id'] ?>" <?= (string) $tipoGasolinaSeleccionado === (string) $tg['id'] ? 'selected' : '' ?>>
                        <?= e($tg['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (can('catalogos.create') || can('comisiones.create')): ?>
                <button type="button" class="btn btn-accent" data-tipo-gasolina-quick-open title="Agregar tipo de combustible" aria-label="Agregar tipo de combustible">+</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php App\Core\View::component('combustible-fraccion-select', [
            'id' => $idPrefix . '_salida',
            'name' => 'tanque_adicional_salida',
            'label' => 'Nivel del tanque adicional al salir',
            'valuePorcentaje' => $combustibleSalida ?? ($tieneTanque ? 100 : null),
            'required' => $tieneTanque,
        ]); ?>
    </div>
    <?php endif; ?>

    <?php if ($showRegreso): ?>
    <div class="tanque-adicional-regreso" data-tanque-adicional-regreso<?= ($mode === 'regreso' || ($mode === 'completo' && $tieneTanque)) ? '' : ' hidden' ?>>
        <?php App\Core\View::component('combustible-fraccion-select', [
            'id' => $idPrefix . '_regreso',
            'name' => 'tanque_adicional_regreso',
            'label' => 'Nivel del tanque adicional al regresar',
            'valuePorcentaje' => $combustibleRegreso ?? ($mode === 'regreso' ? 100 : null),
            'required' => $mode === 'regreso' || ($mode === 'completo' && $tieneTanque),
        ]); ?>
    </div>
    <?php endif; ?>
</div>
