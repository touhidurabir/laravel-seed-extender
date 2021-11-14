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
        
        foreach (array_chunk($this->seedableDataBuilder(), 5000) as $data) {

            DB::table($this->table)->insert(
                array_map(function ($row) use ($seedableColumns, $mergeableData) {
                    return array_combine(
                        $seedableColumns, 
                        array_merge($row, $mergeableData)
                    );
                }, $data)
            );
        }   
    }
    
}