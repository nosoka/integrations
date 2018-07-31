<?php

$container = $app->getContainer();

$container['session'] = function ($c) {
    return new \SlimSession\Helper;
};

$container['view'] = function ($c) {
    return new \Slim\Views\Twig($c['settings']['view']['template_path']);
};

$container['logger'] = function ($c) {
    $logger  = new Startupbros\Libraries\Logger($c['settings']['logger']['name']);
    $handler = new Monolog\Handler\StreamHandler($c['settings']['logger']['path'], Monolog\Logger::DEBUG);
    $handler->setFormatter(new Monolog\Formatter\HtmlFormatter(DateTime::RFC2822));
    $logger->pushHandler($handler);

    return $logger;
};

$container['guzzle'] = function ($c) {

    $stack = \GuzzleHttp\HandlerStack::create();
    $stack->push(\GuzzleHttp\Middleware::log(
        $c['logger'], new \GuzzleHttp\MessageFormatter(">>>>>\n{request}\n>>>>>\n\n<<<<<\n{response}\n<<<<<\n{error}")
    ));
    return new \GuzzleHttp\Client(array('handler' => $stack));
    return new \GuzzleHttp\Client();
};

$container['Startupbros\Apps\SamCart'] = function ($c) {
    return new Startupbros\Apps\SamCart($c['logger']);
};
$container['Startupbros\Apps\Woopra'] = function ($c) {
    return new Startupbros\Apps\Woopra($c['logger'], $c['guzzle']);
};
$container['Startupbros\Apps\Improvely'] = function ($c) {
    return new Startupbros\Apps\Improvely($c['logger'], $c['guzzle']);
};
$container['Startupbros\Libraries\Mailer'] = function ($c) {
    return new Startupbros\Libraries\Mailer($c['settings']);
};
$container['Startupbros\Integrations\SamCartToWoopra'] = function ($c) {
    return new Startupbros\Integrations\SamCartToWoopra($c['request'], $c['response'], $c['view'],
        $c['session'], $c['logger'], $c['settings'],
        $c['Startupbros\Apps\SamCart'], $c['Startupbros\Apps\Woopra'], $c['Startupbros\Libraries\Mailer']);
};

$container['Startupbros\Integrations\SamCartToImprovely'] = function ($c) {
    return new Startupbros\Integrations\SamCartToImprovely($c['request'], $c['response'], $c['view'],
        $c['session'], $c['logger'], $c['settings'],
        $c['Startupbros\Apps\SamCart'], $c['Startupbros\Apps\Improvely'], $c['Startupbros\Libraries\Mailer']);
};
