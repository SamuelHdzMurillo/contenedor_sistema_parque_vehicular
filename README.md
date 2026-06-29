# Contenedor Sistema Parque Vehicular

Entorno local con **PHP 8.2 + Apache**, **MySQL 8** y **phpMyAdmin**, accesible por la IP de tu máquina en la red.

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y en ejecución

## Inicio rápido

```powershell
cd c:\xampp\htdocs\contenedor_sistema_parque_vehicular
docker compose up -d --build
```

## Obtener tu IP local

```powershell
ipconfig
```

Busca la dirección IPv4 de tu adaptador de red (ejemplo: `192.168.1.50`).

## URLs de acceso

| Servicio    | URL                          |
|-------------|------------------------------|
| Aplicación  | `http://TU_IP:8080`          |
| phpMyAdmin  | `http://TU_IP:8081`          |
| MySQL       | `TU_IP:3306`                 |

## Credenciales (por defecto)

Definidas en el archivo `.env`:

| Variable            | Valor por defecto      |
|---------------------|------------------------|
| Base de datos       | `sicv_cecyte_bcs`      |
| Usuario aplicación  | `parque_user`          |
| Contraseña app      | `parque_pass`          |
| Usuario root MySQL  | `root`                 |
| Contraseña root     | `root123`              |

En phpMyAdmin usa **root** / **root123** o el usuario de la aplicación.

### Usuarios de la aplicación (creados automáticamente)

Al levantar el contenedor por primera vez (o tras `docker compose down -v`), se crean todas las tablas y un usuario por cada rol. Las contraseñas únicas están en:

`docker/php/database/seeds/usuarios_predeterminados.txt`

| Rol                    | Correo                         |
|------------------------|--------------------------------|
| Administrador General  | `admin@cecytebcs.edu.mx`       |
| Admin. Transporte      | `transporte@cecytebcs.edu.mx`  |
| Supervisor             | `supervisor@cecytebcs.edu.mx`  |
| Responsable Vehículo   | `responsable@cecytebcs.edu.mx` |
| Consulta               | `consulta@cecytebcs.edu.mx`    |

No se cargan datos de demostración (vehículos, comisiones, catálogos, etc.); esas tablas quedan vacías.

## Inicialización de la base de datos

El contenedor **web** ejecuta `docker/php/database/bootstrap.sh` al arrancar. Si la base de datos aún no tiene el esquema, aplica migraciones, roles, permisos y usuarios predeterminados. Si ya existe, no hace nada.

Para reiniciar desde cero:

```powershell
docker compose down -v
docker compose up -d --build
```

## Dónde van los datos

Los datos persisten en volúmenes Docker (no se pierden al reiniciar):

- `parque_vehicular_mysql_data` — base de datos MySQL
- `parque_vehicular_php_sessions` — sesiones PHP

Tu código PHP va en la carpeta `docker/php/` (montada en el contenedor). La raíz web es `public/`.

## Colocar tu sistema PHP

1. Coloca o clona el proyecto dentro de `docker/php/`
2. Ajusta `docker/php/.env` con la URL y base de datos de Docker
3. En `docker/php/public/.htaccess`, usa `RewriteBase /` (no la ruta de XAMPP)

## Comandos útiles

```powershell
# Ver estado
docker compose ps

# Ver logs
docker compose logs -f

# Detener
docker compose down

# Detener y borrar datos (¡cuidado!)
docker compose down -v
```

## Firewall Windows

Si no puedes acceder desde otra PC en la red, permite los puertos **8080**, **8081** y **3306** en el Firewall de Windows.
