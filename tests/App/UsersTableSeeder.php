<?php

namespace Touhidurabir\SeedExtender\Tests\App;

use Touhidurabir\SeedExtender\BaseTableSeeder;

class UsersTableSeeder extends BaseTableSeeder {
    
    /**
     * Seeder table name 
     *
     * @var string
     */
    protected $table = "users";


    /**
     * The list of table attributes/columns
     *
     * @var array
     */
    protected $columns = ["id", "email", "password", "created_at", "updated_at", "deleted_at"];


    /**
     * The table attributes/columns that will be ignored during the seeding process
     *
     * @var array
     */
    protected $ignorables = ["id", "deleted_at"];


    /**
     * The table attributes/columns that will be used during the seeding process
     *
     * @var array
     */
    protected $useables = ["email", "password"];


    /**
     * Should merge and include timestamps[created_at, updated_at] by default into the seed data
     *
     * @var boolean
     */    
    protected $includeTimestampsOnSeeding = true;


    /**
     * The seeding data
     *
     * @var array
     */
    protected $data = [
    	['testuser1@test.com', '123456'],
        ['testuser2@test.com', '123456'],
    ];


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
}
