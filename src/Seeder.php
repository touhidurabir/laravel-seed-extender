<?php

namespace Touhidurabir\SeedExtender;

use Touhidurabir\SeedExtender\BaseTableSeeder;
use Touhidurabir\SeedExtender\Concerns\SeederValidator;

class Seeder extends BaseTableSeeder {

    use SeederValidator;
    
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

}