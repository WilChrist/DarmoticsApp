<?php

namespace Core;
use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;
/**
 * Base controller
 *
 * PHP version 7.0
 */



abstract class Controller

{
    //variable pour stocker le $post dans le controlleur
    protected $post=[];


    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];
    protected $db;
    protected $logger;
    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        session_start();
        require_once ("../bootstrap.php");
        $this->route_params = $route_params;
        $this->db = $entityManager;
        $this->logger = new Logger('../logs/'.get_class($this), LogLevel::DEBUG, array (
            'extension' => 'log', // changes the log file extension
            'logFormat' => json_encode([
                'datetime' => '{date}',
                'logLevel' => '{level}',
                'message'  => '{message}',
                'context'  => '{context}',
            ]),
            'appendContext' => false
        ));
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        //récupération et stockage du $_POST
        $this->post=$args[0];
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    protected function getpost($key){
        return isset($this->post[$key])? htmlspecialchars( $this->post[$key] ): null;
    }
    protected function getPostMultiple($key){
        $values= isset($this->post[$key])?  $this->post[$key] : null;
        $pureValues=array();
        if($values!=null){
            foreach ($values as $value){
                array_push($pureValues,$value);
            }
        }
        return $pureValues;
    }

    protected function setMessage($key,$value){
        $_SESSION[$key] = $value;
    }

    protected function getMessage($key){
        return isset($_SESSION[$key])? $_SESSION[$key] : null;
    }
    /*
     * get objects of type @className
     * @params string $className Class Name of the Objects
     * @return object ObjectOf $className
     */
    protected function findAll($className){
        return $this->db->getRepository($className)->findAll();
    }

    /*
     * get objects of type @className by @id
     * @params string $className Class Name of the object
     * @params integer $id Id of the Object
     * @return  array ObjectOf $className
     */
    protected function findById($className, $id){
        return $this->db->getRepository($className)->findOneBy(array('id' => $id));;
    }

}
