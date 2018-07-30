<?php namespace Libraries;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    public function event($message, array $context = array())
    {
        if(!is_string($context['data'])) {
            $context['data'] = json_encode($context['data'], JSON_PRETTY_PRINT);
        }
        if(empty($context['nolog'])) {
            $this->info($message, $context);
        }

        $_SESSION['events'][] = [
            'timestamp' => date('Y-m-d H:i:s'), 'message' => $message, 'data' => $context['data'],
        ];
    }
}
