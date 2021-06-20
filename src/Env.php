<?php

namespace ArtARTs36\EnvEditor;

final class Env implements \Countable
{
    private $variables;

    private $path;

    /**
     * @param array<string, mixed> $variables
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
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * @return mixed|null
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
     * @return array<string, mixed>
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
}
