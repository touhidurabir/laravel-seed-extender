<?php

namespace Touhidurabir\SeedExtender\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Touhidurabir\SeedExtender\Tests\Traits\FileHelpers;
use Touhidurabir\SeedExtender\Tests\Traits\LaravelTestBootstrapping;

class CommandTest extends TestCase {

    use LaravelTestBootstrapping;

    use FileHelpers;

    /**
     * The non removeable repository files
     *
     * @var array
     */
    protected $cleanUpExcludeFileNames = [
        'DatabaseSeeder.php',
    ];


    /**
     * Seeder class store full absolute directory path
     *
     * @var string
     */
    protected $seederStoreFullPath;


    /**
     * Generate the seeder class store full absolute directory path
     *
     * @return void
     */
    protected function seederFileStorePath() {

        $this->seederStoreFullPath = $this->sanitizePath(
            str_replace('/public', '/database/seeders/', public_path())
        );
    }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void {

        parent::setUp();

        $this->seederFileStorePath();

        $self = $this;

        $this->beforeApplicationDestroyed(function () use ($self) {

            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();

            foreach(glob($self->seederStoreFullPath . '*.php') as $fileFullPath) {
                
                if ( ! in_array( last(explode('/', $fileFullPath)), $self->cleanUpExcludeFileNames ) ) {

                    File::delete($fileFullPath);
                }
            }
        });
    }


    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations() {

        $this->loadMigrationsFrom(__DIR__ . '/App/database/migrations');
        
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
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


    #[Test]
    public function the_extend_seeder_command_will_run() {

        $this->artisan('make:extend-seeder', [
            'class'         => 'UsersTableSeeder',
            '--table'       => 'users',
            '--model'       => 'Touhidurabir\\SeedExtender\\Tests\\App\\User',
            '--ignorables'  => 'id,deleted_at',
        ])->assertExitCode(0);

        $this->artisan('make:extend-seeder ProfilesTableSeeder --table=profiles --useables=first_name,last_name')->assertExitCode(0);
    }


    #[Test]
    public function it_will_geneate_proper_seeder_with_proper_content() {

        $this->artisan('make:extend-seeder UsersTableSeeder --table=users --ignorables=id,deleted_at')->assertExitCode(0);

        $this->assertEquals(
            trim(File::get($this->seederStoreFullPath . 'UsersTableSeeder.php')),
            trim(File::get(__DIR__ . '/App/UsersTableSeederEmpty.php'))
        );
    }


    // #[Test]
    // public function it_will_geneate_seeder_without_specifying_table_name() {

    //     $this->artisan('make:extend-seeder UsersTableSeeder --ignorables=id,deleted_at')->assertExitCode(0);

    //     $this->assertEquals(
    //         trim(File::get($this->seederStoreFullPath . 'UsersTableSeeder.php')),
    //         trim(File::get(__DIR__ . '/App/UsersTableSeederWithoutTableName.php'))
    //     );
    // }


    #[Test]
    public function it_will_fail_to_generate_seeder_file_if_already_exists_and_not_instructed_to_replace() {

        $this->artisan('make:extend-seeder UsersTableSeeder --table=users --ignorables=id,deleted_at')->assertExitCode(0);

        $this->artisan('make:extend-seeder UsersTableSeeder --table=users --ignorables=id,deleted_at')->assertExitCode(1);
    }


    #[Test]
    public function it_will_run_properly_in_strict_mode() {

        $this->artisan('make:extend-seeder UsersTableSeeder --table=users --ignorables=id,deleted_at --strict')->assertExitCode(0);
    }


    #[Test]
    public function it_will_fail_in_strict_mode_if_wrong_information_provided() {

        // failure when wrong/non-existed table given
        $this->artisan('make:extend-seeder AddressTableSeeder --table=addresses --ignorables=id,deleted_at --strict --replace')->assertExitCode(1);

        // failure when no table given
        $this->artisan('make:extend-seeder UsersTableSeeder --ignorables=id,bio,deleted_at --strict --replace')->assertExitCode(1);


        // failure when invalid/non-existed columns given
        $this->artisan('make:extend-seeder UsersTableSeeder --table=users --ignorables=id,bio,deleted_at --strict --replace')->assertExitCode(1);
        $this->artisan('make:extend-seeder UsersTableSeeder --table=users --useables=id,bio,deleted_at --strict --replace')->assertExitCode(1);

        // failure when invalid/non-existed model class given
        $this->artisan('make:extend-seeder', [
            'class'         => 'UsersTableSeeder',
            '--table'       => 'users',
            '--model'       => 'Touhidurabir\\SeedExtender\\Tests\\App\\TestUser', // invalid model class
            '--ignorables'  => 'id,deleted_at',
            '--strict'      => true,
            '--replace'     => true,
        ])->assertExitCode(1);
    }

}