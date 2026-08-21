#!/bin/bash
set -e

sync_vendor() {
    if [ ! -f /opt/vendor/autoload.php ]; then
        return 0
    fi

    local image_stamp volume_stamp
    image_stamp="$(cat /opt/vendor.stamp 2>/dev/null || true)"
    volume_stamp="$(cat /var/www/html/vendor/.image-stamp 2>/dev/null || true)"

    if [ -f /var/www/html/vendor/autoload.php ] && [ -n "$image_stamp" ] && [ "$image_stamp" = "$volume_stamp" ]; then
        return 0
    fi

    echo "[entrypoint] Preparando dependencias PHP..."
    mkdir -p /var/www/html/vendor
    cp -a /opt/vendor/. /var/www/html/vendor/
    if [ -n "$image_stamp" ]; then
        echo "$image_stamp" > /var/www/html/vendor/.image-stamp
    fi
}

sync_vendor

if [ -f /var/www/html/database/bootstrap.sh ]; then
    sed 's/\r$//' /var/www/html/database/bootstrap.sh | bash -s || echo "[bootstrap] ADVERTENCIA: revisa los logs anteriores."
fi

exec "$@"
