#######################################################################################
# DON'T EDIT THIS FILE! IT'S A COPY OF THE MAIN DOCKERFILE AND HAS BEEN CREATED
# JUST TO USE THE CI IMAGE TO PROVIDE THE SAME TOOLS IN CI TO THE DEVELOPER
#######################################################################################

FROM threadable/linuxnginxphp_ci_20.04:2022.09.01

RUN update-alternatives --set php /usr/bin/php8.1

ARG SRC_FOLDER=./src

EXPOSE 443

RUN apt update \
    && apt install -y clamav clamav-freshclam \
    && apt autoremove \
    && apt clean

# UBUNTU BASIC SETUP
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN echo "#!/bin/sh\nexit 0" > /usr/sbin/policy-rc.d

# CREATE START SCRIPTS
COPY ./docker/entrypoint_scripts/entrypoint_app.sh /entrypoint_app.sh
RUN chmod 755 /entrypoint_app.sh

COPY ./docker/entrypoint_scripts/entrypoint_jobs.sh /entrypoint_jobs.sh
RUN chmod 755 /entrypoint_jobs.sh

COPY ./docker/entrypoint_scripts/entrypoint_dev.sh /entrypoint_dev.sh
RUN chmod 755 /entrypoint_dev.sh

# APP AND JOBS SETUP
COPY ./docker/configs/backend/laravel.ini /etc/php/8.1/fpm/conf.d/99-laravel.ini
COPY ./docker/configs/backend/laravel.ini /etc/php/8.1/cli/conf.d/99-laravel.ini
COPY ./docker/configs/backend/laravel.pool.conf /etc/php/8.1/fpm/pool.d/www.conf
COPY ./docker/configs/backend/jobs/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# SELF SIGNED SSL TEMPLATE
RUN chmod 600 /etc/nginx/certs
COPY ./docker/configs/backend/app/openssl.cnf /etc/ssl_certs/openssl.cnf

# NGINX SETUP
COPY ./docker/configs/backend/app/nginx.conf /etc/nginx/sites-enabled/default

# CLAMAV CRON SETUP
COPY ./docker/configs/cron/cron_scan.sh /etc/cron.hourly/cron_scan.sh
RUN chmod 755 /etc/cron.hourly/cron_scan.sh

# Bitbucket keys
COPY ./docker/configs/composer_auth.json /root/.config/composer/auth.json

# Set container content for publishing
WORKDIR /var/www/html
COPY --chown=www-data:www-data ${SRC_FOLDER} /var/www/html