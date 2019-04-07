<?php

namespace Checkout\Service;

use Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package Checkout\Service
 * @codeCoverageIgnore
 */
class Logger implements LoggerInterface
{
    const MSG_UNKNOWN_APPLICATION_ERROR = 'Unknown application error';

    public function alert($message, array $context = array())
    {
        /** @todo Implement alert() method. */
    }

    public function critical($message, array $context = array())
    {
        /** @todo Implement critical() method. */
    }

    public function debug($message, array $context = array())
    {
        /** @todo Implement debug() method. */
    }

    public function emergency($message, array $context = array())
    {
        /** @todo Implement emergency() method. */
    }

    public function error($message, array $context = array())
    {
        /** @todo Implement error() method. */
    }

    public function info($message, array $context = array())
    {
        /** @todo Implement info() method. */
    }

    public function log($level, $message, array $context = array())
    {
        /** @todo Implement log() method. */
    }

    public function notice($message, array $context = array())
    {
        /** @todo Implement notice() method. */
    }

    public function warning($message, array $context = array())
    {
        /** @todo Implement warning() method. */
    }
}
