<?php

namespace ArtARTs36\EnvEditor\Variable;

use ArtARTs36\Str\Str;

class ValueTypeCaster
{
    public function castToRead($value)
    {
        $valueString = Str::make($value);

        if ($valueString->isEmpty()) {
            return '';
        }

        //

        if ($valueString->isDigit()) {
            if (($int = intval($value)) == $value) {
                return $int;
            }

            if (($float = floatval($value)) == $value) {
                return $float;
            }
        }

        //

        if ($valueString->equals('true', true)) {
            return true;
        }

        if ($valueString->equals('false', true)) {
            return false;
        }

        //

        if (($toString = (string) $value) === $value) {
            if (($valueString->firstSymbol() === '\'' && $valueString->lastSymbol() === '\'') ||
                ($valueString->firstSymbol() === '"' && $valueString->lastSymbol() === '"')
            ) {
                return $valueString->cut($valueString->count() - 2, 1)->__toString();
            } else {
                return $toString;
            }
        }

        return $value;
    }
}
