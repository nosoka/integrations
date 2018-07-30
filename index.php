<?php

require 'vendor/autoload.php';
session_start();

$app = new \Slim\App(require 'app/config/settings.php');
require 'app/config/dependencies.php';
require 'app/config/middleware.php';
require 'app/config/routes.php';

$app->run();
