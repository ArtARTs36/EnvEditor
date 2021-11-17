<?php

namespace ArtARTs36\EnvEditor\Tests;

use ArtARTs36\EnvEditor\Editor;
use ArtARTs36\EnvEditor\Env;
use ArtARTs36\EnvEditor\Lex\Lexer;
use ArtARTs36\EnvEditor\Lex\VariableHydrator;
use PHPUnit\Framework\TestCase;

final class EditorTest extends TestCase
{
    private static $readEnvPath = __DIR__ . '/.env.example';

    private static $saveEnvPath = __DIR__. '/../.env.testing';

    public function setUp(): void
    {
        parent::setUp();

        !file_exists(static::$saveEnvPath) || unlink(static::$saveEnvPath);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        !file_exists(static::$saveEnvPath) || unlink(static::$saveEnvPath);
    }

    public function testLoad(): void
    {
        $hydrator = new VariableHydrator(new Lexer());

        $hydrator->hydrate(file_get_contents(__DIR__ . '/.env.example'));

        die();

        $env = Editor::load(__DIR__ . '/.env.example');

        self::assertTrue($env->has('APP_NAME'));
        self::assertTrue($env->has('ACTIVE'));
        self::assertTrue($env->has('KEY'));
        self::assertTrue($env->has('BOOT'));
        self::assertTrue($env->has('ZERO'));
        self::assertFalse($env->has('random_key'));

        //

        self::assertEquals('EnvEditor', $env->get('APP_NAME')->getValue());
        self::assertTrue($env->get('ACTIVE')->getValue());
        self::assertEmpty($env->get('KEY')->getValue());
        self::assertFalse($env->get('BOOT')->getValue());
        self::assertEquals(0, $env->get('ZERO')->getValue());

        //
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

        self::assertEquals('Test', $env->get('APP_NAME')->getValue());
        self::assertIsBool($env->get('TEST_BOOLEAN_FALSE')->getValue());
        self::assertFalse($env->get('TEST_BOOLEAN_FALSE')->getValue());
        self::assertIsBool($env->get('TEST_BOOLEAN_TRUE')->getValue());
        self::assertTrue($env->get('TEST_BOOLEAN_TRUE')->getValue());
        self::assertIsInt($env->get('TEST_INTEGER_ZERO')->getValue());
        self::assertEquals(0, $env->get('TEST_INTEGER_ZERO')->getValue());
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
