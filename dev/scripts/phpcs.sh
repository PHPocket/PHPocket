#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

${DIR}/../../vendor/bin/phpcs --standard=Zend ${DIR}/../../src/*
${DIR}/../../vendor/bin/phpcs --standard=Zend --report=summary ${DIR}/../../src/*