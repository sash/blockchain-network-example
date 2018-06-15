FROM php:7

RUN apt-get update -y && apt-get install -y openssl zip unzip git gnupg2
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo mbstring
WORKDIR /app

RUN pecl install scrypt
RUN echo "extension=scrypt.so" > /usr/local/etc/php/conf.d/scrypt.ini


RUN apt-get update && apt-get install -y libgmp-dev libpng-dev
RUN docker-php-ext-install gmp

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - && \
  apt-get install -y nodejs

#yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg |  apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" |  tee /etc/apt/sources.list.d/yarn.list

RUN  apt-get update &&  apt-get install yarn

RUN docker-php-ext-install pdo_mysql

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/xdebug.ini
CMD php artisan serve --host=0.0.0.0 --port=80
EXPOSE 80