FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libyaml-dev \
    && pecl install yaml pcov \
    && docker-php-ext-enable yaml pcov \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .
RUN composer install --no-interaction

EXPOSE 1337

CMD ["php", "src/main.php"]
