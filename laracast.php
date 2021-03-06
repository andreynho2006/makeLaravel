#1 /usr/bin/env php_check_syntax
<?php

use Acme\NewCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$app = new Application('Laracast Demo', '1.0');

$app->add(new NewCommand(new GuzzleHttp\Client));

$app->run();