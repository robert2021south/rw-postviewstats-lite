#!/bin/bash
set -e

# 默认值（如果环境变量未设置）
export WP_DB_NAME=${WP_DB_NAME:-test_db}
export WP_DB_USER=${WP_DB_USER:-root}
export WP_DB_PASSWORD=${WP_DB_PASSWORD:-root}
export WP_DB_HOST=${WP_DB_HOST:-mysql}
export WP_DIR=${WP_DIR:-$(pwd)/wp}

export SELENIUM_HOST=${SELENIUM_HOST:-selenium}
export SELENIUM_PORT=${SELENIUM_PORT:-4444}
export WP_WEB_DRIVER_URL="http://${SELENIUM_HOST}:${SELENIUM_PORT}/wd/hub"

echo "====== Environment Variables ======"
echo "DB: $WP_DB_USER@$WP_DB_HOST/$WP_DB_NAME"
echo "WordPress dir: $WP_DIR"
echo "Selenium: $WP_WEB_DRIVER_URL"
echo ""

echo "====== Waiting for MySQL ======"
until mysqladmin ping -h "$WP_DB_HOST" --silent; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "====== Waiting for Selenium ======"
until curl -s "http://${SELENIUM_HOST}:${SELENIUM_PORT}/wd/hub/status" | grep -q "\"ready\":\s*true"; do
    echo "Waiting for Selenium..."
    sleep 2
done

echo "====== Running Unit & Integration Tests ======"
vendor/bin/codecept run unit
vendor/bin/codecept run integration

echo "====== Running Acceptance Tests (Selenium / WPWebDriver) ======"
vendor/bin/codecept run acceptance

echo "All tests passed!"
