-- Tanque adicional: nivel en porcentaje (como combustible principal)
ALTER TABLE comisiones
    CHANGE COLUMN tanque_adicional_litros_salida tanque_adicional_salida DECIMAL(5,2) NULL,
    CHANGE COLUMN tanque_adicional_litros_regreso tanque_adicional_regreso DECIMAL(5,2) NULL;
