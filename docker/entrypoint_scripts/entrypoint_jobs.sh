#!/bin/bash

touch /var/www/html/storage/logs/php.log /var/www/html/storage/logs/php_cron.log /var/www/html/storage/logs/php_queuer.log
chmod 777 /var/www/html/storage/logs/php.log /var/www/html/storage/logs/php_cron.log /var/www/html/storage/logs/php_queuer.log /var/log/supervisor/supervisord.log

set +e

if [ ! -f /var/www/html/.env ]; then

  # Due AWS differences in the env command, we need o sed it
 env |
    sed 's/"//g' |
    sed 's/=\(.*\)/="\1"/' \
      >/etc/environment

  # In case PHP and Laravel have issues to read env vars, we will still dump it here
  cp /etc/environment /var/www/html/.env

  echo "* * * * * cd /var/www/html/; php artisan schedule:run > /var/www/html/storage/logs/php_cron.log" >/etc/cron.d/laravel-cron
  chmod 0644 /etc/cron.d/laravel-cron
  crontab /etc/cron.d/laravel-cron

  # CREATES LOCK
  touch /initialized

fi

mkdir -p storage/framework/{session,views,cache}
chmod -R 775 storage/framework

set -e

ls -la /var/www/html
ls -la /var/www/html/storage
ls -la /var/www/html/storage/framework

supervisord -c /etc/supervisor/supervisord.conf -l /var/www/html/storage/logs/queuer.log
service cron start

#/entrypoint_app.sh &

tail -f /var/www/html/storage/logs/php.log /var/www/html/storage/logs/laravel.log /var/www/html/storage/logs/php_cron.log /var/www/html/storage/logs/queuer.log  > /dev/stdout
