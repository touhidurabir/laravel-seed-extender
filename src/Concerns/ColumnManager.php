<?php

namespace Touhidurabir\SeedExtender\Concerns;

use Illuminate\Support\Facades\Schema;
use Touhidurabir\SeedExtender\Concerns\SchemaHelper;

trait ColumnManager {

    use SchemaHelper;

	/**
     * The list of table attributes/columns
     *
     * @var array
     */
    protected $columns = [];


    /**
     * The table attributes/columns that will be ignored during the seeding process
     *
     * @var array
     */
    protected $ignorables = [];


    /**
     * The table attributes/columns that will be used during the seeding process
     *
     * @var array
     */
    protected $useables = [];


    /**
     * The timestamp columns list
     *
     * @var array
     */
    protected $timestampColumns = ['created_at', 'updated_at'];

	
	/**
     * Should merge and include timestamps[created_at, updated_at] by default into the seed data
     *
     * @var boolean
     */    
    protected $includeTimestampsOnSeeding = true;


	/**
     * Return table column names as array.
     *
     * @return array
     */
    protected function seedableColumns() {

        if ( $this->useables && !empty($this->useables) ) {

            return array_merge(
                $this->useables, 
                $this->includeTimestampsOnSeeding ? $this->timestampColumns : []
            );
        }

    	$this->ignorables = array_unique(
            array_merge(
                $this->ignorables, $this->generateIgnoreableColumnList()
            )
        );
        
    	return array_diff(
            empty($this->columns) ? $this->columnList($this->table) : $this->columns,
            $this->ignorables
        );
    }


    /**
     * Genrate the columns list that will be ignored in seeding process
     *
     * @param  array $columns
     * @return array
     */
    protected function generateIgnoreableColumnList(array $columns = []) {

        if ( ! empty($columns) )  {

            return $columns;
        }

        if ( $this->hasColumn($this->table, 'deleted_at') ) {

            return ['deleted_at'];
        }

    	return [];
    }


    /**
     * Genrate the timestamp related merge data list
     *
     * @param  array $list
     * @return array
     */
    protected function generateTimestampMergeList(array $list = []) {

        if ( ! empty($list) ) {

            return $list;
        }

        if ( ! $this->includeTimestampsOnSeeding ) {

            return [];
        }

        return [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')];   
    }

}