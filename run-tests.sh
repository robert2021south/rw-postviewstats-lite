#!/bin/bash
set -e

# 默认值（如果环境变量未设置）
export WP_DB_NAME=${WP_DB_NAME:-test_db}
export WP_DB_USER=${WP_DB_USER:-root}
export WP_DB_PASSWORD=${WP_DB_PASSWORD:-root}
export WP_DB_HOST=${WP_DB_HOST:-127.0.0.1}
export WP_DIR=${WP_DIR:-$(pwd)/wp}
export PLUGIN_DIR="$(pwd)"   # 当前目录就是仓库根目录

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

# 确认 WordPress 根目录
#echo "WP_DIR=$WP_DIR"
#ls -la "$WP_DIR"

# 等待数据库就绪
echo "Waiting for MySQL to be ready..."
until mysqladmin ping -h 127.0.0.1 -P 3306 --silent; do
    echo "MySQL not ready yet..."
    sleep 1
done
echo "MySQL is ready"

# 启动 PHP 内置服务器
cd "$WP_DIR"
php -S 127.0.0.1:8080 -t "$WP_DIR" > /tmp/php-server.log 2>&1 &
SERVER_PID=$!
echo "PHP server started with PID: $SERVER_PID"

# 设置 WordPress URL
WP_URL="http://127.0.0.1:8080"

# 等待服务器启动（最长 60 秒，每秒检测一次）
echo "Waiting for PHP server to be ready..."
MAX_RETRIES=60
RETRY_COUNT=0
until curl -f -s "$WP_URL" > /dev/null; do
    sleep 1
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "ERROR: PHP server failed to start after $MAX_RETRIES seconds"
        echo "Server logs:"
        cat /tmp/php-server.log
        echo "Debug: first 500 chars of homepage"
        curl -s "$WP_URL" | head -c 500
        kill $SERVER_PID
        exit 1
    fi
done

echo "PHP server is running successfully"
echo "Debug: first 500 chars of homepage"
curl -s "$WP_URL" | head -c 500


echo "====== Running Acceptance Tests (Selenium / WPWebDriver) ======"
# 回到插件根目录
cd "$PLUGIN_DIR"
vendor/bin/codecept run Acceptance ShortcodeCest.php

echo "All tests passed!"
