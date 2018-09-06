<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    /*
     * get Model Class Name for @model
     * @params string $model abbreviation of the model name wanted
     * @return string model Class Name
     */
    public static function models($modelAbbreviation){
        $models=[
            'dep'=>'App\Models\Department',
            'emp'=>'App\Models\Employee',
            'pro'=>'App\Models\Project',
        ];
        return $models[$modelAbbreviation];
    }
}
