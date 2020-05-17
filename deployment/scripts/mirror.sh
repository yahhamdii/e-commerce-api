#!/bin/bash

set -e

if [[ "${BITBUCKET_WORKSPACE}" != "oyez" ]];then echo "[WARN] Not running from oyez workspace! Skipping.."; exit 0; else echo "[INFO] Script unlocked..";fi

apt -qq update && apt -qq install git -y
echo "Mirroring repository to https://bitbucket.org/safobitbucket/${BITBUCKET_REPO_SLUG}.git"

git config user.name "Hamdi Fourati"
git config user.email "hamdi.fourati@oyez.fr"

git config --global push.default simple
git remote add safo-mirror git@bitbucket.org:safobitbucket/${BITBUCKET_REPO_SLUG}.git
git fetch --unshallow origin
git push safo-mirror

