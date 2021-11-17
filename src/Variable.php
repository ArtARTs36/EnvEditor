<?php

namespace ArtARTs36\EnvEditor;

class Variable
{
    public $key;

    public $value;

    public $topComment;

    public $rightComment;

    public function __construct(
        string $key,
        $value,
        string $topComment = '',
        string $rightComment = ''
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->topComment = $topComment;
        $this->rightComment = $rightComment;
    }

    public static function fromArray(array $array): self
    {
        var_dump($array);

        return new self($array['name'], $array['value'], $array['top_comment'] ?? '', $array['right_comment'] ?? '');
    }

    /**
     * @return scalar
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
