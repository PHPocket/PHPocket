#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $DIR"/../../tests/"

while :; do clear; phpunit; sleep 5; done