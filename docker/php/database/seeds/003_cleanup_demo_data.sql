-- Elimina datos de demostración insertados por migraciones; conserva roles y permisos.
USE sicv_cecyte_bcs;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE comision_herramientas;
TRUNCATE TABLE comision_niveles_liquidos;
TRUNCATE TABLE comision_luces_tablero;
TRUNCATE TABLE comision_fotos;
TRUNCATE TABLE comisiones;
TRUNCATE TABLE inspeccion_luces_tablero;
TRUNCATE TABLE inspeccion_fotos;
TRUNCATE TABLE inspeccion_items;
TRUNCATE TABLE inspecciones;
TRUNCATE TABLE danio_fotos;
TRUNCATE TABLE danio_seguimiento;
TRUNCATE TABLE danios;
TRUNCATE TABLE mantenimiento_servicios;
TRUNCATE TABLE mantenimiento_fotos;
TRUNCATE TABLE mantenimientos;
TRUNCATE TABLE combustible_cargas;
TRUNCATE TABLE herramienta_reposiciones;
TRUNCATE TABLE herramientas_vehiculo;
TRUNCATE TABLE documentos;
TRUNCATE TABLE vehiculo_luces_tablero;
TRUNCATE TABLE vehiculo_luces_meta;
TRUNCATE TABLE vehiculo_fotos;
TRUNCATE TABLE vehiculo_estado_historial;
TRUNCATE TABLE vehiculo_alerta_config;
TRUNCATE TABLE vehiculos;
TRUNCATE TABLE alertas;
TRUNCATE TABLE alerta_config;
TRUNCATE TABLE auditoria;
TRUNCATE TABLE access_logs;
TRUNCATE TABLE user_sessions;
TRUNCATE TABLE password_resets;
TRUNCATE TABLE users;
TRUNCATE TABLE conductores;
TRUNCATE TABLE areas;
TRUNCATE TABLE planteles;
TRUNCATE TABLE proveedores;

SET FOREIGN_KEY_CHECKS = 1;
