<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\StaticPHPDriver;
//use Doctrine\ORM\Mapping\AnsiQuoteStrategy;
use App\Config;

require_once "vendor/autoload.php";

$paths = array("App/Models");
$driver = new StaticPHPDriver("App/Models/Entities");
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => Config::DB_USER ,
    'password' => Config::DB_PASSWORD,
    'dbname'   => Config::DB_NAME ,
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);
$entityManager->getConfiguration()->setMetadataDriverImpl($driver);
//$entityManager->getConfiguration()->setQuoteStrategy(new AnsiQuoteStrategy());



