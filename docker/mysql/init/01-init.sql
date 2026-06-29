-- Script inicial opcional. Se ejecuta solo la primera vez que se crea el volumen MySQL.
USE parque_vehicular;

CREATE TABLE IF NOT EXISTS ejemplo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mensaje VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO ejemplo (mensaje) VALUES ('Conexión MySQL correcta desde Docker');
