<?php

namespace Touhidurabir\SeedExtender\Exceptions;

use Exception;

class SeedExtenderSchemaException extends Exception {

    /**
     * If no table information provided
     *
     * @return object<\Exception>
     */
    public static function noTableGiven() {

        return new static("Not table name provided");
    }


    /**
     * If given table not found in the DB schema
     *
     * @param  string $table
     * @return object<\Exception>
     */
    public static function tableNotFound(string $table) {

        return new static("The given {$table} not found");
    }


    /**
     * If some set columns not exists in the given custom columns list
     *
     * @param  string   $table
     * @param  array    $columns
     * @param  string   $purposeOption
     * 
     * @return object<\Exception>
     */
    public static function tableColumnsNotDefined(string $table, array $columns, string $purposeOption = 'use') {

        $missingColumns = implode(', ', $columns);

        return new static("The given columns {$missingColumns} not defined in the {$table} schema that is give for {$purposeOption} purpose");
    }
    
}