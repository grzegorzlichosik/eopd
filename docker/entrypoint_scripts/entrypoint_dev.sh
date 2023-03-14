#!/bin/bash

cd /var/www/html
composer install
php artisan migrate --seed

# ATTENTION DEVELOPERS
# The following are really slow to run on your machine in the container on an M1 processor:
npm install
npm run production
/entrypoint_app.sh