-- Actualiza contraseñas de usuarios predeterminados (ver usuarios_predeterminados.txt)
USE sicv_cecyte_bcs;

UPDATE users SET password_hash = '$2y$10$5jUKrdZdtL02NU345PZF.utgpGuo7moOL/yxGA7EVL.1Ibw8o56V6' WHERE email = 'admin@cecytebcs.edu.mx';
UPDATE users SET password_hash = '$2y$10$nwCMBQH7lAyXkyY9/N4LYucJhOOzInolqkPAGH//wkOQF9BNex/s2' WHERE email = 'transporte@cecytebcs.edu.mx';
UPDATE users SET password_hash = '$2y$10$uhLNbzH5NxKvyefCz/fGIenl1jQ5/ij66fM8Zc8N77RlE9oLsDC76' WHERE email = 'supervisor@cecytebcs.edu.mx';
UPDATE users SET password_hash = '$2y$10$ZEdwnZuynzz0M/wLkysyeeXzz8gd6FFzSl/jAfqk3uBd7FoJZ6vQS' WHERE email = 'responsable@cecytebcs.edu.mx';
UPDATE users SET password_hash = '$2y$10$7YvrpsIlsaNGdOpCxIZM6u7pUzvCJDu9k2yogXToIfepyEg0T.qIm' WHERE email = 'consulta@cecytebcs.edu.mx';
