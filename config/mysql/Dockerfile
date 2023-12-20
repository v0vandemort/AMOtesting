# Используем официальный образ MySQL с конкретной версией
FROM mysql:oracle

# Настройка пароля для пользователя root
ENV MYSQL_ROOT_PASSWORD example

# Опционально: создание базы данных и пользователя
ENV MYSQL_DATABASE mydb
ENV MYSQL_USER myuser
ENV MYSQL_PASSWORD mypassword

# Опционально: устанавливаем кодировку и временную зону
ENV MYSQL_CHARSET utf8
ENV MYSQL_COLLATION utf8_unicode_ci
ENV TZ=UTC

# Опционально: копируем скрипты и конфигурацию в контейнер
COPY ./config/mysql/my.cnf /etc/mysql/conf.d/my.cnf
COPY ./config/mysql/init.sql /docker-entrypoint-initdb.d/
