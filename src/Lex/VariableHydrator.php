<?php

namespace ArtARTs36\EnvEditor\Lex;

use ArtARTs36\EnvEditor\Variable;

class VariableHydrator
{
    protected $lexer;

    protected $rules = [
        // Старт
        0 => [
            Token::COMMENT_BEFORE_VAR => 1,
            Token::VAR_NAME => 2,
            Token::NEW_LINE => 0,
        ],
        // Присвоение комментария перед переменной
        1 => [
            Token::VAR_NAME => 2,
            Token::COMMENT_BEFORE_VAR => 1, // Двойные комментарии
            Token::NEW_LINE => 0,
        ],
        // Присвоение имени переменной
        2 => [
            Token::ASSIGN => 6,
        ],
        // Присвоение значения переменной
        3 => [
            Token::WHITESPACE => 7,
            Token::NEW_LINE => 5,
            Token::VALUE => 3,
            Token::ASSIGN => 8,
            #Token::VAR_NAME => 0,
        ],
        // Присвоение комментария справа от значения переменной
        4 => [
            'any' => 5,
        ],
        // Создание Variable
        5 => [
            Token::COMMENT_BEFORE_VAR => 1,
            Token::VAR_NAME => 2,
            Token::NEW_LINE => 0,
        ],
        // Пропуск Token::ASSIGN
        6 => [
            Token::VALUE => 3,
            Token::NEW_LINE => 5,
            Token::VAR_NAME => 3,
        ],
        // Пропуск Token::WHITE_SPACE
        7 => [
            'any' => 4,
        ],
        8 => [
            'any' => 5,
        ],
    ];

    protected $actions = [];

    protected $typeCaster;

    public function __construct(VariableLexer $lexer, ValueNormalizer $typeCaster)
    {
        $this->lexer = $lexer;
        $this->actions = [
            0 => function () {
            },
            1 => function (Token $token, array &$variable) {
                if (! array_key_exists('top_comment', $variable)) {
                    $variable['top_comment'] = $token->flat;
                } else {
                    $variable['top_comment'] = implode(". ", [
                        $variable['top_comment'],
                        $token->flat,
                    ]);
                }
            },
            2 => function (Token $token, array &$variable) {
                $variable['name'] = $token->value;
            },
            3 => function (Token $token, array &$variable) {
                $variable['value'] = $this->typeCaster->toRead(($variable['value'] ?? '') . $token->value);
            },
            4 => function (Token $token, array &$variable) {
                $variable['right_comment'] = $token->flat;
            },
            5 => function (Token $token, array &$variable) {
                $var = Variable::fromArray($variable);

                $variable = [];

                return $var;
            },
            6 => function () {
            },
            7 => function () {
            },
            8 => function (Token $token, array &$variable) {
                $variable['value'] = '';
            }
        ];

        $this->typeCaster = $typeCaster;
    }

    /**
     * @return array<Variable>
     */
    public function hydrate(string $content): array
    {
        $variables = [];
        $lastVariable = [];
        $state = 0;

        foreach ($this->lexer->lex($content) as $token) {
            $state = $this->rules[$state][$token->token] ?? $this->rules[$state]['any'] ?? null;

            if ($state === null) {
                throw new \RuntimeException('Not valid source');
            }

            $variable = $this->actions[$state]($token, $lastVariable);

            if ($variable instanceof Variable) {
                $variables[$variable->name] = $variable;
            }
        }

        return $variables;
    }
}
