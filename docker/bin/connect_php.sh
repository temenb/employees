#!/usr/bin/env bash

CONTAINER_NAME="alexa-php";

function greenMessage {
	local GREEN="\033[0;32m";
	local ENDCOLOR="\033[0m";
	echo -e "$(date +%s) ${GREEN} -> [${CONTAINER_NAME}] ${1} ...${ENDCOLOR}\n";
}

clear;

greenMessage 'Connecting';
sleep 2;
clear;

bash -c "(docker exec -it ${CONTAINER_NAME} bash || export \"TERM=xterm\")";
