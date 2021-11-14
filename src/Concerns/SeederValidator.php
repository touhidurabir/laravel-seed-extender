<?php

namespace Touhidurabir\SeedExtender\Concerns;

use Touhidurabir\SeedExtender\Exceptions\SeedExtenderSchemaException;

trait SeederValidator {

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