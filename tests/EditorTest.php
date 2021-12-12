<?php

namespace ArtARTs36\EnvEditor\Tests;

use ArtARTs36\EnvEditor\Editor;
use ArtARTs36\EnvEditor\Env;
use PHPUnit\Framework\TestCase;

final class EditorTest extends TestCase
{
    private static $readEnvPath = __DIR__ . '/.env.example';

    private static $saveEnvPath = __DIR__. '/../.env.testing';

    public function setUp(): void
    {
        parent::setUp();

        ! file_exists(self::$saveEnvPath) || unlink(self::$saveEnvPath);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        ! file_exists(self::$saveEnvPath) || unlink(self::$saveEnvPath);
    }

    public function providerForTestLoad(): array
    {
        return [
            [
                self::$readEnvPath,
                [
                    'APP_NAME' => [
                        'name' => 'APP_NAME',
                        'value' => 'EnvEditor',
                        'topComment' => '',
                        'rightComment' => 'APP_COMM',
                    ],
                    'ACTIVE' => [
                        'name' => 'ACTIVE',
                        'value' => true,
                        'topComment' => '',
                        'rightComment' => '',
                    ],
                    'KEY' => [
                        'name' => 'KEY',
                        'value' => '',
                        'topComment' => '',
                        'rightComment' => '',
                    ],
                    'BOOT' => [
                        'name' => 'BOOT',
                        'value' => false,
                        'topComment' => '',
                        'rightComment' => '',
                    ],
                    'ZERO' => [
                        'name' => 'ZERO',
                        'value' => 0,
                        'topComment' => '',
                        'rightComment' => 'ZERO_COMMENT',
                    ],
                    'VAR_DOUBLE' => [
                        'name' => 'VAR_DOUBLE',
                        'value' => 0.1,
                        'topComment' => '',
                        'rightComment' => '',
                    ],
                    'VAR_NULL' => [
                        'name' => 'VAR_NULL',
                        'value' => null,
                        'topComment' => '',
                        'rightComment' => '',
                    ],
                    'DB_PASSWORD' => [
                        'name' => 'DB_PASSWORD',
                        'value' => '${EXTERNAL_DB_PASSWORD}',
                        'topComment' => '',
                        'rightComment' => '',
                    ],
                ],
            ]
        ];
    }

    /**
     * @dataProvider providerForTestLoad
     */
    public function testLoad(string $path, array $expected): void
    {
        $env = Editor::load($path);

        self::assertEquals($expected, $env->toArray());
    }

    /**
     * @covers \ArtARTs36\EnvEditor\Editor::save
     */
    public function testSave(): void
    {
        $env = $this->loadExample();

        $env->set('APP_NAME', 'Test');
        $env->set('TEST_BOOLEAN_FALSE', false);
        $env->set('TEST_BOOLEAN_TRUE', true);
        $env->set('TEST_INTEGER_ZERO', 0);

        Editor::save($env, static::$saveEnvPath);

        //

        $env = $this->loadSavedExample();

        self::assertEquals('Test', $env->get('APP_NAME')->value);
        self::assertIsBool($env->get('TEST_BOOLEAN_FALSE')->value);
        self::assertFalse($env->get('TEST_BOOLEAN_FALSE')->value);
        self::assertIsBool($env->get('TEST_BOOLEAN_TRUE')->value);
        self::assertTrue($env->get('TEST_BOOLEAN_TRUE')->value);
        self::assertIsInt($env->get('TEST_INTEGER_ZERO')->value);
        self::assertEquals(0, $env->get('TEST_INTEGER_ZERO')->value);
    }

    /**
     * @covers \ArtARTs36\EnvEditor\Editor::relevantSave
     */
    public function testRelevantSave(): void
    {
        $env = Editor::create(static::$saveEnvPath = __DIR__ . '/.env.relevant_test_save')
            ->set('RABBIT_PASSWORD', 12345)
            ->set('APP_HOST', 'localhost')
            ->set('RABBIT_HOST', 'localhost')
            ->set('RABBIT_AUTH_HOST_PORT', '123')
            ->set('RABBIT_AUTH_HOST_POST', '123')
            ->set('RABBIT_AUTH_HOST', '123')
            ->set('APP_PORT', 8080)
            ->set('RABBIT_HEARTBEAT', 120)
            ->set('TEST_ONE', 1)
            ->set('APP_ONE', 2)
            ->set('TEST_TWO', 3)
            ->set('KUKU_A1', 2)
            ->set('KUKU_A2', 3);

        Editor::relevantSave($env);

        self::assertEquals(
            file_get_contents(__DIR__ . '/.env.relevant_save'),
            file_get_contents(static::$saveEnvPath)
        );
    }

    /**
     * @return Env
     */
    private function loadExample(): Env
    {
        return Editor::load(static::$readEnvPath);
    }

    /**
     * @return Env
     */
    private function loadSavedExample(): Env
    {
        return Editor::load(static::$saveEnvPath);
    }
}
