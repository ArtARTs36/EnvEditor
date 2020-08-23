<?php

namespace ArtARTs36\EnvEditor;

use ArtARTs36\EnvEditor\Exceptions\EnvNotFound;
use ArtARTs36\EnvEditor\Exceptions\EnvNotValid;
use ArtARTs36\Str\Str;

/**
 * Class Editor
 * @package ArtARTs36\EnvEditor
 */
final class Editor
{
    /**
     * @param string $path
     * @return Env
     */
    public static function create(string $path): Env
    {
        return new Env([], $path);
    }

    /**
     * @param string $path
     * @return Env
     */
    public static function load(string $path): Env
    {
        if (!file_exists($path)) {
            throw new EnvNotFound($path);
        }

        $matches = [];

        preg_match_all('/(.*)=(.*)\n/i', file_get_contents($path), $matches);

        if (count($matches) !== 3) {
            throw new EnvNotValid($path);
        }

        $variables = array_combine($matches[1], array_map('static::prepareValueToRead', $matches[2]));

        return new Env($variables, $path);
    }

    /**
     * @param Env $env
     * @param string|null $path
     * @return bool
     */
    public static function save(Env $env, string $path = null): bool
    {
        $file = '';

        foreach ($env->getVariables() as $key => $value) {
            $value = static::prepareValueToSave($value);

            $file .= "{$key}={$value}\n";
        }

        return (bool) file_put_contents($path ?? $env->getPath(), $file);
    }

    /**
     * @param mixed $value
     * @return string
     */
    private static function prepareValueToSave($value): string
    {
        if (empty($value)) {
            return '\'\'';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        // boolean

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
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

    /**
     * @param mixed $value
     * @return bool|float|int|mixed|string
     */
    private static function prepareValueToRead($value)
    {
        if (empty($value)) {
            return '';
        }

        //

        if (is_numeric($value)) {
            if (($int = intval($value)) == $value) {
                return $int;
            }

            if (($float = floatval($value)) == $value) {
                return $float;
            }
        }

        //

        $str = Str::make($value);

        if ($str->equals('true', true)) {
            return true;
        }

        if ($str->equals('false', true)) {
            return false;
        }

        //

        if (($toString = (string) $value) === $value) {
            $str = Str::make($value);

            if (($str->firstSymbol() === '\'' && $str->lastSymbol() === '\'') ||
                ($str->firstSymbol() === '"' && $str->lastSymbol() === '"')
            ) {
                return $str->cut($str->count() - 2, 1)->__toString();
            } else {
                return $toString;
            }
        }

        return $value;
    }
}
