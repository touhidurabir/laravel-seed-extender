<?php

namespace Touhidurabir\SeedExtender\Tests;

use Orchestra\Testbench\TestCase;
use Touhidurabir\SeedExtender\Tests\App\UsersTableSeeder;
use Touhidurabir\SeedExtender\Tests\Traits\LaravelTestBootstrapping;

class SeedingProcessTest extends TestCase {

    use LaravelTestBootstrapping;

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations() {

        $this->loadMigrationsFrom(__DIR__ . '/App/database/migrations');
        
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {

            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }


    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app) {

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.url', 'http://localhost/');
        $app['config']->set('app.debug', false);
        $app['config']->set('app.key', env('APP_KEY', '1234567890123456'));
        $app['config']->set('app.cipher', 'AES-128-CBC');
    }


    /**
     * @test
     */
    public function the_seeder_class_will_seed_data() {

        (new UsersTableSeeder)->run();

        $this->assertDatabaseHas('users', [
            'email' => 'testuser1@test.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser2@test.com',
        ]);
    }
    
}