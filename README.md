# P8 ToDo & Co - Readme

This project consists of taking over a legacy application, analyzing it and improving it.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

The application is designed to work with an Apache or Nginx server and a Redis cache server.

### Prerequisites

-  PHP 7.4
-  Symfony 5.3
-  Symfony CLI
-  Composer
-  Redis

Developed and tested with MariaDB, in this case the following PHP extensions are necessary:

-  pdo_mysql extension
-  mysqli extension

### Installing

A step by step series of examples that tell you how to get a development env running

1) Clone the project in your workspace of your PHP environment.

2) Install the necessary libraries via composer
   ```
   php composer install
   ```

3) Copy the .env file to .env.local and change the settings according to your needs. The parameters present in .env.local overwrite those found in .env

4) Create the database
   ```
   php bin/console doctrine:database:create
   ```

5) Make a migration and migrate it
   ```
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

6) Load fixtures
   ```
   php bin/console doctrine:fixtures:load
   ```
   
9) It's ready!

### Docker

If you want to use a ready container for this project you can build the docker-compose inside the "build" directory. Previously, you can
change the settings according to your needs.
If you are using a MySQL / MariaDB database, make sure they are on the same docker network. Here it is the "my-network" network, you can change it in the docker-compose file.

To build it:
   ```
   /build/docker-compose up -d --build
   ```

