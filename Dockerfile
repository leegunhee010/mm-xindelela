# 무료 테스트 서버(Render)용. 운영(1Panel)은 docs/DEPLOY.md 방식을 쓴다.
# PHP 8.2 + Apache, DB 는 SQLite(컨테이너 안). 재배포·재시작하면 데이터가 처음 상태로 돌아간다.
FROM php:8.2-apache

RUN apt-get update \
 && apt-get install -y --no-install-recommends libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
 && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
 && docker-php-ext-install -j"$(nproc)" intl gd \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

RUN { \
      echo 'upload_max_filesize = 20M'; \
      echo 'post_max_size = 64M'; \
      echo 'memory_limit = 256M'; \
      echo 'max_execution_time = 120'; \
      echo 'expose_php = Off'; \
    } > /usr/local/etc/php/conf.d/mm.ini

COPY docker/apache-site.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
COPY . /var/www/html
RUN chmod +x docker/start.sh

CMD ["docker/start.sh"]
