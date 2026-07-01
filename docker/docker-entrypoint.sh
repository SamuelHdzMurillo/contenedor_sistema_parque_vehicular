#!/bin/bash
set -e

if [ -f /var/www/html/database/bootstrap.sh ]; then
    sed 's/\r$//' /var/www/html/database/bootstrap.sh | bash -s || echo "[bootstrap] ADVERTENCIA: revisa los logs anteriores."
fi

exec "$@"
