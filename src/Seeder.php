<?php

namespace Touhidurabir\SeedExtender;

use Touhidurabir\SeedExtender\BaseTableSeeder;
use Touhidurabir\SeedExtender\Exceptions\SeedExtenderSchemaException;

class Seeder extends BaseTableSeeder {
    
    /**
     * Set the seeding table
     *
     * @param  string $table
     * @return self
     */
    public function table(string $table) {
        
        $this->table = $table;        

        return $this;
    }


    /**
     * Set the useable seeding columns
     *
     * @param  array $columns
     * @return self
     */
    public function useables(array $columns = []) {

        $this->useables = $columns;

        return $this;
    }


    /**
     * Set the ignoreable seeding columns
     *
     * @param  array $columns
     * @return self
     */
    public function ignorables(array $columns = []) {

        $this->ignorables = $columns;
        
        return $this;
    }


    /**
     * Define if seeding data will by default include the timestamp data
     *
     * @param  bool $withTimestamp
     * @return self
     */
    public function includeTimestampsOnSeeding(bool $withTimestamp = true) {

        $this->includeTimestampsOnSeeding = $withTimestamp;

        return $this;
    }


    /**
     * Set the seeding data
     *
     * @param  array $data
     * @param  array $mergeables
     * 
     * @return self
     */
    public function seedData(array $data = [], array $mergeables = []) {

        foreach ($data as $key => $value) {
            
            $data[$key] = array_merge($value, $mergeables);
        }

        $this->data = $data;

        return $this;
    }


    /**
     * Run the seeding process
     *
     * @return void
     */
    public function run() {

        $this->validate();

        $this->columns = $this->columnList($this->table);

        parent::run();
    }


    /**
     * Validate the details before running the seeding process
     *
     * @return void
     */
    protected function validate() {

        if ( ! $this->table ) {

            throw SeedExtenderSchemaException::noTableGiven();
        }

        if ( ! $this->tableExists($this->table) ) {

            throw SeedExtenderSchemaException::tableNotFound($table);
        }

        if ( !empty($this->useables) && !$this->hasColumns($this->table, $this->useables) ) {

            throw SeedExtenderSchemaException::tableColumnsNotDefined(
                $this->table, 
                $this->arbitraryColumns($this->table, $this->useables), 
                "useables"
            );
        }

        if ( !empty($this->ignorables) && !$this->hasColumns($this->table, $this->ignorables) ) {

            throw SeedExtenderSchemaException::tableColumnsNotDefined(
                $this->table, 
                $this->arbitraryColumns($this->table, $this->ignorables), 
                "ignorables"
            );
        }
    }
}