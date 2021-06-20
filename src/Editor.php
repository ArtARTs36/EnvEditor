<?php

namespace ArtARTs36\EnvEditor;

use ArtARTs36\EnvEditor\Exceptions\EnvNotFound;
use ArtARTs36\EnvEditor\Exceptions\EnvNotValid;
use ArtARTs36\EnvEditor\Field\Field;
use ArtARTs36\Str\Str;

class Editor
{
    /**
     * @param string $path - file path
     */
    public static function create(string $path): Env
    {
        return new Env([], $path);
    }

    /**
     * @param string $path - file path
     */
    public static function load(string $path): Env
    {
        if (! file_exists($path)) {
            throw new EnvNotFound($path);
        }

        $matches = [];

        preg_match_all('/([^=]*)=(.*)\n*/i', file_get_contents($path), $matches);

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

        return self::saveFile($env, $file, $path);
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected static function prepareValueToSave($value): string
    {
        // boolean

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        //

        if (empty($value)) {
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

    /**
     * @param mixed $value
     * @return bool|float|int|mixed|string
     */
    protected static function prepareValueToRead($value)
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

    /**
     * Save env variables by logical groups
     */
    public static function relevantSave(Env $env, string $path = null): bool
    {
        $keys = array_keys($env->getVariables());

        $root = new Field('');
        $field = $root;

        foreach ($keys as $key) {
            $parts = explode('_', $key);

            foreach ($parts as $part) {
                $field = $field->addChildren($part, next($parts) ? false : true);
            }

            $field = $root;
        }

        //

        $fields = [];
        static::getFieldsForRelevant($keys, $root, $fields);

        $file = '';
        foreach ($fields as $field) {
            if ($field === "\n") {
                $file .= "\n";

                continue;
            }

            $file .= "{$field}={$env->get($field)}\n";
        }

        return self::saveFile($env, $file, $path);
    }

    protected static function getFieldsForRelevant(array $keys, Field $root, array &$fields): void
    {
        $saveToken = function (Field $root) use (&$fields) {
            $fields[] = $root->token;

            if ($root->ancestor->isVisitedAllChildren()) {
                $fields[] = "\n";
            }
        };

        if ($root->isEndToken()) {
            $saveToken($root);

            return;
        }

        if (in_array($root->token, $keys)) {
            $saveToken($root);
        }

        foreach ($root->sort()->getChildren() as $child) {
            self::getFieldsForRelevant($keys, $child, $fields);
        }
    }

    protected static function saveFile(Env $env, string $content, ?string $path): bool
    {
        return (bool) file_put_contents($path ?? $env->getPath(), $content);
    }
}
