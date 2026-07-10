#!/bin/bash
set -eu

BASE_DIR="/var/www/html/database"
MIG_DIR="${BASE_DIR}/migrations"
SEED_DIR="${BASE_DIR}/seeds"

if [ -f /var/www/html/.env ]; then
    set -a
    # shellcheck disable=SC1091
    source <(grep -v '^#' /var/www/html/.env | grep -v '^\s*$' | sed 's/\r$//')
    set +a
fi

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_DATABASE:-${DB_NAME:-sicv_cecyte_bcs}}"
DB_USER="${DB_USERNAME:-${DB_USER:-parque_user}}"
DB_PASS="${DB_PASSWORD:-parque_pass}"

mysql_cmd() {
    # Forzar utf8mb4 en el cliente evita mojibake (ó → Ã³) al cargar .sql con acentos.
    mysql --skip-ssl --default-character-set=utf8mb4 -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$@"
}

wait_for_db() {
    echo "[bootstrap] Esperando MySQL en ${DB_HOST}:${DB_PORT}..."
    for _ in $(seq 1 60); do
        if mysql_cmd -e "SELECT 1" >/dev/null 2>&1; then
            echo "[bootstrap] MySQL disponible."
            return 0
        fi
        sleep 2
    done
    echo "[bootstrap] ERROR: MySQL no respondió a tiempo."
    exit 1
}

is_initialized() {
    local count
    count=$(mysql_cmd -N -e \
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='roles'" \
        2>/dev/null || echo "0")
    [ "${count:-0}" -gt 0 ]
}

ensure_default_data() {
    if ! table_exists "roles"; then
        return 0
    fi

    local role_count user_count
    role_count=$(mysql_cmd -N "$DB_NAME" -e "SELECT COUNT(*) FROM roles" 2>/dev/null || echo "0")
    user_count=$(mysql_cmd -N "$DB_NAME" -e "SELECT COUNT(*) FROM users" 2>/dev/null || echo "0")

    if [ "${role_count:-0}" -eq 0 ]; then
        echo "[bootstrap] Roles no encontrados; cargando permisos..."
        run_sql_file "${SEED_DIR}/002_seed_minimal.sql"
    fi

    if [ "${user_count:-0}" -eq 0 ]; then
        echo "[bootstrap] Usuarios no encontrados; cargando usuarios predeterminados..."
        run_sql_file "${SEED_DIR}/004_seed_users.sql"
        echo "[bootstrap] Usuarios predeterminados listos."
    fi
}

table_exists() {
    local table="$1"
    local count
    count=$(mysql_cmd -N "$DB_NAME" -e \
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}' AND table_name='${table}'" \
        2>/dev/null || echo "0")
    [ "${count:-0}" -gt 0 ]
}

column_exists() {
    local table="$1"
    local column="$2"
    local count
    count=$(mysql_cmd -N "$DB_NAME" -e \
        "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema='${DB_NAME}' AND table_name='${table}' AND column_name='${column}'" \
        2>/dev/null || echo "0")
    [ "${count:-0}" -gt 0 ]
}

apply_migration_file() {
    local file="$1"
    local base="$2"
    echo "[bootstrap] Ejecutando ${base}..."
    set +e
    sed 's/\r$//' "$file" | mysql_cmd "$DB_NAME" 2>&1
    local status=${PIPESTATUS[1]}
    set -e
    if [ "$status" -eq 0 ]; then
        return 0
    fi
    if migration_already_effective "$base"; then
        echo "[bootstrap] ${base} ya estaba aplicada; se registra como completada."
        return 0
    fi
    echo "[bootstrap] ADVERTENCIA: ${base} falló (código ${status}); se continúa el arranque."
    return 1
}

ensure_migrations_table() {
    mysql_cmd "$DB_NAME" -e "
        CREATE TABLE IF NOT EXISTS schema_migrations (
            migration VARCHAR(255) NOT NULL PRIMARY KEY,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    "
}

migration_applied() {
    local name="$1"
    local count
    count=$(mysql_cmd -N "$DB_NAME" -e \
        "SELECT COUNT(*) FROM schema_migrations WHERE migration='${name}'" \
        2>/dev/null || echo "0")
    [ "${count:-0}" -gt 0 ]
}

mark_migration_applied() {
    local name="$1"
    mysql_cmd "$DB_NAME" -e \
        "INSERT IGNORE INTO schema_migrations (migration) VALUES ('${name}')"
}

should_skip_migration() {
    local base="$1"
    case "$base" in
        000_*|001_*|012_*)
            return 0
            ;;
    esac
    return 1
}

run_sql_file() {
    local file="$1"
    apply_migration_file "$file" "$(basename "$file")"
}

migration_already_effective() {
    local base="$1"
    case "$base" in
        003_comision_salida_regreso.sql)
            column_exists "comisiones" "doc_regreso_ruta"
            ;;
        006_mantenimiento_factura.sql)
            column_exists "mantenimientos" "factura_total"
            ;;
        008_combustible_ticket.sql)
            column_exists "combustible_cargas" "folio_ticket"
            ;;
        010_*)
            table_exists "conductores"
            ;;
        013_vehiculo_alerta_config.sql)
            column_exists "alerta_config" "umbral_verde_dias"
            ;;
        016_mantenimiento_historico.sql)
            column_exists "mantenimientos" "es_historico"
            ;;
        017_mantenimiento_servicio.sql)
            column_exists "mantenimientos" "servicio"
            ;;
        019_mantenimiento_servicios.sql)
            table_exists "mantenimiento_servicios"
            ;;
        019_fix_permissions_utf8.sql)
            return 0
            ;;
        022_inspeccion_folio_combustible.sql)
            column_exists "inspecciones" "folio"
            ;;
        023_inspeccion_historico.sql)
            column_exists "inspecciones" "es_historico"
            ;;
        024_mantenimiento_intervalos.sql)
            column_exists "mantenimiento_servicios" "intervalo_dias"
            ;;
        026_tipos_gasolina.sql)
            table_exists "tipos_gasolina"
            ;;
        027_comision_tanque_adicional.sql)
            column_exists "comisiones" "tanque_adicional_litros_regreso"
            ;;
        028_comision_tanque_adicional_porcentaje.sql)
            column_exists "comisiones" "tanque_adicional_salida"
            ;;
        029_comision_tanque_tipo_gasolina.sql)
            column_exists "comisiones" "tanque_adicional_tipo_gasolina_id"
            ;;
        030_alerta_config_dias_columns.sql)
            column_exists "alerta_config" "umbral_verde_dias"
            ;;
        *)
            return 1
            ;;
    esac
}

