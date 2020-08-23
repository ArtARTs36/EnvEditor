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

        Editor::save($env, static::$saveEnvPath);

        //

        $env = $this->loadSavedExample();

        self::assertEquals('Test', $env->get('APP_NAME'));
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
