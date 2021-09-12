# Laravel Seed Extender

A highly opinioned way to work with the laravel seeder. 

## Installation

Require the package using composer:

```bash
composer require touhidurabir/laravel-seed-extender
```

## WHY ?

As mentioned, this is a highly opinioned way to work with seeder. We have popular **Faker** library and **Model Factories** to seed model table and that seems like the obious choice . But sometimes in production or even in the development purpose we need real life data to seed model table and for that purpose need seeder classes . 

Now this package does not introduce any new mechanism of the seeding using the seeder classes but just add some ability to manupulate the each seeder class as per need . Basically it add some extar feature/ability to the seeder class. 


## Usage

To generate a new seeder class of this package, run the following command

```bash
php artisan make:extend-seeder SeederClassName --table=table_name
```

That will generate an seeder new seeder class at the **/database/seeders** location . for example , a basic user seeder class will look like this  

```php
<?php

namespace Database\Seeders;

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
    protected $ignorables = [];


    /**
     * The table attributes/columns that will be used during the seeding process
     *
     * @var array
     */
    protected $useables = [];


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
```

As the above example showes, there are few properties that can be modified manually or vai the command. 

## Class properties and methods explanation

### $table (PROPERTY)

It define for which talbel it will run the seeder . to specify which table vai the command

```bash
php artisan make:extend-seeder UsersTableSeeder --table=users
```

### $columns (PROPERTY)

It defined all the available columns in the table . nothing to do here as it will be auto generated by checking the table schema by this package itself .

### $ignorables (PROPERTY)

It defined which columns will be ignored at the seed time . it specify which columns we want to ignore at the class generation time through the command, provide comma seperated columns name

```bash
php artisan make:extend-seeder UsersTableSeeder --table=users --ignorables=id,deleted_at
```

### $useables (PROPERTY)

It defined which columns will be used at the seed time . it specify which columns we want to use at the class generation time through the command, provide comma seperated columns name

```bash
php artisan make:extend-seeder UsersTableSeeder --table=users --useables=name,email,password
```
 > NOTE that if the **$useables** property and defined and not empty, it will take account of it and ignore the set values of **$ignorables** property. 

### $includeTimestampsOnSeeding (PROPERTY)

This defined if the **created_at** and **updated_at** values will be auto included at the seed time. by default this is set to true . but if the model is not using the **timestamp** values or do not want to include it in the seding process, then specify it through the command at the time of generation 

```bash
php artisan make:extend-seeder UsersTableSeeder --table=users --useables=name,email,password --no-timestamp
```

### $data (PROPERTY)

This defined what data to seed . for example

```php
protected $data = [
    ['testuser1@test.com', '123456'],
    ['testuser2@test.com', '123456'],
];
```

### seedableDataBuilder (METHOD)

If we have some data that needed to be presetent to every seeding rows, then it's better to not to have the as repetitive data in the **$data** properties and define those in this method . 

```php
protected function seedableDataBuilder() {

    foreach ($this->data as $key => $value) {
        
        $this->data[$key] = array_merge($value, [
            // any repetitive merge data 
            // it will merge to every row data defined in the $data proeprties
        ]);
    }

    return $this->data;
}
```

## More Command Options

### replace

By default it will throw exception and print a error message in the console when a seeder class of same name already exists but if needed to replace it pass the flag **--replace**

```bash
php artisan make:extend-seeder UsersTableSeeder --table=users --replace
```

### strict

By default this package will not try to validate the give informaitons like table name it useables or ignorables columns but if required to , it can validate those via passing the flag **--strict** .

```bash
php artisan make:extend-seeder UsersTableSeeder --table=users --useables=email,password --strict
```

## Run seeding independent of seeder class

This package provides way to run a seeding process independent of seeder class . That is one can run a seeding process from within the app at the runtime . 

```php
use Touhidurabir\SeedExtender\Facades\SeedExtender;

SeedExtender::table('table_name') //table name
    ->useables([]) // useables columns as array
    ->ignorables([]) // ignorables columns as array
    ->includeTimestampsOnSeeding(true) // auto include of timestamp value as boolean
    ->seedData( // set seed data
        [ [], [], ], // the seed data itself
        [] // any auto mergeable repetitive data
    )
    ->run(); // run the seeding process
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)
