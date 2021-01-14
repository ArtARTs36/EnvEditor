<?php

namespace ArtARTs36\EnvEditor\Field;

class Field
{
    public $token;

    public $isEndToken;

    /** @var static[] */
    public $children;

    /** @var static */
    public $ancestor;

    public $visited = false;

    public function __construct(string $token, bool $isEndToken = false)
    {
        $this->token = $token;
        $this->isEndToken = $isEndToken;
    }

    public function addChildren(string $token, bool $isEndToken = false): self
    {
        if (! isset($this->children[$token])) {
            $newToken = $this->token ? $this->token . '_' . $token : $token;

            $this->children[$token] = new static($newToken, $isEndToken);
            $this->children[$token]->ancestor = $this;
        }

        return $this->children[$token];
    }

    public function sort(): self
    {
        ksort($this->children);

        return $this;
    }

    /**
     * @return static[]
     */
    public function getChildren(): array
    {
        $this->visited = true;

        return $this->children;
    }

    public function isVisitedAllChildren(): bool
    {
        if (! $this->children) {
            return false;
        }

        foreach ($this->children as $child) {
            if ($child->visited === false) {
                return false;
            }
        }

        return true;
    }

    public function isEndToken(): bool
    {
        $this->visited = true;

        return $this->isEndToken;
    }
}
