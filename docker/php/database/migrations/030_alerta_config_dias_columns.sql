-- Columnas de umbrales en días para alerta_config (MySQL 8 no admite ADD COLUMN IF NOT EXISTS).
USE sicv_cecyte_bcs;

ALTER TABLE alerta_config
    ADD COLUMN umbral_verde_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_rojo,
    ADD COLUMN umbral_amarillo_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_verde_dias,
    ADD COLUMN umbral_rojo_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_amarillo_dias;

UPDATE alerta_config SET
    umbral_verde_dias = 365,
    umbral_amarillo_dias = 180,
    umbral_rojo_dias = 90
WHERE unidad = 'km' AND umbral_verde_dias IS NULL;
