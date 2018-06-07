FROM php:7
RUN apt-get update -y && apt-get install -y openssl zip unzip git
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


CMD php artisan serve --host=0.0.0.0 --port=5000
EXPOSE 5000
