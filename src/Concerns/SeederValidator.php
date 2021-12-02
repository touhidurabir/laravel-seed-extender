<?php

namespace Touhidurabir\SeedExtender\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Touhidurabir\SeedExtender\Exceptions\SeedExtenderModelException;
use Touhidurabir\SeedExtender\Exceptions\SeedExtenderSchemaException;

trait SeederValidator {

    /**
     * Validate the details before running the seeding process
     *
     * @return void
     */
    protected function validate() {

        $this->validateTableHasProvided($this->table);
        $this->validateGivenTableExists($this->table);
        $this->validateProvidedColumnsDefinedInTable($this->table, Arr::wrap($this->useables), 'useables');
        $this->validateProvidedColumnsDefinedInTable($this->table, Arr::wrap($this->ignorables), 'ignorables');
        $this->validateGivenModel($this->model);
    }


    /**
     * Validate if table information has provided or not
     *
     * @param  string $table
     * @return void
     * 
     * @throws \Exception
     */
    protected function validateTableHasProvided(string $table = null) {

        if ( ! $table ) {

            throw SeedExtenderSchemaException::noTableGiven();
        }
    }


    /**
     * Validate if the given table exists in the database
     *
     * @param  string $table
     * @return void
     * 
     * @throws \Exception
     */
    protected function validateGivenTableExists(string $table) {

        if ( ! $this->tableExists($table) ) {

            throw SeedExtenderSchemaException::tableNotFound($table);
        }
    }


    /**
     * Validate if the given column/s is present in the given table schema
     *
     * @param  string   $table
     * @param  array    $columns
     * @param  string   $usePurpose
     * 
     * @return void
     * 
     * @throws \Exception
     */
    protected function validateProvidedColumnsDefinedInTable(string $table, array $columns, string $usePurpose = null) {

        if ( !empty($columns) && !$this->hasColumns($table, $columns) ) {

            throw SeedExtenderSchemaException::tableColumnsNotDefined(
                $table, 
                $this->arbitraryColumns($table, $columns), 
                $usePurpose
            );
        }
    }


    /**
     * Validate if the given model class to use for seeder
     *
     * @param  string   $model
     * @return void
     * 
     * @throws \Exception
     */
    protected function validateGivenModel(string $model = null) {

        if ( is_null($model) ) {

            return;
        }

        if ( ! class_exists($model) ) {

            throw SeedExtenderModelException::modelNotFound($model);
        }

        if ( ! (new $model) instanceof Model ) {
            
            throw SeedExtenderModelException::invalidModelClass($model);
        }
    }
    
}