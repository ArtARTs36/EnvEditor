<?php

namespace ArtARTs36\EnvEditor;

class Variable
{
    protected $key;

    protected $value;

    protected $comment;

    public function __construct(string $key, $value, string $comment = '')
    {
        $this->key = $key;
        $this->value = $value;
        $this->comment = $comment;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param scalar $value
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return scalar
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
