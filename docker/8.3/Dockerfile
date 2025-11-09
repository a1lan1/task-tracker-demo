FROM ubuntu:24.04

LABEL maintainer="Taylor Otwell"

ARG WWWUSER
ARG WWWGROUP
ARG NODE_VERSION=22
ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=18

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"
ENV PLAYWRIGHT_BROWSERS_PATH=0

# Base system setup
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt-get update && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano

# PHP PPA and installation
RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xb8dc7e53946656efbce4c1dd71daeaab4ad4cab6' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null
RUN echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list
RUN apt-get update && apt-get install -y php8.3-cli php8.3-dev \
    php8.3-pgsql php8.3-sqlite3 php8.3-gd \
    php8.3-curl php8.3-mongodb \
    php8.3-imap php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-zip php8.3-bcmath php8.3-soap \
    php8.3-intl php8.3-readline \
    php8.3-ldap \
    php8.3-msgpack php8.3-igbinary php8.3-redis \
    php8.3-memcached php8.3-pcov php8.3-imagick php8.3-xdebug php8.3-swoole

# Composer installation
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer && rm /tmp/composer-setup.php

# Kafka extension
RUN apt-get update && apt-get install -y librdkafka-dev
RUN pecl install rdkafka
RUN echo "extension=rdkafka.so" > /etc/php/8.3/cli/conf.d/20-rdkafka.ini

# Node.js, Yarn, and other JS tools
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null
RUN echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y nodejs yarn && npm install -g npm pnpm bun && npx playwright install-deps

# PostgreSQL client
RUN curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null
RUN echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] https://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list
RUN apt-get update && apt-get install -y $MYSQL_CLIENT postgresql-client-$POSTGRES_VERSION

# Final cleanup
RUN apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# User and permissions setup
RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.3
RUN userdel -r ubuntu
RUN groupadd --force -g $WWWGROUP sail || true
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u ${WWWUSER:-1337} sail
RUN git config --global --add safe.directory /var/www/html

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.3/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]
