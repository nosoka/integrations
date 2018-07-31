<?php

return [

    \Startupbros\Libraries\Logger::class => function ($c) {
        $logger  = new Startupbros\Libraries\Logger($c->get('settings')['logger']['name']);
        $handler = new Monolog\Handler\StreamHandler($c->get('settings')['logger']['path'], Monolog\Logger::DEBUG);
        $handler->setFormatter(new Monolog\Formatter\HtmlFormatter(DateTime::RFC2822));
        $logger->pushHandler($handler);

        return $logger;
    },

    \Startupbros\Libraries\Mailer::class => function ($c) {
        return new Startupbros\Libraries\Mailer($c->get('settings'));
    },

    \Slim\Views\Twig::class => function ($c) {
        return new \Slim\Views\Twig($c->get('settings')['view']['template_path']);
    },

    // log all http requests and responses
    \GuzzleHttp\Client::class => function (\Startupbros\Libraries\Logger $logger) {
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push(\GuzzleHttp\Middleware::log(
            $logger, new \GuzzleHttp\MessageFormatter(">>>>>\n{request}\n>>>>>\n\n<<<<<\n{response}\n<<<<<\n{error}")
        ));
        return new \GuzzleHttp\Client(array('handler' => $stack));
    },
];
