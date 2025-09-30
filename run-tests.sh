#!/bin/bash
set -e

# 默认值（如果环境变量未设置）
export WP_DB_NAME=${WP_DB_NAME:-test_db}
export WP_DB_USER=${WP_DB_USER:-root}
export WP_DB_PASSWORD=${WP_DB_PASSWORD:-root}
export WP_DB_HOST=${WP_DB_HOST:-127.0.0.1}
export WP_DIR=${WP_DIR:-$(pwd)/wp}

export SELENIUM_HOST=${SELENIUM_HOST:-127.0.0.1}
export SELENIUM_PORT=${SELENIUM_PORT:-4444}
export WP_WEB_DRIVER_URL="http://${SELENIUM_HOST}:${SELENIUM_PORT}/wd/hub"

# 新增Web服务器配置
#export WP_URL="http://localhost:8080"
#export WP_DOMAIN="localhost:8080"

echo "====== Environment Variables ======"
echo "DB: $WP_DB_USER@$WP_DB_HOST/$WP_DB_NAME"
echo "WordPress dir: $WP_DIR"
echo "Selenium: $WP_WEB_DRIVER_URL"
echo ""

echo "====== Waiting for MySQL ======"
until mysqladmin ping --protocol=tcp -h "$WP_DB_HOST" -u"$WP_DB_USER" -p"$WP_DB_PASSWORD" --silent; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "====== Waiting for Selenium ======"
until curl -s $WP_WEB_DRIVER_URL"/status" | grep -q '"ready":\s*true'; do
    echo "Waiting for Selenium..."
    sleep 2
done

echo "====== Running Unit & Integration Tests ======"
vendor/bin/codecept run Unit
vendor/bin/codecept run Integration

echo "====== Starting PHP Built-in Server for Acceptance Tests ======"

# 在 WordPress 根目录启动 PHP 内置服务器
cd "$WP_DIR"

# 指定 IPv4 地址 + 端口 + 网站根目录，日志输出到文件
php -S 127.0.0.1:8080 -t "$WP_DIR" > /tmp/php-server.log 2>&1 &
SERVER_PID=$!
echo "PHP server started with PID: $SERVER_PID"

# 等待服务器启动并可访问（最长 15 秒，每 1 秒检查一次）
echo "Waiting for PHP server to be ready..."
MAX_RETRIES=15
RETRY_COUNT=0
until curl -f -s "$WP_URL" > /dev/null; do
    sleep 1
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "ERROR: PHP server failed to start after $MAX_RETRIES seconds"
        echo "Server logs:"
        cat /tmp/php-server.log
        kill $SERVER_PID
        exit 1
    fi
done

echo "PHP server is running successfully"


echo "====== Running Acceptance Tests (Selenium / WPWebDriver) ======"
vendor/bin/codecept run Acceptance ShortcodeCest.php

echo "All tests passed!"
