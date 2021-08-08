#!/bin/bash

service redis-server start && tail -F /var/log/redis/redis-server.log &
service apache2 start && tail -F /var/log/apache2/error.log