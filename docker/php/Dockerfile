FROM php:8.5-fpm

ARG TESTKIT_APT_FORCE_IPV4=1

RUN if [ "$TESTKIT_APT_FORCE_IPV4" = "1" ]; then echo 'Acquire::ForceIPv4 "true";' > /etc/apt/apt.conf.d/99force-ipv4; fi \
    && apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    && rm -f /etc/apt/apt.conf.d/99force-ipv4 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
