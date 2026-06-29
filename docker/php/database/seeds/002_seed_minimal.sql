-- Roles, permisos y asignaciones (sin datos operativos)
USE sicv_cecyte_bcs;

INSERT INTO roles (slug, nombre, descripcion) VALUES
('admin_general', 'Administrador General', 'Acceso completo al sistema. Puede crear, editar y eliminar usuarios; consultar la auditoría de cambios; y gestionar todos los módulos: vehículos, comisiones, inspecciones, daños, mantenimiento, combustible, proveedores, herramientas, documentos, alertas y reportes. Rol de más alto nivel, destinado a dirección general o responsable de TI.'),
('admin_transporte', 'Administrador de Transporte', 'Gestión operativa integral del parque vehicular. Puede administrar vehículos, comisiones, inspecciones, daños, mantenimiento, combustible, proveedores, herramientas y documentación; configurar alertas y exportar reportes. No puede crear, modificar ni eliminar usuarios del sistema ni consultar la auditoría de cuentas.'),
('supervisor', 'Supervisor', 'Supervisa y autoriza la operación del parque. Puede consultar y actualizar información en la mayoría de módulos, autorizar comisiones y mantenimientos, y exportar reportes. No crea ni elimina registros operativos ni gestiona usuarios. Ideal para jefes de área o coordinadores de transporte.'),
('responsable_vehiculo', 'Responsable de Vehículo', 'Operación diaria de las unidades asignadas. Puede registrar comisiones, inspecciones, reportes de daños y cargas de combustible; actualizar el estado de sus vehículos y herramientas; y consultar expedientes, alertas y reportes de sus unidades. No autoriza comisiones ni mantenimientos ni accede a la configuración del sistema.'),
('consulta', 'Consulta', 'Acceso de solo lectura. Puede consultar vehículos, expedientes, comisiones, inspecciones, mantenimiento, combustible y alertas; ver el panel principal y exportar reportes informativos. No puede crear, editar ni eliminar ningún registro. Pensado para personal administrativo o consulta externa.');

INSERT INTO permissions (slug, modulo, accion, descripcion) VALUES
('usuarios.read', 'usuarios', 'read', 'Ver usuarios del sistema'),
('usuarios.create', 'usuarios', 'create', 'Registrar nuevos usuarios'),
('usuarios.update', 'usuarios', 'update', 'Editar usuarios existentes'),
('usuarios.delete', 'usuarios', 'delete', 'Eliminar usuarios'),
('vehiculos.read', 'vehiculos', 'read', 'Ver vehículos'),
('vehiculos.create', 'vehiculos', 'create', 'Registrar vehículos'),
('vehiculos.update', 'vehiculos', 'update', 'Editar vehículos'),
('vehiculos.delete', 'vehiculos', 'delete', 'Dar de baja vehículos'),
('expediente.read', 'expediente', 'read', 'Consultar expediente digital'),
('comisiones.read', 'comisiones', 'read', 'Ver comisiones'),
('comisiones.create', 'comisiones', 'create', 'Registrar comisiones'),
('comisiones.update', 'comisiones', 'update', 'Editar comisiones'),
('comisiones.delete', 'comisiones', 'delete', 'Cancelar comisiones'),
('comisiones.authorize', 'comisiones', 'authorize', 'Autorizar comisiones'),
('inspecciones.read', 'inspecciones', 'read', 'Ver inspecciones'),
('inspecciones.create', 'inspecciones', 'create', 'Registrar inspecciones'),
('inspecciones.update', 'inspecciones', 'update', 'Editar inspecciones'),
('inspecciones.delete', 'inspecciones', 'delete', 'Eliminar inspecciones'),
('danios.read', 'danios', 'read', 'Ver reportes de daños'),
('danios.create', 'danios', 'create', 'Reportar daños'),
('danios.update', 'danios', 'update', 'Actualizar daños'),
('mantenimiento.read', 'mantenimiento', 'read', 'Ver mantenimientos'),
('mantenimiento.create', 'mantenimiento', 'create', 'Registrar mantenimientos'),
('mantenimiento.update', 'mantenimiento', 'update', 'Editar mantenimientos'),
('mantenimiento.authorize', 'mantenimiento', 'authorize', 'Autorizar mantenimientos'),
('mantenimiento.delete', 'mantenimiento', 'delete', 'Eliminar mantenimientos'),
('proveedores.read', 'proveedores', 'read', 'Ver proveedores'),
('proveedores.create', 'proveedores', 'create', 'Registrar proveedores'),
('proveedores.update', 'proveedores', 'update', 'Editar proveedores'),
('catalogos.read', 'catalogos', 'read', 'Ver catálogos (planteles, áreas, conductores y servicios)'),
('catalogos.create', 'catalogos', 'create', 'Dar de alta en catálogos'),
('catalogos.update', 'catalogos', 'update', 'Editar catálogos'),
('combustible.read', 'combustible', 'read', 'Ver cargas de combustible'),
('combustible.create', 'combustible', 'create', 'Registrar cargas de combustible'),
('combustible.update', 'combustible', 'update', 'Editar cargas de combustible'),
('herramientas.read', 'herramientas', 'read', 'Ver herramientas del vehículo'),
('herramientas.update', 'herramientas', 'update', 'Actualizar herramientas'),
('documentos.read', 'documentos', 'read', 'Ver documentos'),
('documentos.create', 'documentos', 'create', 'Subir documentos'),
('documentos.update', 'documentos', 'update', 'Actualizar documentos'),
('alertas.read', 'alertas', 'read', 'Ver alertas'),
('alertas.config', 'alertas', 'config', 'Configurar alertas'),
('dashboard.read', 'dashboard', 'read', 'Ver panel principal'),
('reportes.export', 'reportes', 'export', 'Exportar reportes'),
('auditoria.read', 'auditoria', 'read', 'Consultar auditoría del sistema');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE slug NOT IN ('usuarios.create', 'usuarios.update', 'usuarios.delete', 'auditoria.read');

INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE accion IN ('read', 'update', 'authorize') OR slug = 'reportes.export';

INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions WHERE slug IN (
    'vehiculos.read', 'vehiculos.update', 'expediente.read',
    'comisiones.read', 'comisiones.create', 'comisiones.update',
    'inspecciones.read', 'inspecciones.create', 'inspecciones.update',
    'danios.read', 'danios.create', 'danios.update',
    'mantenimiento.read', 'combustible.read', 'combustible.create', 'combustible.update',
    'proveedores.read',
    'herramientas.read', 'herramientas.update', 'documentos.read',
    'alertas.read', 'dashboard.read', 'reportes.export', 'catalogos.read'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions WHERE accion = 'read' OR slug IN ('expediente.read', 'dashboard.read', 'reportes.export');
