<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'darmotics';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'francis';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'Mypass321$';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Do redirection onto public Folder
     * @var string
     */
    const RACINE = '/DarmoticsApp/public';
}
