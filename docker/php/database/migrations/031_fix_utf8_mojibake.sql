-- Repara textos UTF-8 doble-codificados (mojibake: ó→Ã³, í→Ã­, ñ→Ã±, etc.).
-- Usa CONVERT para no depender del charset del cliente mysql al aplicar el .sql.
-- Solo toca filas con la firma HEX típica de doble codificación (C383C2...).

UPDATE alerta_config
SET nombre = CONVERT(BINARY CONVERT(nombre USING latin1) USING utf8mb4)
WHERE HEX(nombre) LIKE '%C383C2%';

UPDATE permissions
SET descripcion = CONVERT(BINARY CONVERT(descripcion USING latin1) USING utf8mb4)
WHERE HEX(descripcion) LIKE '%C383C2%';

UPDATE alertas
SET titulo = CONVERT(BINARY CONVERT(titulo USING latin1) USING utf8mb4)
WHERE HEX(titulo) LIKE '%C383C2%';

UPDATE alertas
SET mensaje = CONVERT(BINARY CONVERT(mensaje USING latin1) USING utf8mb4)
WHERE HEX(mensaje) LIKE '%C383C2%';

UPDATE roles
SET nombre = CONVERT(BINARY CONVERT(nombre USING latin1) USING utf8mb4)
WHERE HEX(nombre) LIKE '%C383C2%';

UPDATE roles
SET descripcion = CONVERT(BINARY CONVERT(descripcion USING latin1) USING utf8mb4)
WHERE HEX(descripcion) LIKE '%C383C2%';
