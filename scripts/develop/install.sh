#!/usr/bin/env bash
bold=$(tput bold)
normal=$(tput sgr0)

dir=$(cd $(dirname "${BASH_SOURCE[0]}")/../../ && pwd)

if [ -d $dir/packages/kontrolgruppen ]; then
    (>&2 echo Package exits)
    exit
fi

mkdir -p $dir/packages/kontrolgruppen

git clone --branch=develop https://github.com/aakb/kontrolgruppen-core-bundle.git $dir/packages/kontrolgruppen/core-bundle

