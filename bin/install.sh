#!/usr/bin/env bash

cd "$( dirname "$0" )"

Help()
{
  echo "XC55 installation script"
  echo
  echo "Syntax: install.sh [-b build-name] [-l lng] [-a admin-credentials] [-d|p] [-m] [-k license-key] [-h]"
  echo "Options:"
  echo "-b Set build name (example: build/default, build/auto, built/marketplace, etc. See: service-tool//resources folder), default is build/default"
  echo "-l Set language, default is en"
  echo "-a Set admin credentials (example: login@mail.ru:password)"
  echo "-d Development mode, default"
  echo "-p Production mode"
  echo "-r Resize all images"
  echo "-m Download external modules"
  echo "-k License key"
  echo "-h Print help and exit"
  echo
}

BuildName="build/default"
Lang="en"
Additional="additional/dev"
ResizeAllImages="no"
DownloadExternalModules="no"

while getopts b:l:a:k:dphrm option; do
  case $option in
    h)
      Help
      exit;;
    b)
      BuildName=${OPTARG};;
    l)
      Lang=${OPTARG};;
    a)
      AdminCredentials=${OPTARG};;
    d)
      Additional="additional/dev";;
    p)
      Additional="additional/prod";;
    r)
      ResizeAllImages="yes";;
    m)
      DownloadExternalModules="yes";;
    k)
      LicenseKey=${OPTARG};;
    \?)
      echo "Error: Invalid option"
      exit;;
  esac
done

Spec=../var/install.yaml
if [[ -f "$Spec" ]]; then
  echo "Install XCart"
  head -n 1 "$Spec"
  ResizeAllImages="yes"
  DownloadExternalModules="no"
else
  echo "Install spec:"
  echo "Build name: ${BuildName}"
  echo "Language: ${Lang}"
  echo "Additional mutations: ${Additional}"
  echo "Resize all images: ${ResizeAllImages}"
  echo "Download external modules: ${DownloadExternalModules}"
fi

echo

rm -rf ../var/workers/*

../service-tool/bin/install.sh -f $@ && \

./service xcst:install $BuildName $Lang $Additional --resizeAllImages=$ResizeAllImages --adminCredentials=$AdminCredentials --downloadExternalModules=$DownloadExternalModules --licenseKey=$LicenseKey
