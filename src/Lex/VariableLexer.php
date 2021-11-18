<?php

namespace ArtARTs36\EnvEditor\Lex;

interface VariableLexer
{
    /**
     * @return array<Token>
     */
    public function lex(string $source): array;
}
