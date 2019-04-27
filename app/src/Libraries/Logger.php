<?php namespace App\Libraries;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    public function info($message, array $context = array()) {
        $context['level'] = 'info';
        $this->event($message, $context);
    }

    public function warn($message, array $context = array()) {
        $context['level'] = 'warning';
        $this->event($message, $context);
    }

    public function error($message, array $context = array()) {
        $context['level'] = 'error';
        $this->event($message, $context);
    }

    public function success($message, array $context = array()) {
        $context['level'] = 'success';
        $this->event($message, $context);
    }

    public function event($message, array $context = array()) {

        $level             = $context['level'];
        $data              = $context['data'];
        $monologlevel      = is_numeric($this->toMonologLevel($level)) ? $this->toMonologLevel($level) : 200;
        $context['data']   = !is_string($data) ? json_encode($data, JSON_PRETTY_PRINT) : $data;

        $this->addRecord($monologlevel, $message, $context);
        $_SESSION['events'][] = ['timestamp' => date('Y-m-d H:i:s'), 'message' => $message, 'context' => $context];
    }
}
