<?php

namespace Zenya\Api\Entity;

use Zenya\Api\Entity,
    Zenya\Api\Entity\EntityInterface,
    Zenya\Api\Reflection,
    Zenya\Api\Router;

/**
 * Represents a resource.
 *
 */
class EntityClosure extends Entity implements EntityInterface
{

    protected $actions = array();

    public $group;

    private $reflection;

    /**
     * Sets and returns a reflection of a function.
     *
     * @param string $name The REST name of function.
     * @return \ReflectionFunction|false
     */
    public function reflectedFunc($name)
    {
        if(isset($this->reflection[$name])) {
            return $this->reflection[$name];
        } else if ( isset($this->actions[$name]['action'])
            && $this->actions[$name]['action'] instanceOf \Closure
        ) {
            $this->reflection[$name] = new \ReflectionFunction($this->actions[$name]['action']);
            return $this->reflection[$name];
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function append(array $defs)
    {
        parent::_append($defs);
        if(!isset($defs['method'])) {
            throw new \RuntimeException('Closure is not defining a method?');
        }
        $this->actions[$defs['method']] = $defs;
    }

    /**
     * {@inheritdoc}
     */
     function underlineCall(Router $route)
    {
        $method = $this->getMethod($route);

        #try {
            $action = $this->getAction($route->getMethod());
        #} catch (\Exception $e) {
        #    throw new \RuntimeException("Resource entity not (yet) implemented.", 501);
        #}

        // TODO: merge with TEST & OPTIONS ???

        $params = $this->getRequiredParams($method, $route->getMethod(), $route->getParams());

        #$this->addAllListeners('resource', 'early');

        return call_user_func_array($action, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function _parseDocs()
    {
        // class doc
        $docs = Reflection::parsePhpDoc( $this->group );

        // doc for all methods
        foreach ($this->getActions() as $key => $func) {
          if ($func['action'] InstanceOf \Closure) {
              $doc = $this->reflectedFunc($key)->getDocComment();
              $docs['methods'][$key] = Reflection::parsePhpDoc($doc);
          }
        }

        return $docs;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(Router $route)
    {
        $name = $route->getMethod();
        if (false === $method = $this->reflectedFunc($name)) {
            throw new \InvalidArgumentException("Invalid resource's method ({$name}) specified.", 405);
        }

        return $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        #return $this->actions+$this->overrides;
        return $this->actions;
    }

    private function getAction($method)
    {
      return $this->actions[$method]['action'];
    }

    /* --- CLOSURE only --- */

    /**
     * Group a resource entity.
     *
     * @param  string $name The group name
     * @return void
     */
    public function group($test)
    {
        // TODO retrive phpdoc coment strinfg here!
        #$test = "/* TODO {closure-group-title} */";
        // group test
        $this->group = $test;

        return $this;
    }

    /**
     * Adds a redirect.
     *
     * @param  string $location A  name
     * @param  array  $resource The resource definition array
     * @return void
     */
    public function redirect($location)
    {
        $this->redirect = $location;

        return $this;
    }
}
