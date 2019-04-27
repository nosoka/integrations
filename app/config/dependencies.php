<?php

return [

    \App\Libraries\Logger::class => function ($c) {
        $logger  = new \App\Libraries\Logger($c->get('settings')['logger']['name']);
        $handler = new \Monolog\Handler\StreamHandler($c->get('settings')['logger']['path'], Monolog\Logger::DEBUG);
        $handler->setFormatter(new \Monolog\Formatter\HtmlFormatter(DateTime::RFC2822));
        $logger->pushHandler($handler);

        return $logger;
    },

    \App\Libraries\Mailer::class => function ($c, \Slim\Views\Twig $view) {
        return new \App\Libraries\Mailer($c->get('settings'), $view);
    },

    \Slim\Views\Twig::class => function ($c) {
        return new \Slim\Views\Twig($c->get('settings')['view']['template_path']);
    },

    // log all http requests and responses
    \GuzzleHttp\Client::class => function (\App\Libraries\Logger $logger) {
        $handler = \GuzzleHttp\HandlerStack::create();
        $handler->push(\GuzzleHttp\Middleware::log(
            $logger, new \GuzzleHttp\MessageFormatter(">>>>>\n{request}\n>>>>>\n\n<<<<<\n{response}\n<<<<<\n{error}")
        ));

        return new \GuzzleHttp\Client(['handler' => $handler]);
    },
];
