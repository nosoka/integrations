<?php

$container = $app->getContainer();

$container['session'] = function ($c) {
    return new \SlimSession\Helper;
};

$container['view'] = function ($c) {
    return new \Slim\Views\Twig($c['settings']['view']['template_path']);
};

$container['logger'] = function ($c) {
    $logger  = new Libraries\Logger($c['settings']['logger']['name']);
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

$container['Apps\SamCart'] = function ($c) {
    return new Apps\SamCart($c['logger']);
};
$container['Apps\Woopra'] = function ($c) {
    return new Apps\Woopra($c['logger'], $c['guzzle']);
};
$container['Apps\Improvely'] = function ($c) {
    return new Apps\Improvely($c['logger'], $c['guzzle']);
};
$container['Libraries\Mailer'] = function ($c) {
    return new Libraries\Mailer($c['settings']);
};
$container['Integrations\SamCartToWoopra'] = function ($c) {
    return new Integrations\SamCartToWoopra($c['request'], $c['response'], $c['view'],
        $c['session'], $c['logger'], $c['settings'],
        $c['Apps\SamCart'], $c['Apps\Woopra'], $c['Libraries\Mailer']);
};

$container['Integrations\SamCartToImprovely'] = function ($c) {
    return new Integrations\SamCartToImprovely($c['request'], $c['response'], $c['view'],
        $c['session'], $c['logger'], $c['settings'],
        $c['Apps\SamCart'], $c['Apps\Improvely'], $c['Libraries\Mailer']);
};
