#!/bin/bash

set -e

source /tmp/google-cloud-sdk/path.bash.inc

export ENV=${1}

case  ${ENV} in
"preprod")
export GCLOUD_PROJECT=commande-com-preprod
export GCLOUD_ZONE=europe-west1-c
export KUBERNETES_CLUSTER=commande-com
;;
"qualif")
export GCLOUD_PROJECT=commande-com-qualif
export GCLOUD_ZONE=europe-west1-c
export KUBERNETES_CLUSTER=commande-com
;;
"staging")
export GCLOUD_PROJECT=safo-staging
export GCLOUD_ZONE=europe-west1-b
export KUBERNETES_CLUSTER=k8s-safo-staging
;;
"prod")
export GCLOUD_PROJECT=safo-prod
export GCLOUD_ZONE=europe-west1-b
export KUBERNETES_CLUSTER=k8s-safo-prod
;;
*)
  echo "Environment not supported!"
  exit 1;
;;
esac

# connect to cluster
gcloud container clusters get-credentials ${KUBERNETES_CLUSTER}

export IMAGE_NAME=${BITBUCKET_REPO_SLUG}
export IMAGE_TAG=${BITBUCKET_COMMIT}

echo "Test deploying version: ${IMAGE_TAG}"
helm upgrade --install ${IMAGE_NAME} deployment/${IMAGE_NAME} --values deployment/${IMAGE_NAME}/${ENV}-values.yaml --set php.image.tag=php-${IMAGE_TAG} --set nginx.image.tag=nginx-${IMAGE_TAG} --recreate-pods --dry-run=True
echo "Deploying version: ${IMAGE_TAG}"
helm upgrade --install ${IMAGE_NAME} deployment/${IMAGE_NAME} --values deployment/${IMAGE_NAME}/${ENV}-values.yaml --set php.image.tag=php-${IMAGE_TAG} --set nginx.image.tag=nginx-${IMAGE_TAG} --recreate-pods
