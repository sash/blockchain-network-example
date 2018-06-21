#!/usr/bin/env bash

docker-compose run node1 php artisan migrate:fresh ; docker-compose run node1 php artisan db:seed
docker-compose run node2 php artisan migrate:fresh ; docker-compose run node2 php artisan db:seed
docker-compose run faucet1 php artisan migrate:fresh
docker-compose run faucet2 php artisan migrate:fresh