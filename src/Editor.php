<?php

namespace ArtARTs36\EnvEditor;

use ArtARTs36\EnvEditor\Exceptions\EnvNotFound;
use ArtARTs36\EnvEditor\Exceptions\EnvNotValid;
use ArtARTs36\EnvEditor\Field\Field;
use ArtARTs36\EnvEditor\Lex\Lexer;
use ArtARTs36\EnvEditor\Lex\VariableHydrator;
use ArtARTs36\EnvEditor\Variable\ValueNormalizer;

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

        $source = rtrim(file_get_contents($path)) . "\n";

        try {
            $variables = (new VariableHydrator(new Lexer(), new ValueNormalizer()))->hydrate($source);
        } catch (\RuntimeException $exception) {
            throw new EnvNotValid($path, 0, $exception);
        }

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

        $normalizer = new ValueNormalizer();

        foreach ($env->getVariables() as $variable) {
            $value = $normalizer->toSave($variable->value);

            if ($variable->topComment) {
                $file .= '#' . $variable->topComment . "\n";
            }

            $file .= "{$variable->key}={$value}\n";
        }

        return self::saveFile($env, $file, $path);
    }

    /**
     * Save env variables by logical groups
     */
    public static function relevantSave(Env $env, ?string $path = null): bool
    {
        $keys = array_keys($env->getVariables());

        $root = new Field('');
        $field = $root;

        foreach ($keys as $key) {
            $parts = explode('_', $key);

            foreach ($parts as $part) {
                $field = $field->addChildren($part, ! next($parts));
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
