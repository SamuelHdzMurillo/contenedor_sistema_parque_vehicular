<?php ?>
<div class="modal-overlay" id="modal-tipo-gasolina-quick" data-tipo-gasolina-quick-modal aria-hidden="true">
    <div class="modal-dialog" role="dialog" aria-labelledby="modal-tipo-gasolina-quick-title">
        <div class="modal-header">
            <h3 id="modal-tipo-gasolina-quick-title" class="modal-title">Nuevo tipo de gasolina</h3>
            <button type="button" class="modal-close" data-tipo-gasolina-quick-close aria-label="Cerrar">&times;</button>
        </div>
        <form data-tipo-gasolina-quick-form action="<?= e(url_path('catalogos/tipos-gasolina/quick')) ?>" method="post" novalidate>
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <div class="modal-body">
                <p class="card-header-hint mb-2">Se agregará al catálogo y quedará disponible al registrar cargas.</p>
                <div class="form-group">
                    <label class="form-label" for="modal-tipo-gasolina-nombre">Nombre <span class="required">*</span></label>
                    <input type="text" id="modal-tipo-gasolina-nombre" name="nombre" class="form-control" required maxlength="100" placeholder="Ej. Magna, Premium, Diésel">
                </div>
                <input type="hidden" name="activo" value="1">
                <div class="alert alert-error" data-tipo-gasolina-quick-error hidden></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-tipo-gasolina-quick-close>Cancelar</button>
                <button type="submit" class="btn btn-primary" data-tipo-gasolina-quick-submit>Registrar</button>
            </div>
        </form>
    </div>
</div>
