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
    mysql --skip-ssl -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$@"
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

run_sql_file() {
    local file="$1"
    echo "[bootstrap] Ejecutando $(basename "$file")..."
    sed 's/\r$//' "$file" | mysql_cmd "$DB_NAME"
}

wait_for_db

if is_initialized; then
    echo "[bootstrap] Base de datos ya inicializada; se omite."
    exit 0
fi

echo "[bootstrap] Creando base de datos y esquema..."

mysql_cmd -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

run_sql_file "${MIG_DIR}/001_schema.sql"
run_sql_file "${SEED_DIR}/002_seed_minimal.sql"

for migration in "${MIG_DIR}"/[0-9][0-9][0-9]_*.sql; do
    [ -f "$migration" ] || continue
    base=$(basename "$migration")
    case "$base" in
        000_*|001_*|012_*)
            continue
            ;;
    esac
    run_sql_file "$migration"
done

run_sql_file "${SEED_DIR}/003_cleanup_demo_data.sql"
run_sql_file "${SEED_DIR}/004_seed_users.sql"

echo "[bootstrap] Base de datos lista: tablas, roles, permisos y usuarios predeterminados."
