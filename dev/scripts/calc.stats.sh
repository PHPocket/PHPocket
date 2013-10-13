#!/bin/bash

# Calculates stats for provided file
# Pass filename as argument


DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $DIR"/../../"

php -r 'include "autoload.php"; $x = new \PHPocket\Dev\PHPSourceExternalsStats("'$1'"); $x->dump(); ';