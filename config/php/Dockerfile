## Используем официальный образ PHP с конкретной версией
FROM php:fpm
#
#RUN docker-php-ext-install pdo_mysql
#
#    # Опционально: устанавливаем дополнительные зависимости и настройки PHP
## RUN apt-get update && apt-get install -y ...
#
## Опционально: устанавливаем дополнительные расширения PHP
## RUN docker-php-ext-install ...
#
## Опционально: копируем ваши PHP-скрипты и конфигурации в контейнер
#COPY ./app /var/www/html
#FROM php:7.1-apache
#COPY apache2.conf /etc/apache2
RUN docker-php-ext-install pdo pdo_mysql

# for mysqli if you want
#RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY ./ /var/www/html

