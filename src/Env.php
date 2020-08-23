<?php

namespace ArtARTs36\EnvEditor;

/**
 * Class Env
 * @package ArtARTs36\EnvEditor
 */
final class Env implements \Countable
{
    /** @var array */
    private $variables;

    /** @var string */
    private $path;

    /**
     * Env constructor.
     * @param array $variables
     * @param string $path
     */
    public function __construct(array $variables, string $path)
    {
        $this->variables = $variables;
        $this->path = $path;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->variables[$key]);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->variables[$key] ?? null;
    }

    /**
     * @return int|void
     */
    public function count()
    {
        return count($this->variables);
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function save(): self
    {
        Editor::save($this);

        return $this;
    }
}
