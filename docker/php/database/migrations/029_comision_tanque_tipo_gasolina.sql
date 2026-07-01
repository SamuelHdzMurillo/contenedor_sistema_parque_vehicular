-- Tipo de combustible del tanque adicional en comisiones
ALTER TABLE comisiones
    ADD COLUMN tanque_adicional_tipo_gasolina_id INT UNSIGNED NULL AFTER tanque_adicional_regreso,
    ADD CONSTRAINT fk_comision_tanque_tipo_gasolina
        FOREIGN KEY (tanque_adicional_tipo_gasolina_id) REFERENCES tipos_gasolina(id) ON DELETE SET NULL;
