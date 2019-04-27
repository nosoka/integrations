<?php

require 'vendor/autoload.php';
session_start();

use DI\ContainerBuilder;
class IntegrationsApp extends \DI\Bridge\Slim\App
{
    protected function configureContainer(ContainerBuilder $builder) {
        $builder->addDefinitions(__DIR__ . '/app/config/settings.php');
        $builder->addDefinitions(__DIR__ . '/app/config/dependencies.php');
    }
}
$app = new IntegrationsApp;

require 'app/config/middleware.php';
require 'app/config/routes.php';

$app->run();
