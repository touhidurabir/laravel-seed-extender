<?php

namespace Touhidurabir\SeedExtender\Console;

use Throwable;
use Exception;
use Illuminate\Console\Command;
use Touhidurabir\StubGenerator\StubGenerator;
use Touhidurabir\SeedExtender\Concerns\SchemaHelper;
use Touhidurabir\SeedExtender\Exceptions\SeedExtenderSchemaException;
use Touhidurabir\SeedExtender\Console\Concerns\CommandExceptionHandler;

class ExtendSeeder extends Command {

    /**
     * Process the handeled exception and provide output
     */
    use CommandExceptionHandler;

    use SchemaHelper;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:extend-seeder
                            {class                                  : Seeder class name}
                            {--table=                               : Seeder table name}
                            {--ignorables=                          : Column list to ignore}
                            {--useables=                            : Column list to use}
                            {--timestampables=created_at,updated_at : The columns related to table timestamp}
                            {--no-timestamp                         : Should allow auto merge of timestamp column value of created_at and updated_at}
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

            $stubGenerator = (new StubGenerator)
                                ->from($this->generateFullPathOfStubFile($this->stubPath), true)
                                ->to('/database/seeders/', true)
                                ->as($this->argument('class'))
                                ->withReplacers([
                                    'class'         => $this->argument('class'),
                                    'table'         => $this->option('table'),
                                    // 'columns'       => $this->option('table') ? $this->columnList($this->option('table')) : [],
                                    'ignorables'    => $this->option('ignorables') ? array_map('trim', explode(',', $this->option('ignorables'))) : [],
                                    'useables'      => $this->option('useables') ? array_map('trim', explode(',', $this->option('useables'))) : [],
                                    'timestamp'     => $this->option('no-timestamp') ? false : $this->hasColumns($this->option('table'), array_map('trim', explode(',', $this->option('timestampables')))),
                                ])
                                ->replace($this->option('replace'))
                                ->save();

            if ($stubGenerator) {

                $this->info('Custom seeder class generated successfully');
            }
            
        } catch (Throwable $exception) {

            ray($exception);
            
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

        if ( ! $this->option('table') ) {

            throw SeedExtenderSchemaException::noTableGiven();
        }

        if ( !$this->tableExists($this->option('table')) ) {

            throw SeedExtenderSchemaException::tableNotFound($this->option('table'));
        }

        collect($this->columnableOptions)
            ->each( fn($option, $index) => $this->validateGivenColumnsList($option) );

    }


    /**
     * Validate the give columns lists 
     *
     * @param  string $option
     * @return void
     * 
     * @throws Exception
     */
    private function validateGivenColumnsList(string $option = null) {

        if ( ! $this->option($option) ) {

            return;
        }

        $columns = array_map('trim', explode(',', $this->option($option)));

        if ( $this->hasColumns($this->option('table'), $columns) ) {

            return;
        }

        throw SeedExtenderSchemaException::tableColumnsNotDefined(
            $this->option('table'), 
            $this->arbitraryColumns($this->option('table'), $columns), 
            "{$option} option"
        );
    }   

}
