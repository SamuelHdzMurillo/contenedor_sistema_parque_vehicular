-- Usuario predeterminado por cada rol (contraseñas en usuarios_predeterminados.txt)
USE sicv_cecyte_bcs;

INSERT INTO users (role_id, area_id, nombre, apellido_paterno, apellido_materno, email, telefono, password_hash, activo) VALUES
(1, NULL, 'Administrador', 'General', 'SICV', 'admin@cecytebcs.edu.mx', NULL, '$2y$10$5jUKrdZdtL02NU345PZF.utgpGuo7moOL/yxGA7EVL.1Ibw8o56V6', 1),
(2, NULL, 'Administrador', 'Transporte', 'SICV', 'transporte@cecytebcs.edu.mx', NULL, '$2y$10$nwCMBQH7lAyXkyY9/N4LYucJhOOzInolqkPAGH//wkOQF9BNex/s2', 1),
(3, NULL, 'Usuario', 'Supervisor', 'SICV', 'supervisor@cecytebcs.edu.mx', NULL, '$2y$10$uhLNbzH5NxKvyefCz/fGIenl1jQ5/ij66fM8Zc8N77RlE9oLsDC76', 1),
(4, NULL, 'Responsable', 'Vehiculo', 'SICV', 'responsable@cecytebcs.edu.mx', NULL, '$2y$10$ZEdwnZuynzz0M/wLkysyeeXzz8gd6FFzSl/jAfqk3uBd7FoJZ6vQS', 1),
(5, NULL, 'Usuario', 'Consulta', 'SICV', 'consulta@cecytebcs.edu.mx', NULL, '$2y$10$7YvrpsIlsaNGdOpCxIZM6u7pUzvCJDu9k2yogXToIfepyEg0T.qIm', 1);
