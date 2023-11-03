#!/usr/bin/env bash

CONTAINER_NAME="alexa-php";

bash -c "docker exec -i ${CONTAINER_NAME} bash -c \"supervisord -c /etc/supervisor/supervisord.conf\"";
bash -c "docker exec -i ${CONTAINER_NAME} bash -c \"supervisorctl reread\"";
bash -c "docker exec -i ${CONTAINER_NAME} bash -c \"supervisorctl update\"";
bash -c "docker exec -i ${CONTAINER_NAME} bash -c \"supervisorctl start laravel-worker:*\"";
