<?php

namespace Touhidurabir\SeedExtender\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait SchemaHelper {

    /**
     * Table exist in the Database
     *
     * @param  string $table
     * @return bool
     */
    public function tableExists(string $table) {

        return Schema::hasTable($table);
    }


    /**
     * Get the list of extra/arbitrary cloumns from the custom table columns list
     *
     * @param  string $table
     * @param  array  $columns
     * 
     * @return array
     */
    public function arbitraryColumns(string $table, array $columns) {
        
        return array_values(array_diff($columns, $this->columnList($table)));
    }


    /**
     * Check if custom list of columns are actuall match with table columns
     *
     * @param  string $table
     * @param  array  $columns
     * @param  bool   $strict
     * 
     * @return bool
     */
    public function hasColumns(string $table, array $columns, bool $strict = true) {

        if ( empty($columns) ) {

            return false;
        }

        return empty($this->arbitraryColumns($table, $columns));
    }


    /**
     * Column exist in a table
     *
     * @param  string $column
     * @param  string $table
     *
     * @return bool
     */
	public function hasColumn(string $table, string $column) {

		return Schema::hasColumn($table, $column);
	}


	/**
     * Return table column list
     *
     * @param  string $table
     * @return array
     */
	public function columnList(string $table) {

		// this cause to return table columns in alphabetic order
        // return Schema::getColumnListing($table);

        // this cause to return table columns in numeric order
        return DB::getSchemaBuilder()->getColumnListing($table);
	}
}