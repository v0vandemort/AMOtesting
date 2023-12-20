# Используем официальный образ Nginx с конкретной версией
FROM nginx:stable-perl

# Копируем ваш конфигурационный файл Nginx в контейнер
COPY ./config/nginx/nginx.conf /etc/nginx/templates/nginx.conf

# Опционально: Копируем статические файлы вашего проекта в контейнер
COPY ./   /usr/share/nginx/html

# Опционально: Экспонируем порт 80
EXPOSE 80

# Запускаем Nginx
CMD ["nginx", "-g", "daemon off;"]
