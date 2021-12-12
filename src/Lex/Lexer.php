<?php

namespace ArtARTs36\EnvEditor\Lex;

use Phlexy\LexerDataGenerator;
use Phlexy\LexerFactory\Stateless\UsingPregReplace;

class Lexer implements VariableLexer
{
    protected $lexer;

    public function __construct(UsingPregReplace $factory)
    {
        $this->lexer = $factory->createLexer([
            '([A-Z]|_)+'                                 => Token::VAR_NAME,
            '\='                                         => Token::ASSIGN,
            '(true|false)'                               => Token::VALUE,
            '\#(.*)\n'                                   => Token::COMMENT_BEFORE_VAR,
            '\#(.*)'                                     => Token::COMMENT_INLINE_VAR,
            // email
            '[\w\-\.]+\@([\w-]+\.)+[\w-]{2,4}'           => Token::VALUE,
            // base64
            'base64:(([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{3}\=|[A-Za-z0-9+\/]{2}\=\=)?){1}' => Token::VALUE,
            '\n'                                         => Token::NEW_LINE,
            '\w+'                                        => Token::VALUE,
            '\s+'                                        => Token::WHITESPACE,
            "\'(.*)\'"                                   => Token::VALUE,
            '[a-zA-Z_\x7f-\xff0-9.:\/\-][a-zA-Z0-9_\x7f-\xff.:\/\-]*' => Token::VALUE,
            '"(.*)"'                                     => Token::VALUE,
        ]);
    }

    public static function make(): self
    {
        return new self(new UsingPregReplace(new LexerDataGenerator()));
    }

    /**
     * @return array<Token>
     */
    public function lex(string $source): array
    {
        $tokens = [];

        foreach ($this->lexer->lex($source) as $token) {
            $tokens[] = new Token($token[0], trim($token[2]), $this->cleanFlat($token));
        }

        return $tokens;
    }

    protected function cleanFlat(array $token): string
    {
        if (! array_key_exists(3, $token) || ! array_key_exists(1, $token[3])) {
            return '';
        }

        return trim($token[3][1]);
    }
}
