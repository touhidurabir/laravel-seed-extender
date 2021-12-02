<?php

namespace Touhidurabir\SeedExtender;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Touhidurabir\SeedExtender\Concerns\ColumnManager;
use Touhidurabir\SeedExtender\Concerns\SeederValidator;


abstract class BaseTableSeeder extends Seeder {

    use SeederValidator;

    /**
     * Provide table column related functionality
     */
    use ColumnManager;


	/**
     * Seeder table name 
     *
     * @var string
     */
    protected $table = null;


    /**
     * The list of table attributes/columns
     *
     * @var array
     */
    protected $columns = [];


    /**
     * If define, the seeding process will utilize the eloquent model
     *
     * @var string
     */    
    protected $model = null;


    /**
     * Determine if the seeding process should run quietly without firing any model event if seed vai model
     *
     * @var boolean
     */    
    protected $quietly = true;

    
    /**
     * The seeding data
     *
     * @var array
     */
    protected $data = [];


    /**
     * Build up the seedeable data set;
     *
     * @return array
     */
    protected function seedableDataBuilder() {

        foreach ($this->data as $key => $value) {
            
            $this->data[$key] = array_merge($value, [

            ]);
        }

        return $this->data;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $this->validate();

        $this->columns = $this->columnList($this->table);
        
    	$seedableColumns = $this->seedableColumns();
        
        $mergeableData = $this->generateTimestampMergeList();

        $self = $this;

        $saveMethod = 'save';

        if ( $this->model ) {

            if ( $this->quietly && method_exists(new $this->model, 'saveQuietly') ) {

                $saveMethod = 'saveQuietly';
            }
        }

        foreach (array_chunk($this->seedableDataBuilder(), 5000) as $data) {

            $insertables = collect($data)->map(function ($row) use ($seedableColumns, $mergeableData) {

                return array_combine($seedableColumns, array_merge($row, $mergeableData));
            });

            if ( is_null($this->model) ) {

                DB::table($this->table)->insert($insertables->toArray());

                continue;
            }

            $insertables->each(function ($insertable) use ($self, $saveMethod) {

                (new $self->model)->fill($insertable)->{$saveMethod}();
            });
        }   
    }
    
}