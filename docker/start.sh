#!/bin/sh
# Render 컨테이너 시작: .env 생성 → DB 준비(처음이면 데모 데이터) → 관리자 비밀번호·구글맵 키 → Apache
set -e
cd /var/www/html

export PORT="${PORT:-10000}"
BASE="${RENDER_EXTERNAL_URL:-http://localhost:${PORT}}"

mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar public/uploads

cat > .env <<EOF
CI_ENVIRONMENT = production
app.baseURL = '${BASE%/}/'
app.indexPage = ''
app.forceGlobalSecureRequests = false
database.default.DBDriver = SQLite3
database.default.database = mm.db
database.default.hostname = ''
database.default.username = ''
database.default.password = ''
database.default.DBDebug = false
database.default.foreignKeys = false
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.cookieName = 'mm_session'
session.expiration = 2592000
EOF

php spark migrate --all
if [ ! -f writable/.seeded ]; then
  php spark db:seed DemoSeeder && touch writable/.seeded
fi
php docker/render-setup.php

chown -R www-data:www-data writable public/uploads
echo "Listen ${PORT}" > /etc/apache2/ports.conf
exec apache2-foreground
