#!/usr/bin/env bash

cli_arguments=(
  "${DB_HOSTNAME:-0}"
  "${DB_PASSWORD:-0}"
  "${HTTP_SERVER:-0}"
)

is_valid=1

for cli_arg in ${cli_arguments[@]}; do
  if [ $cli_arg = 0 ]; then
    is_valid=0
  fi
done

if [ $is_valid = 1 ]; then
  if [ ! -f config.php ] || [ ! -s config.php ]; then
      until nc -z -w30 $DB_HOSTNAME ${DB_PORT:-3306}; do
          echo "Aguardando inicialização do banco de dados"
          sleep 5
      done

      password_default=$(cat /dev/urandom | tr -cd A-Za-z0-9 | head -c 10)

      cli_arguments=(
          --db_driver "pdo" \
          --db_hostname "${DB_HOSTNAME}" \
          --db_username "${DB_USERNAME:-root}" \
          --db_password "${DB_PASSWORD}" \
          --db_database "${DB_DATABASE:-opencartbrasil}" \
          --db_port "${DB_PORT:-3306}" \
          --db_prefix "${DB_PREFIX:-ocbr_}" \
          --username "${USERNAME:-admin}" \
          --password "${PASSWORD:-$password_default}" \
          --email "${EMAIL:-web@master}" \
          --http_server "${HTTP_SERVER%/}/"
      )

      php install/cli_install.php install ${cli_arguments[@]};

      if [ -z $PASSWORD ]; then
          echo -e "\nCredenciais de acesso"
          echo "Usuário: ${USERNAME:-'admin'}"
          echo "Senha: ${PASSWORD:-$password_default}"
          echo -e "Após logar, troque os dados para sua segurança\n\n\n"
      fi
  fi
fi

folders=(
  "/var/www/html/image/cache/"
  "/var/www/html/image/catalog/"
  "/var/www/html/system/storage/cache/"
  "/var/www/html/system/storage/logs/"
  "/var/www/html/system/storage/download/"
  "/var/www/html/system/storage/upload/"
  "/var/www/html/system/storage/session/"
  "/var/www/html/system/storage/modification/"
)

for folder in ${folders[@]}; do
  if [ ! -d "$folder" ]; then
    mkdir -p "$folder"
    chown -R www-data:www-data "$folder"
  fi
done

files=(
  "/var/www/html/config.php"
  "/var/www/html/admin/config.php"
)

for f in ${files[@]}; do
  if [ ! -d "$f" ]; then
    touch "$f"
    chown www-data:www-data "$f"
  fi
done

if [ ! -f composer.lock ]; then
  composer install
fi

exec "$@"