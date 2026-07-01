-- Tanque adicional en comisiones (combustible extra portátil)
ALTER TABLE comisiones
    ADD COLUMN tanque_adicional TINYINT(1) NOT NULL DEFAULT 0 AFTER combustible_regreso,
    ADD COLUMN tanque_adicional_litros_salida DECIMAL(8,2) NULL AFTER tanque_adicional,
    ADD COLUMN tanque_adicional_litros_regreso DECIMAL(8,2) NULL AFTER tanque_adicional_litros_salida;
