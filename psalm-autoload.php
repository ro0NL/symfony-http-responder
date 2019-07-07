<?php

if (!is_file('vendor/autoload.php')) {
    echo "Run `make install` first.\n";
    exit(1);
}
require_once 'vendor/autoload.php';

if (!is_file('var/phpunit/vendor/autoload.php')) {
    echo "Run `make phpunit-pull` first.\n";
    exit(1);
}
require_once 'var/phpunit/vendor/autoload.php';
