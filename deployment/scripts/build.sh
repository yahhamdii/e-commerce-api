#!/bin/bash
set -e

source /tmp/google-cloud-sdk/path.bash.inc


export ENV=${1}

case  ${ENV} in
"preprod"|"qualif")
export GCLOUD_PROJECT=commande-com-preprod
;;
"staging"|"prod")
export GCLOUD_PROJECT=safo-staging
;;
*)
  echo "[ERROR] Environment not supported!"
  exit 1;
;;
esac

export IMAGE_NAME=${BITBUCKET_REPO_SLUG}
export IMAGE_TAG=${BITBUCKET_COMMIT}

yes y | gcloud auth configure-docker
# Build php-fpm
docker build -t eu.gcr.io/${GCLOUD_PROJECT}/${IMAGE_NAME}:php-${IMAGE_TAG} . --target php
# Build nginx
docker build -t eu.gcr.io/${GCLOUD_PROJECT}/${IMAGE_NAME}:nginx-${IMAGE_TAG} . --target nginx

docker push eu.gcr.io/${GCLOUD_PROJECT}/${IMAGE_NAME}:php-${IMAGE_TAG}
docker push eu.gcr.io/${GCLOUD_PROJECT}/${IMAGE_NAME}:nginx-${IMAGE_TAG}
