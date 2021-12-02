<?php

namespace Touhidurabir\SeedExtender\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class SeedExtenderModelException extends Exception {

    /**
     * If model not found on given path
     *
     * @param  string $model
     * @return object<\Exception>
     */
    public static function modelNotFound(string $model) {

        return new static("Given model {$model} not found.");
    }


    /**
     * If given model class not a valid child of base Model class
     *
     * @param  string $model
     * @return object<\Exception>
     */
    public static function invalidModelClass(string $model) {

        $baseModel = Model::class;

        return new static("Given model {$model} is not a valid child of base model {$baseModel} class");
    }
    
}