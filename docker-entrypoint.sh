#!/usr/bin/env bash

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

exec "$@"