#!/usr/bin/env bash

cd "$( dirname "$0" )"

Help()
{
  echo "XC55 upgrade script"
  echo
  echo "Syntax: upgrade.sh [-t type] [-l list] [-m list] [-p list] [-f] [-h]"
  echo "Options:"
  echo "t\tUpgrade Type (Possible values: build, minor, major)"
  echo "l\tModules list to upgrade (example: -l CDev-GoSocial,XC-News)"
  echo "m\tManually loaded module list to upgrade (example: -m CDev-GoSocial,XC-News)"
  echo "p\tLoaded module packs list to upgrade (example: -p XC-News:5.5.1.2) - (var/packs/XC-News-v5.5.1.2.tar.gz)"
  echo "f\tForce rebuild (Remove active scenario if exists)"
  echo "h\tPrint help and exit"
  echo
}

Type=""
List=""
Force=""

while getopts t:l:m:p:fh option; do
  case $option in
    h)
      Help
      exit;;
    t)
      Type="-t $OPTARG";;
    l)
      List="-l $OPTARG";;
    m)
      List="-m $OPTARG";;
    p)
      List="-p $OPTARG";;
    f)
      Force="-f";;
    \?)
      echo "Error: Invalid option"
      exit;;
  esac
done

echo

./service xcst:upgrade $Type $List $Force && \
./service xcst:continue
