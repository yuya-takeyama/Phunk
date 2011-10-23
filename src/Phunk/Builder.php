<?php
namespace Phunk;

class Builder
{
    /**
     * Instances to be called.
     *
     * Sequence of Phack middlewares and a Phack application.
     *
     * @var array
     */
    protected $_instances;

    /**
     * Constructor.
     *
     * @param Closure $initializer
     */
    public function __construct($initializer = NULL)
    {
        $this->_instances = array();
        if (isset($initializer)) {
            $initializer($this);
        }
    }

    /**
     * Specifies a middleware to use.
     *
     * @param
     */
    public function enable($middleware, $params = array(), $initializer = NULL)
    {
        $this->_instances[] = function ($app) use ($middleware, $params, $initializer) {
            return new $middleware($app, $params, $initializer);
        };
    }

    /**
     * Specifies something invokable.
     *
     * @param
     */
    public function run($app)
    {
        $this->_instances[] = $app;
    }

    public function toApp()
    {
        $app = array_pop($this->_instances);
        $middlewares = $this->_instances;
        rsort($middlewares);
        foreach ($middlewares as $middleware) {
            $app = $middleware($app);
        }
        return $app;
    }

    public function call($env)
    {
        $app = $this->toApp();
        return $app($env);
    }

    public function __invoke($env)
    {
        return $this->call($env);
    }
}
