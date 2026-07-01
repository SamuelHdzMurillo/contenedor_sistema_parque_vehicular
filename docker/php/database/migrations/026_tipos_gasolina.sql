-- Catálogo de tipos de gasolina / combustible para cargas
CREATE TABLE IF NOT EXISTS tipos_gasolina (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_tipos_gasolina_nombre (nombre)
) ENGINE=InnoDB;

INSERT IGNORE INTO tipos_gasolina (nombre, activo) VALUES
    ('Magna', 1),
    ('Premium', 1),
    ('Diésel', 1),
    ('Urea (AdBlue)', 1);

ALTER TABLE combustible_cargas
    ADD COLUMN tipo_gasolina_id INT UNSIGNED NULL AFTER proveedor_id,
    ADD CONSTRAINT fk_combustible_tipo_gasolina
        FOREIGN KEY (tipo_gasolina_id) REFERENCES tipos_gasolina(id) ON DELETE SET NULL;
