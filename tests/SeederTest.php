<?php

namespace Touhidurabir\SeedExtender\Tests;

use Exception;
use Orchestra\Testbench\TestCase;
use Touhidurabir\SeedExtender\Seeder;
use Touhidurabir\SeedExtender\Tests\App\User;
use Touhidurabir\SeedExtender\Facades\SeedExtender;
use Touhidurabir\SeedExtender\Tests\Traits\LaravelTestBootstrapping;

class SeederTest extends TestCase {
    
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
    public function the_seeder_can_be_initialted() {

        $seeder = new Seeder;

        $this->assertTrue($seeder instanceof Seeder);
        $this->assertIsObject($seeder);
    }


    /**
     * @test
     */
    public function the_seeder_can_be_initialted_from_facade() {

        $seeder = SeedExtender::getFacadeRoot();

        $this->assertTrue($seeder instanceof Seeder);
        $this->assertIsObject($seeder);
    }

    /**
     * @test
     */
    public function the_seeder_will_run_seeding_process() {

        (new Seeder)
            ->table('users')
            ->useables(['email', 'password'])
            ->seedData([
                ['testuser1@test.com', '123456',],
            ])
            ->run();

        $this->assertDatabaseHas('users', [
            'email' => 'testuser1@test.com'
        ]);

        SeedExtender::table('users')
                    ->ignorables(['id', 'deleted_at'])
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->run();
        
        $this->assertDatabaseHas('users', [
            'email' => 'testuser2@test.com'
        ]);
    }


    /**
     * @test
     */
    public function the_seeder_will_run_seeding_process_via_model() {

        (new Seeder)
            ->table('users')
            ->useables(['email', 'password'])
            ->seedData([
                ['testuser1@test.com', '123456',],
            ])
            ->throughModel(User::class)
            ->run();

        $this->assertDatabaseHas('users', [
            'email' => 'testuser1@test.com'
        ]);

        SeedExtender::table('users')
                    ->ignorables(['id', 'deleted_at'])
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->throughModel(User::class)
                    ->withModelEvents()
                    ->run();
        
        $this->assertDatabaseHas('users', [
            'email' => 'testuser2@test.com'
        ]);
    }


    /**
     * @test
     */
    public function the_seeder_will_throw_exception_if_no_table_has_given() {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No table information has provided');

        SeedExtender::ignorables(['id', 'deleted_at'])
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->run();
    }


    /**
     * @test
     */
    public function the_seeder_will_throw_exception_if_wrong_table_name_given() {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The given some_table not found');

        SeedExtender::table('some_table')
                    ->ignorables(['id', 'deleted_at'])
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->run();
    }


    /**
     * @test
     */
    public function the_seeder_will_throw_exception_if_wrong_columns_given() {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The given columns [some_other_column] not defined in the users schema that is give for ignorables purpose');
        
        SeedExtender::table('users')
                    ->ignorables(['id', 'deleted_at', 'some_other_column'])
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->run();
    }


    /**
     * @test
     */
    public function the_seeder_will_throw_exception_if_non_existed_model_given() {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Given model Touhidurabir\SeedExtender\Tests\App\TestUser not found.");
        
        SeedExtender::table('users')
                    ->ignorables(['id', 'deleted_at'])
                    ->throughModel('Touhidurabir\\SeedExtender\\Tests\\App\\TestUser')
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->run();
    }


    /**
     * @test
     */
    public function the_seeder_will_throw_exception_if_invalid_model_given() {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Given model Touhidurabir\SeedExtender\Tests\App\UsersTableSeeder is not a valid child of base model Illuminate\Database\Eloquent\Model class");
        
        SeedExtender::table('users')
                    ->ignorables(['id', 'deleted_at'])
                    ->throughModel('Touhidurabir\\SeedExtender\\Tests\\App\\UsersTableSeeder')
                    ->seedData([
                        ['testuser2@test.com', '123456',],
                    ])
                    ->run();
    }


    /**
     * @test
     */
    public function the_seeder_will_not_store_timestamps_if_instructed() {

        (new Seeder)
            ->table('users')
            ->useables(['email', 'password'])
            ->includeTimestampsOnSeeding(false)
            ->seedData([
                ['testuser1@test.com', '123456',],
            ])
            ->run();

        $this->assertDatabaseHas('users', [
            'email' => 'testuser1@test.com'
        ]);

        $user = User::whereEmail('testuser1@test.com')->first();

        $this->assertNull($user->created_at);
        $this->assertNull($user->updated_at);
    }


    /**
     * @test
     */
    public function the_seeder_will_work_according_to_given_merge_data() {

        (new Seeder)
            ->table('users')
            ->useables(['email', 'password', 'created_at', 'updated_at'])
            ->includeTimestampsOnSeeding(false)
            ->seedData(
                [
                    ['testuser1@test.com', '123456',],
                ],
                ['2021-09-09 10:10:10', '2021-09-09 10:10:10']
            )
            ->run();
        
        $this->assertDatabaseHas('users', [
            'email' => 'testuser1@test.com',
            'created_at' => '2021-09-09 10:10:10',
            'updated_at' => '2021-09-09 10:10:10'
        ]);
    }

}