FROM php:7.4-apache

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

#PHP extensions
RUN install-php-extensions mysqli pdo_mysql intl zip @composer redis opcache

#Install GIT and ZIP
RUN apt update -y && \
    apt install git zip unzip wget -y

#Install Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

#Configure PHP date.timezone
COPY conf/phpTimeZone.ini /usr/local/etc/php/conf.d/phpTimeZone.ini
COPY conf/phpSymfony.ini /usr/local/etc/php/conf.d/phpSymfony.ini
COPY conf/000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY conf/docker-entrypoint.sh /docker-entrypoint.sh

#Install Blackfire
RUN apt install gnupg2 -y && \
    wget -q -O - https://packages.blackfire.io/gpg.key | apt-key add - && \
    echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list && \
    apt update && apt install blackfire blackfire-php -y

#Install analysis tools
RUN composer require --dev phpstan/phpstan && \
    composer require --dev squizlabs/php_codesniffer && \
    composer require --dev phpmd/phpmd && \
    composer require --dev sebastian/phpcpd && \
    composer require --dev phpmetrics/phpmetrics && \
    composer require --dev friendsoftwig/twigcs && \
    composer require --dev icanhazstring/composer-unused

RUN apt install redis-server -y

WORKDIR /var/www/html/

RUN chmod +x /docker-entrypoint.sh
ENTRYPOINT ["sh", "/docker-entrypoint.sh"]