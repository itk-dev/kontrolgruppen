#!/usr/bin/env bash
bold=$(tput bold)
normal=$(tput sgr0)

dir=$(cd $(dirname "${BASH_SOURCE[0]}")/../../ && pwd)

mkdir -p $dir/packages/kontrolgruppen

git clone --branch=develop git@github.com:aakb/kontrolgruppen-core-bundle.git $dir/packages/kontrolgruppen/core-bundle

