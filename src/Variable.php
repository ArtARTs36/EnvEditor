<?php

namespace ArtARTs36\EnvEditor;

class Variable
{
    public $name;

    public $value;

    public $topComment;

    public $rightComment;

    public function __construct(
        string $key,
        $value,
        string $topComment = '',
        string $rightComment = ''
    ) {
        $this->name = $key;
        $this->value = $value;
        $this->topComment = $topComment;
        $this->rightComment = $rightComment;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['name'],
            array_key_exists('value', $array) ? $array['value'] : '',
            $array['top_comment'] ?? '',
            $array['right_comment'] ?? ''
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
