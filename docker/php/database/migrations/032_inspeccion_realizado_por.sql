-- Nombre libre de quien realizó la inspección (para firmar en el formato).
ALTER TABLE inspecciones
    ADD COLUMN realizado_por VARCHAR(200) NULL AFTER responsable_id;

UPDATE inspecciones i
JOIN users u ON u.id = i.responsable_id
SET i.realizado_por = TRIM(CONCAT(u.nombre, ' ', u.apellido_paterno))
WHERE i.realizado_por IS NULL OR i.realizado_por = '';
