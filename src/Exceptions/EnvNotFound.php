<?php

namespace ArtARTs36\EnvEditor\Exceptions;

final class EnvNotFound extends \LogicException
{
    public function __construct($path, $code = 0, \Throwable $previous = null)
    {
        $message = "Env by {$path} not found!";

        parent::__construct($message, $code, $previous);
    }
}
