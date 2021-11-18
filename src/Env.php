<?php

namespace ArtARTs36\EnvEditor;

use ArtARTs36\Str\Facade\Str;

class Env implements \Countable
{
    private $variables;

    private $path;

    /**
     * @param array<string, Variable> $variables
     */
    public function __construct(array $variables, string $path)
    {
        $this->variables = $variables;
        $this->path = $path;
    }

    public function has(string $key): bool
    {
        return isset($this->variables[$key]);
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): self
    {
        if (array_key_exists($key, $this->variables)) {
            $this->variables[$key]->value = $value;
        } else {
            $this->variables[$key] = new Variable($key, $value);
        }

        return $this;
    }

    /**
     * @return Variable|null
     */
    public function get(string $key)
    {
        return $this->variables[$key] ?? null;
    }

    public function count()
    {
        return count($this->variables);
    }

    /**
     * @return array<string, Variable>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function save(): self
    {
        Editor::save($this);

        return $this;
    }

    public function getVariablesByPrefix(string $prefix, bool $removePrefix = false): array
    {
        $variables = [];
        $prefixLength = mb_strlen($prefix);

        foreach ($this->variables as $key => $value) {
            if (Str::startsWith($key, $prefix)) {
                $newKey = $key;

                if ($removePrefix) {
                    $newKey = Str::cut($newKey, null, $prefixLength);
                }

                $variables[$newKey] = $value;
            }
        }

        return $variables;
    }

    public function toArray(): array
    {
        $array = [];

        foreach ($this->variables as $variable) {
            $array[$variable->name] = $variable->toArray();
        }

        return $array;
    }
}
