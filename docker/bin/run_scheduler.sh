#!/bin/bash

docker exec -i alexa-php sh -c "php artisan schedule:run"
