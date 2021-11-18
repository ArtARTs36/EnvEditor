<?php

namespace ArtARTs36\EnvEditor\Variable;

use ArtARTs36\Str\Str;

class ValueNormalizer
{
    /**
     * @return scalar
     */
    public function toRead($value)
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

    /**
     * @param scalar $value
     * @return string
     */
    public function toSave($value): string
    {
        // boolean

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        //

        if ($value === '') {
            return '\'\'';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        //

        $str = Str::make($value);

        if (($toString = (string) $value) === $value) {
            if (($str->firstSymbol() === '\'' && $str->lastSymbol() === '\'') ||
                ($str->firstSymbol() === '"' && $str->lastSymbol() === '"')
            ) {
                return $toString;
            } else {
                return "'{$toString}'";
            }
        }

        return $value;
    }
}
