FROM php:8.2.9-fpm
WORKDIR "/app"

# Update
RUN apt-get update -y

RUN echo "alias ll='ls -alF'" > /etc/profile.d/alias.sh && apt-get update

# Install Packages
RUN apt-get install -y libzip-dev zip libxml2-dev libonig-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN apt-get clean -y

# Install PHP Extensions
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install soap
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install XDebug
RUN pecl install xdebug-3.2.2 && docker-php-ext-enable xdebug

# Cleanup
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
