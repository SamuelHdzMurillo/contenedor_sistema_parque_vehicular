-- Umbrales en días para alertas basadas en km (lógica OR: km o días)
USE sicv_cecyte_bcs;

CREATE TABLE IF NOT EXISTS vehiculo_alerta_config (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehiculo_id INT UNSIGNED NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    umbral_verde INT NOT NULL DEFAULT 0,
    umbral_amarillo INT NOT NULL DEFAULT 0,
    umbral_rojo INT NOT NULL DEFAULT 0,
    umbral_verde_dias INT UNSIGNED NULL DEFAULT NULL,
    umbral_amarillo_dias INT UNSIGNED NULL DEFAULT NULL,
    umbral_rojo_dias INT UNSIGNED NULL DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_vehiculo_alerta_tipo (vehiculo_id, tipo),
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE alerta_config
    ADD COLUMN umbral_verde_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_rojo,
    ADD COLUMN umbral_amarillo_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_verde_dias,
    ADD COLUMN umbral_rojo_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_amarillo_dias;

UPDATE alerta_config SET
    umbral_verde_dias = 365,
    umbral_amarillo_dias = 180,
    umbral_rojo_dias = 90
WHERE unidad = 'km' AND umbral_verde_dias IS NULL;

-- Umbrales en días por vehículo (tabla puede existir con esquema previo)
ALTER TABLE vehiculo_alerta_config
    ADD COLUMN umbral_verde_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_rojo,
    ADD COLUMN umbral_amarillo_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_verde_dias,
    ADD COLUMN umbral_rojo_dias INT UNSIGNED NULL DEFAULT NULL AFTER umbral_rojo_dias;
