FROM php:8.1-fpm

RUN apt-get update \
    # extension required for MySQL
    && docker-php-ext-install pdo_mysql \
    # extensions recommended for ramsey/uuid
    && apt-get install -y libgmp-dev && docker-php-ext-install bcmath gmp

# Clear APT cache
RUN apt-get clean && rm -rf /var/lib/apt/lists

WORKDIR /app
