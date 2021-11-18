<?php

namespace ArtARTs36\EnvEditor\Lex;

class Token
{
    public const VAR_NAME = 0;
    public const ASSIGN = 1;
    public const COMMENT_BEFORE_VAR = 3;
    public const COMMENT_INLINE_VAR = 4;
    public const NEW_LINE = 5;
    public const VALUE = 6;
    public const WHITESPACE = 7;

    public $token;

    public $value;

    public $flat;

    public function __construct(int $token, string $value, ?string $flat)
    {
        $this->token = $token;
        $this->value = $value;
        $this->flat = $flat;
    }
}
