#!/bin/bash

set -ex

if [[ "${BITBUCKET_WORKSPACE}" != "safobitbucket" ]];then echo "[WARN] Not running from safobitbucket workspace! Skipping.."; exit 0; else echo "[INFO] Script unlocked..";fi

export COMPOSER_MEMORY_LIMIT=-1

apt -qq update && apt -qq install -y wget git unzip python libpng-dev zlib1g-dev
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
docker-php-ext-install zip gd pdo pdo_mysql

composer install --no-interaction

bash deployment/scripts/configure.sh staging
bash deployment/scripts/build.sh staging
bash deployment/scripts/deploy.sh staging

