FROM php:alpine

ENV SYMFONY_DEPRECATIONS_HELPER "disabled"
ENV SYMFONY_ENV="dev"
ENV APP_ENV="dev"
ENV USER_NAME="safebeat-dev"

RUN apk --no-cache add \
        sudo \
        git \
        zsh 

RUN addgroup -g 1000 ${USER_NAME} \
    && addgroup sudo \
    && adduser -D -u 1000 ${USER_NAME} -G ${USER_NAME} www-data -h /home/${USER_NAME} -s $(which zsh)

COPY config/sudoers /etc/sudoers
COPY config/php-fpm.conf /usr/local/etc/php-fpm.d/custom.conf

RUN curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

USER ${USER_NAME}

RUN git clone --depth 1 https://github.com/robbyrussell/oh-my-zsh /home/${USER_NAME}/.oh-my-zsh

COPY config/zshrc /home/${USER_NAME}/.zshrc

WORKDIR /var/www/

USER root

