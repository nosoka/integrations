<?php

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

// TODO:: find a better way to store temp data instead of using $_session
$_SESSION['events']   = [];
$_SESSION['eventLog'] = __DIR__ . '/../../logs/' . date('Ymd-His-') . rand() . '.html';

return [
    'settings' => [

        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'httpVersion' => '2',
        'determineRouteBeforeAppMiddleware' => false,

        'addContentLengthHeader' => false,
        'displayErrorDetails' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),

        'view' => [
            'template_path' => __DIR__ . '/../templates'
        ],

        'logger' => [
            'name' => 'Integration',
            'path' => $_SESSION['eventLog'],
        ],
        'email' => [
            'driver' => getenv('MAIL_DRIVER'), // ex: mail/sendmail/smtp

            'smtp' => [
                'server' => getenv('MAIL_HOST'),
                'port' => getenv('MAIL_PORT'),
                'username' => getenv('MAIL_USERNAME'),
                'password' => getenv('MAIL_PASSWORD'),
                'encryption' => getenv('MAIL_ENCRYPTION'),
            ],

            'from' => [
                'address' => getenv('MAIL_FROM_ADDRESS'),
                'name' => getenv('MAIL_FROM_NAME'),
            ],

            'to' => [
                'address' => getenv('MAIL_TO_ADDRESS'),
                'name' => getenv('MAIL_TO_NAME'),
            ],
        ],
    ],
];
