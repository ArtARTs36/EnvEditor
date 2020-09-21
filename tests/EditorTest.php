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

        !file_exists(static::$saveEnvPath) || unlink(static::$saveEnvPath);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        !file_exists(static::$saveEnvPath) || unlink(static::$saveEnvPath);
    }

    public function testLoad(): void
    {
        $env = Editor::load(__DIR__ . '/.env.example');

        self::assertTrue($env->has('APP_NAME'));
        self::assertTrue($env->has('ACTIVE'));
        self::assertTrue($env->has('KEY'));
        self::assertTrue($env->has('BOOT'));
        self::assertFalse($env->has('random_key'));

        //

        self::assertEquals('EnvEditor', $env->get('APP_NAME'));
        self::assertTrue($env->get('ACTIVE'));
        self::assertEmpty($env->get('KEY'));
        self::assertFalse($env->get('BOOT'));

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

        Editor::save($env, static::$saveEnvPath);

        //

        $env = $this->loadSavedExample();

        self::assertEquals('Test', $env->get('APP_NAME'));
        self::assertIsBool($env->get('TEST_BOOLEAN_FALSE'));
        self::assertFalse($env->get('TEST_BOOLEAN_FALSE'));
        self::assertIsBool($env->get('TEST_BOOLEAN_TRUE'));
        self::assertTrue($env->get('TEST_BOOLEAN_TRUE'));
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
