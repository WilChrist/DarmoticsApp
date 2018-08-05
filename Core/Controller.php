<?php

namespace Core;

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
        return isset($this->post[$key])? $this->post[$key] : null;
    }
}
