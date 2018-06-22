#!/usr/bin/env bash

docker-compose exec node1 php artisan node:bootstrap
docker-compose exec node2 php artisan node:bootstrap