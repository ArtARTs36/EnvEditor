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
        ],
        // Присвоение имени переменной
        2 => [
            Token::ASSIGN => 6,
        ],
        // Присвоение значения переменной
        3 => [
            Token::WHITESPACE => 7,
            Token::NEW_LINE => 5,
        ],
        // Присвоение комментария справа от значения переменной
        4 => [
            'any' => 5,
        ],
        // Создание Variable
        5 => [
            Token::COMMENT_BEFORE_VAR => 1,
            Token::VAR_NAME => 2,
            Token::NEW_LINE => 1,
        ],
        // Пропуск Token::ASSIGN
        6 => [
            'any' => 3,
        ],
        7 => [
            'any' => 4,
        ],
    ];

    protected $actions = [];

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->actions = [
            1 => function (Token $token, array &$variable) {
                $variable['top_comment'] = $token->flat;
            },
            2 => function (Token $token, array &$variable) {
                $variable['name'] = $token->value;
            },
            3 => function (Token $token, array &$variable) {
                $variable['value'] = $token->value;
            },
            4 => function (Token $token, array &$variable) {
                $variable['right_comment'] = $token->value;
            },
            5 => function (Token $token, array &$variable) {
                $var = Variable::fromArray($variable);

                $variable = [];

                return $var;
            },
            6 => function () {},
            7 => function () {},
        ];
    }

    public function hydrate(string $content)
    {
        $variables = [];
        $lastVariable = [];
        $state = 0;

        foreach ($this->lexer->lex($content) as $token) {
            $state = $this->rules[$state][$token->token] ?? $this->rules[$state]['any'];

            $variable = $this->actions[$state]($token, $lastVariable);

            if ($variable instanceof Variable) {
                $variables[$variable->key] = $variable;
            }
        }
    }
}
