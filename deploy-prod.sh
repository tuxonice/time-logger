#!/bin/bash

# Usage: ./deploy-prod.sh {tag or commit}
# Folder structure
# main-folder
#   |-- releases
#     |-- v0.1.1
#   |-- shared
#     |-- var
set -e

MAIN_PATH=$(pwd)

if [ -z "$1" ]
then
    release_name=$(date +"%Y%m%d_%H%M%S")
else
    release_name=$1
fi

mkdir "releases/$release_name"

cd "releases/$release_name" || exit

git clone https://github.com/tuxonice/time-logger.git .
if [[ $# -eq 0 ]] ; then
    git checkout $release_name
fi

composer install --no-dev

rm deploy-prod.sh .env.dist .gitignore LICENSE phpcs.xml phpstan.neon README.md
rm -rf var .git tools

ln -s $MAIN_PATH/shared/.env .env
ln -s $MAIN_PATH/shared/var var

./bin/console t:g

cd $MAIN_PATH || exit

rm current
ln -s "releases/$release_name/public/" current
