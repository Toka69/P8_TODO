FROM php:7.4

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

#PHP extensions
RUN install-php-extensions mysqli pdo_mysql xdebug intl zip @composer

#Install GIT and ZIP
RUN apt update -y && \
    apt install git zip unzip wget -y

#Install Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony

#Configure PHP date.timezone
COPY conf/phpTimezone.ini /usr/local/etc/php/conf.d/phpTimezone.ini

#Install Blackfire
RUN apt install gnupg2 -y && \
    wget -q -O - https://packages.blackfire.io/gpg.key | apt-key add - && \
    echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list && \
    apt update && apt install blackfire blackfire-php -y

#Install Panther
RUN apt install -y zlib1g-dev chromium
ENV PANTHER_NO_SANDBOX 1
RUN composer require symfony/panther --dev && \
    composer require --dev dbrekelmans/bdi && \
    vendor/bin/bdi detect drivers

#Install analysis tools
RUN composer require --dev phpstan/phpstan && \
    composer require --dev squizlabs/php_codesniffer && \
    composer require --dev phpmd/phpmd && \
    composer require --dev sebastian/phpcpd && \
    composer require --dev phpmetrics/phpmetrics && \
    composer require --dev friendsoftwig/twigcs && \
    composer require --dev icanhazstring/composer-unused

WORKDIR workspaces/p8/