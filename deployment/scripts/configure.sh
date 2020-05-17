#!/bin/bash

set -e

export ENV=${1}

case  ${ENV} in
"preprod")
export GCLOUD_PROJECT=commande-com-preprod
export GCLOUD_ZONE=europe-west1-c
export GCLOUD_API_KEYFILE=${GCLOUD_API_KEYFILE_PREPROD}
;;
"qualif")
export GCLOUD_PROJECT=commande-com-qualif
export GCLOUD_ZONE=europe-west1-c
export GCLOUD_API_KEYFILE=${GCLOUD_API_KEYFILE_QUALIF}
;;
"staging")
export GCLOUD_PROJECT=safo-staging
export GCLOUD_ZONE=europe-west1-b
export GCLOUD_API_KEYFILE=${GCLOUD_API_KEYFILE_STAGING}
;;
"prod")
export GCLOUD_PROJECT=safo-prod
export GCLOUD_ZONE=europe-west1-b
export GCLOUD_API_KEYFILE=${GCLOUD_API_KEYFILE_PROD}
;;
*)
  echo "Environment not supported!"
  exit 1;
;;
esac

# Google Cloud SDK
curl https://sdk.cloud.google.com -o /tmp/installer.sh
bash /tmp/installer.sh --disable-prompts  --install-dir=/tmp
source /tmp/google-cloud-sdk/path.bash.inc
# kubectl
gcloud --quiet components install kubectl

kubectl version --client

# Helm
HELM_VERSION=v2.14.1

wget https://storage.googleapis.com/kubernetes-helm/helm-${HELM_VERSION}-linux-amd64.tar.gz
tar xaf helm-${HELM_VERSION}-linux-amd64.tar.gz
mv linux-amd64/helm /usr/local/bin/helm && rm linux-amd64/*
chmod +x /usr/local/bin/helm

rm linux-amd64 helm-${HELM_VERSION}-linux-amd64.tar.gz -rf

helm version --client

# Authenticating with the service account key file
echo ${GCLOUD_API_KEYFILE} | base64 --decode --ignore-garbage > ~/gcloud-api-key.json
gcloud auth activate-service-account --key-file ~/gcloud-api-key.json
# Linking to the Google Cloud project
gcloud config set project ${GCLOUD_PROJECT}
gcloud config set compute/zone ${GCLOUD_ZONE}
