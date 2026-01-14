<?php

declare(strict_types=1);

namespace AngeArsene\Chat\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);

        $this->migrate('up');
    }

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => ''
        ]);
    }

    public function tearDown(): void
    {
        $this->migrate('down');

        parent::tearDown();
    }
    
    protected function getPackageProviders($app)
    {
        return [
            \AngeArsene\Chat\Providers\ChatServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Chat' => \AngeArsene\Chat\Facades\Chat::class
        ];
    }

    private function migrate(string $direction): void
    {
        $migrations_directories = [
            'core_migrations' => dirname(__DIR__).'/database/migrations',
            'test_migrations' => __DIR__.'/helpers/database/migrations'
        ];

        foreach ($migrations_directories as $migrations_dir) {
            foreach (scandir($migrations_dir) as $migration) {
                if ($migration !== '.' && $migration !== '..') {
                    (require "$migrations_dir/$migration")->{$direction}();
                }
            }
        }
    }
}