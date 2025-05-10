#!/bin/bash

echo "=== Laravel Application Installer with OJS Configuration ==="

if [ ! -f .env ]; then
  echo "Copying .env file..."
  cp .env.example .env
else
  echo ".env file already exists, skipping copy."
fi

echo "Installing Composer dependencies..."
composer install

echo "Generating application key..."
php artisan key:generate

echo "Enter main Laravel database configuration (press Enter to use default):"

read -p "DB_CONNECTION [mysql]: " DB_CONNECTION
DB_CONNECTION=${DB_CONNECTION:-mysql}

read -p "DB_HOST [127.0.0.1]: " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "DB_PORT [3306]: " DB_PORT
DB_PORT=${DB_PORT:-3306}

read -p "DB_DATABASE [ojs_connector]: " DB_DATABASE
DB_DATABASE=${DB_DATABASE:-ojs_connector}

read -p "DB_USERNAME [root]: " DB_USERNAME
DB_USERNAME=${DB_USERNAME:-root}

read -p "DB_PASSWORD []: " DB_PASSWORD

echo "Enter OJS database configuration (press Enter to use default):"

read -p "OJS_URL [http://localhost]: " OJS_URL
OJS_URL=${OJS_URL:-http://localhost}

read -p "DB_OJS_HOST [127.0.0.1]: " DB_OJS_HOST
DB_OJS_HOST=${DB_OJS_HOST:-127.0.0.1}

read -p "DB_OJS_PORT [3306]: " DB_OJS_PORT
DB_OJS_PORT=${DB_OJS_PORT:-3306}

read -p "DB_OJS_DATABASE [ojs]: " DB_OJS_DATABASE
DB_OJS_DATABASE=${DB_OJS_DATABASE:-ojs}

read -p "DB_OJS_USERNAME [root]: " DB_OJS_USERNAME
DB_OJS_USERNAME=${DB_OJS_USERNAME:-root}

read -p "DB_OJS_PASSWORD []: " DB_OJS_PASSWORD

sed -i '/^DB_CONNECTION=/d' .env
sed -i '/^DB_HOST=/d' .env
sed -i '/^DB_PORT=/d' .env
sed -i '/^DB_DATABASE=/d' .env
sed -i '/^DB_USERNAME=/d' .env
sed -i '/^DB_PASSWORD=/d' .env

sed -i '/^OJS_URL=/d' .env
sed -i '/^DB_OJS_HOST=/d' .env
sed -i '/^DB_OJS_PORT=/d' .env
sed -i '/^DB_OJS_DATABASE=/d' .env
sed -i '/^DB_OJS_USERNAME=/d' .env
sed -i '/^DB_OJS_PASSWORD=/d' .env

cat <<EOT >> .env

DB_CONNECTION=$DB_CONNECTION
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

OJS_URL=$OJS_URL
DB_OJS_HOST=$DB_OJS_HOST
DB_OJS_PORT=$DB_OJS_PORT
DB_OJS_DATABASE=$DB_OJS_DATABASE
DB_OJS_USERNAME=$DB_OJS_USERNAME
DB_OJS_PASSWORD=$DB_OJS_PASSWORD
EOT

echo "Database configurations saved to .env."

read -p "Do you want to run database migrations now? (y/n): " runMigration
if [[ "$runMigration" == "y" ]]; then
  php artisan migrate
fi

echo "=== Installation complete. You can run the app with 'php artisan serve'. ==="

echo "Deleting installation script..."
rm -- "$0"
