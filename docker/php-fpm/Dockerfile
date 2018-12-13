FROM php:fpm-alpine

ENV SYMFONY_DEPRECATIONS_HELPER "disabled"
ENV SYMFONY_ENV="dev"
ENV APP_ENV="dev"
ENV USER_NAME="safebeat-dev"

RUN apk --no-cache add \
    sudo \
    git \
    zsh \
    curl \
    libbz2 \
    libxslt \
    icu-libs

RUN addgroup -g 1000 ${USER_NAME} \
    && addgroup sudo \
    && adduser -D -u 1000 ${USER_NAME} -G ${USER_NAME} www-data -h /home/${USER_NAME} -s $(which zsh)

COPY config/sudoers /etc/sudoers
COPY config/php-fpm.conf /usr/local/etc/php-fpm.d/custom.conf

RUN curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

USER ${USER_NAME}

RUN git clone --depth 1 https://github.com/robbyrussell/oh-my-zsh /home/${USER_NAME}/.oh-my-zsh

COPY config/zshrc /home/${USER_NAME}/.zshrc

USER root

RUN apk add --no-cache --virtual build-dependencies \
    bzip2-dev \
    autoconf \
    libbz2 \
    libxslt \
    g++ \
    make \
    pcre-dev \
    openssl-dev \
    zlib-dev \
    libxml2-dev \
    libpng-dev \
    gettext-dev \
    gmp-dev \
    icu-dev \
    tidyhtml-dev \
    libzip-dev \
    libxslt-dev \
    fontconfig-dev \
    freetype-dev

RUN docker-php-ext-install -j5 sockets pdo_mysql zip intl opcache pcntl
RUN apk del build-dependencies \
    && docker-php-source delete \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/*

WORKDIR /var/www/safebeat-be
