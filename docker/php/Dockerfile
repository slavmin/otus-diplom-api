FROM php:8.4-fpm

ARG HOST_UID=1000
ARG HOST_GID=1000
ARG HOST_USER=www-data
ARG HOST_GROUP=www-data
ARG HOST_TZ=UTC

ARG APP_DIR=/var/www/html/app
ARG XDEBUG_INSTALL=false

RUN mkdir -p $APP_DIR && \
    usermod -u $HOST_UID $HOST_USER && \
    groupmod -g $HOST_GID $HOST_GROUP && \
    chown -R $HOST_USER:$HOST_GROUP $APP_DIR

ENV TZ=$HOST_TZ
RUN echo "date.timezone = ${TZ:-UTC}" > ${PHP_INI_DIR}/conf.d/timezone.ini

WORKDIR $APP_DIR

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libz-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libmemcached-dev \
    libssl-dev \
    libonig-dev \
    libxml2-dev \
    libxslt-dev \
    libicu-dev \
    libreadline-dev \
    unzip \
    git \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install the PHP extentions
RUN docker-php-ext-install \
    zip \
    opcache \
    pdo_mysql \
    pcntl \
    xsl \
    && docker-php-ext-configure intl && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    --2

# COPYING config files
COPY ./php/config/php.ini ${PHP_INI_DIR}/php.ini
COPY ./php/config/fpm-pool.conf ${PHP_INI_DIR}/php-fpm.d/www.conf
COPY ./php/config/xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.ini
COPY ./php/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

#####################################
# xDebug:
#####################################
ARG XDEBUG_MODE=coverage
RUN if [ ${XDEBUG_INSTALL} = true ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=${XDEBUG_MODE}\n" >> ${PHP_INI_DIR}/conf.d/xdebug.ini \
;fi

# Switch to use a non-root user from here on
USER $HOST_USER

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping || exit 1
# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
