<?php

namespace ArtARTs36\EnvEditor\Exceptions;

final class EnvNotValid extends \LogicException
{
    public function __construct($path, $code = 0, \Throwable $previous = null)
    {
        $message = "Env by {$path} not valid!";

        parent::__construct($message, $code, $previous);
    }
}
