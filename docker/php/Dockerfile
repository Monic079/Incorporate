version: '3.8'

services:

  mysql:
    image: mysql:8
    container_name: incorporate_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: incorporate_1
    ports:
      - "3307:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./database/init.sql:/docker-entrypoint-initdb.d/init.sql

  empresas:
    build: ./docker/php
    container_name: empresas_app
    volumes:
      - ./empresas:/var/www/html
    ports:
      - "8001:8000"
    depends_on:
      - mysql

  alumnos:
    build: ./docker/php
    container_name: alumnos_app
    volumes:
      - ./alumnos:/var/www/html
    ports:
      - "8002:8000"
    depends_on:
      - mysql

  admin:
    build: ./docker/php
    container_name: admin_app
    volumes:
      - ./admin:/var/www/html
    ports:
      - "8003:8000"
    depends_on:
      - mysql

volumes:
  db_data:
    FROM php:8.2-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

CMD php -S 0.0.0.0:8000 -t /var/www/htmlsss