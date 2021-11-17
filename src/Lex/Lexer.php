<?php

namespace ArtARTs36\EnvEditor\Lex;

use Phlexy\LexerDataGenerator;
use Phlexy\LexerFactory\Stateless\UsingPregReplace;

class Lexer
{
    protected $lexer;

    public function __construct()
    {
        $factory = new UsingPregReplace(
            new LexerDataGenerator()
        );

        $this->lexer = $factory->createLexer([
            '([A-Z]|_)+'          => Token::VAR_NAME,
            '\='                  => Token::ASSIGN,
            '(true|false)'        => Token::VALUE,
            '^\#(.*)\n'           => Token::COMMENT_BEFORE_VAR,
            '\#(.*)'              => Token::COMMENT_INLINE_VAR,
            '\n'                  => Token::NEW_LINE,
            '\w+'                 => Token::VALUE,
            '\s+'                 => Token::WHITESPACE,
        ]);
    }

    /**
     * @return array<Token>
     */
    public function lex(string $source): array
    {
        $tokens = [];

        foreach ($this->lexer->lex($source) as $token) {
            $tokens[] = new Token($token[0], trim($token[2]), $token[3][1] ?? null);
        }

        return $tokens;
    }
}
