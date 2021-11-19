<?php

namespace ArtARTs36\EnvEditor\Tests;

use ArtARTs36\EnvEditor\Variable;
use PHPUnit\Framework\TestCase;

final class VariableTest extends TestCase
{
    public function providerForTestComment(): array
    {
        return [
            [
                [
                    'name' => 'var',
                ],
                '',
            ],
            [
                [
                    'name' => 'var',
                    'top_comment' => 'top_c',
                ],
                'top_c',
            ],
            [
                [
                    'name' => 'var',
                    'right_comment' => 'right_c',
                ],
                'right_c',
            ],
            [
                [
                    'name' => 'var',
                    'top_comment' => 'top__',
                    'right_comment' => 'right__',
                ],
                "top__\nright__",
            ],
        ];
    }

    /**
     * @dataProvider providerForTestComment
     * @covers \ArtARTs36\EnvEditor\Variable::comment
     */
    public function testComment(array $variable, string $expected): void
    {
        $variable = Variable::fromArray($variable);

        self::assertEquals($expected, $variable->comment());
    }
}