backfill_schema_migrations() {
    local count
    count=$(mysql_cmd -N "$DB_NAME" -e "SELECT COUNT(*) FROM schema_migrations" 2>/dev/null || echo "0")
    if [ "${count:-0}" -gt 0 ]; then
        return 0
    fi

    echo "[bootstrap] Registrando migraciones previas (actualización de instalación existente)..."
    for migration in "${MIG_DIR}"/[0-9][0-9][0-9]_*.sql; do
        [ -f "$migration" ] || continue
        base=$(basename "$migration")
        if should_skip_migration "$base"; then
            mark_migration_applied "$base"
            continue
        fi
        if migration_already_effective "$base"; then
            mark_migration_applied "$base"
            continue
        fi
        case "$base" in
            010_*)
                # Pendiente: se aplicará en apply_pending_migrations.
                ;;
            *)
                # Instalaciones anteriores asumen el resto de migraciones ya aplicadas.
                mark_migration_applied "$base"
                ;;
        esac
    done
}

apply_pending_migrations() {
    ensure_migrations_table
    backfill_schema_migrations

    local applied=0
    for migration in "${MIG_DIR}"/[0-9][0-9][0-9]_*.sql; do
        [ -f "$migration" ] || continue
        base=$(basename "$migration")
        if should_skip_migration "$base"; then
            continue
        fi
        if migration_applied "$base"; then
            continue
        fi
        if migration_already_effective "$base"; then
            mark_migration_applied "$base"
            continue
        fi
        if apply_migration_file "$migration" "$base"; then
            mark_migration_applied "$base"
            applied=$((applied + 1))
        fi
    done

    if [ "$applied" -gt 0 ]; then
        echo "[bootstrap] Migraciones pendientes aplicadas: ${applied}."
    else
        echo "[bootstrap] Sin migraciones pendientes."
    fi
}

wait_for_db

if is_initialized; then
    echo "[bootstrap] Base de datos ya inicializada; verificando migraciones..."
    apply_pending_migrations
    ensure_default_data
    exit 0
fi

echo "[bootstrap] Creando base de datos y esquema..."

mysql_cmd -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

run_sql_file "${MIG_DIR}/001_schema.sql"
run_sql_file "${SEED_DIR}/002_seed_minimal.sql"

ensure_migrations_table
mark_migration_applied "001_schema.sql"

for migration in "${MIG_DIR}"/[0-9][0-9][0-9]_*.sql; do
    [ -f "$migration" ] || continue
    base=$(basename "$migration")
    if should_skip_migration "$base"; then
        continue
    fi
    if apply_migration_file "$migration" "$base"; then
        mark_migration_applied "$base"
    fi
done

run_sql_file "${SEED_DIR}/003_cleanup_demo_data.sql"
run_sql_file "${SEED_DIR}/004_seed_users.sql"
ensure_default_data

echo "[bootstrap] Base de datos lista: tablas, roles, permisos y usuarios predeterminados."
