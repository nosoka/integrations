<?php

// TODO:: find a better way to store temp data instead of $_session
$_SESSION['integrationId'] = \Zend\Math\Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
$_SESSION['logfile'] = __DIR__ . '/../../logs/' . date('Y_m_d-H_i_s-') . $_SESSION['integrationId'] . '.html';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

return [
    'settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),

        'view' => [
            'template_path' => __DIR__ . '/../templates'
        ],

        'logger' => [
            'name' => 'Integration',
            'path' => $_SESSION['logfile'],
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
