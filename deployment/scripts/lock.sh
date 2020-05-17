#!/bin/bash

set -e

#TODO: make it prettier!
# Lock mirror to oyez repo
if [[ -z "${1}" ]];then echo "[ERROR] Missing workspace!";exit 1;fi

if [[ "${BITBUCKET_WORKSPACE}" != "${1}" ]];then echo "[WARN] Not running from ${1} workspace! Skipping.."; exit 0;fi
