<?php

namespace Touhidurabir\SeedExtender\Console;

use Exception;
use Throwable;
use Illuminate\Console\Command;
use Touhidurabir\StubGenerator\StubGenerator;
use Touhidurabir\SeedExtender\Concerns\SchemaHelper;
use Touhidurabir\SeedExtender\Concerns\SeederValidator;
use Touhidurabir\SeedExtender\Exceptions\SeedExtenderSchemaException;
use Touhidurabir\SeedExtender\Console\Concerns\CommandExceptionHandler;

class ExtendSeeder extends Command {

    /**
     * Process the handeled exception and provide output
     */
    use CommandExceptionHandler;

    use SchemaHelper;

    use SeederValidator;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extend-seeder
                            {class                                  : Seeder class name}
                            {--table=                               : Seeder table name}
                            {--model=                               : will run the seeding process through given eloquent model class}
                            {--ignorables=                          : Column list to ignore}
                            {--useables=                            : Column list to use}
                            {--timestampables=created_at,updated_at : The columns related to table timestamp}
                            {--no-timestamp                         : Should allow auto merge of timestamp column value of created_at and updated_at}
                            {--with-events                          : Will fire the eloquent model events when seeding through model class}
                            {--replace                              : Should replace existing class files}
                            {--strict                               : should do a strict checking of provided table and columns existance}';

    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom Extended Seeder Class';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';


    /**
     * The list of table columnable options of this command
     *
     * @var array
     */
    protected $columnableOptions = [
        'useables',
        'ignorables'
    ];


    /**
     * Class generator stub path
     *
     * @var string
     */
    protected $stubPath = '/stubs/seeder.stub';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        
        $this->info('Creating custom seeder');

        try {
            
            $this->handleStrict($this->option('strict'));

            $tableName = $this->option('table') ?? 'table_name';

            $stubGenerator = (new StubGenerator)
                                ->from($this->generateFullPathOfStubFile($this->stubPath), true)
                                ->to('/database/seeders/', true)
                                ->as($this->argument('class'))
                                ->withReplacers([
                                    'class'             => $this->argument('class'),
                                    'table'             => $tableName,
                                    // 'columns'           => $this->option('table') ? $this->columnList($tableName) : [],
                                    'ignorables'        => $this->option('ignorables') ? array_map('trim', explode(',', $this->option('ignorables'))) : [],
                                    'useables'          => $this->option('useables') ? array_map('trim', explode(',', $this->option('useables'))) : [],
                                    'timestamp'         => $this->option('no-timestamp') ? false : $this->hasColumns($tableName, array_map('trim', explode(',', $this->option('timestampables')))),
                                    'seedThroughModel'  => $this->throughModelStubber(),

                                ])
                                ->replace($this->option('replace'))
                                ->save();

            if ($stubGenerator) {

                $this->info('Custom seeder class generated successfully');
            }
            
        } catch (Throwable $exception) {
            
            $this->outputConsoleException($exception);

            return 1;
        }
    }


    /**
     * Genrate the stub file full absolute path
     *
     * @param  string $stubRelativePath
     * @return string
     */
    private function generateFullPathOfStubFile(string $stubRelativePath) {

        return __DIR__ . $stubRelativePath;
    }


    /**
     * Generate form stub segment for model based seeding and passed as string 
     *
     * @return string
     */
    private function throughModelStubber() {
        
        if ( ! $this->option('model') ) {

            return '';
        }

        return (new StubGenerator)
                ->from($this->generateFullPathOfStubFile('/stubs/seed_through_model.stub'), true)
                ->withReplacers([
                    'model'     => $this->option('model') . '::class',
                    'quietly'   => $this->option('with-events') ? false : true,
                ])
                ->toString();
    }


    /**
     * Handle strict chekcing of passed options
     *
     * @param  bool $strict
     * @return void
     * 
     * @throws Exception
     */
    private function handleStrict(bool $strict = false) {

        if ( ! $strict ) {

            return;
        }

        $this->validateTableHasProvided($this->option('table'));
        $this->validateGivenTableExists($this->option('table'));
        
        collect($this->columnableOptions)
            ->each( function($option, $index) {

                if ( $this->option($option) ) {

                    $this->validateProvidedColumnsDefinedInTable(
                        $this->option('table'),
                        array_map('trim', explode(',', $this->option($option))),
                        $option
                    );
                }
            });

        $this->validateGivenModel($this->option('model'));
    }

}
