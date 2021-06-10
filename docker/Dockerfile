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

#Install Panther
RUN apt install -y zlib1g-dev chromium
ENV PANTHER_NO_SANDBOX 1
RUN composer require symfony/panther --dev && \
    composer require --dev dbrekelmans/bdi && \
    vendor/bin/bdi detect drivers

WORKDIR workspaces/p8/