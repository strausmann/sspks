FROM alpine:3.15
LABEL maintainer="Julien Del-Piccolo <julien@del-piccolo.com>"
LABEL branch=${BRANCH}
LABEL commit=${COMMIT}

USER root

# gitpod trick to bypass the docker caching mechanism for all lines below this one
# just increment the value each time you want to bypass the cache system
ENV INVALIDATE_CACHE=2

COPY . /var/www/localhost/htdocs/

RUN apk update && apk add --no-cache apache2 php7-iconv php7-mbstring php7-apache2 php7-phar php7-ctype php7-json \
    && apk upgrade \
    && apk add --virtual=.build-dependencies curl openssl php7 php7-openssl git \
    && rm -f /var/www/localhost/htdocs/index.html \
    && curl -sSL https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    && cd /var/www/localhost/htdocs \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-progress --prefer-dist --no-ansi --no-cache \
    && rm -f /usr/local/bin/composer \
    && apk del .build-dependencies \
    && rm -rf /var/cache/apk/* \
    && sed -i 's/Listen 80/Listen 8080/' /etc/apache2/httpd.conf \
    && sed -i 's/^variables_order = "GPCS"/variables_order = "EGPCS"/' /etc/php7/php.ini \
    && ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log \
    && ln -sf /var/www/localhost/htdocs/packages /packages \
    && ln -sf /var/www/localhost/htdocs/cache /cache

EXPOSE 8080
VOLUME "/packages"
VOLUME "/cache"
CMD ["/usr/sbin/httpd", "-DFOREGROUND"]
