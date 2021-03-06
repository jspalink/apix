<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix;

use Apix\Entity,
    Apix\Entity\EntityInterface;

/**
 * Represents a collection of resources.
 */
class Resources
{

    /**
     * @var array
     */
    protected $resources = array();

    /**
     * @var EntityInterface
     */
    protected $entity = null;

    /**
     * Sets an entity object.
     *
     * @param EntityInterface $entity An entity object
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Gets the current entity object.
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Adds a resource entity.
     *
     * @param  string $name     A resource name
     * @param  array  $resource A resource definition array
     * @return Entity
     */
    public function add($name, array $resources)
    {
        switch(true):

            case isset($resources['action'])
                && $resources['action'] instanceof \Closure:
                $this->setEntity(
                    new Entity\EntityClosure()
                );
            break;

            case isset($resources['controller']):
            default:
                $this->setEntity(
                    new Entity\EntityClass()
                );

        endswitch;

        if (!isset($this->resources[$name])) {
            $entity = get_class($this->getEntity());
            $this->resources[$name] = new $entity(); //new Entity($group);
        }
        $this->resources[$name]->append($resources);

        return $this->resources[$name];
    }

    /**
     * Checks wether a specified resource name exists.
     *
     * @param  string  $name The resource name to check
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->resources[$name]);
    }

    /**
     * Returns all the resources.
     *
     * @return array The array of resources
     */
    public function toArray()
    {
        return $this->resources;
    }

    /**
     * Gets the specified resource entity.
     *
     * @param  string                 $name The resource name to retrieve.
     * @throws /DomainException       404
     * @return Entity/EntityInterface
     */
    public function getResource($name)
    {
        #echo '<pre>';print_r($this->resources);

        if (isset($this->resources[$name])) {
            return $this->resources[$name];
        }

        throw new \DomainException(
            sprintf('Invalid resource entity specified (%s).', $name), 404
        );
    }

    /**
     * Gets the specified ressource entity from a route object.
     *
     * @param  Router                 $route  The resource route object.
     * @param  boolean                $follow Wether to handle the default actions.
     * @throws /DomainException       404
     * @return Entity/EntityInterface
     */
    public function get(Router &$route, $follow=true)
    {
        $entity = $this->getResource(
            $route->getName()
        );

        // swap if aliased/redirected
        if ($redirect = $entity->getRedirect()) {
            $entity = $this->getResource($redirect);
        }

        // handles the default actions but do not override a local action definition.
        if ($follow) {

            $method = $route->getMethod();

            if ( $method == 'HEAD' && $entity->hasMethod('GET') ) {
                $route->setMethod('GET');
            }

            if (
                ( $redirect = $entity->getDefaultAction($method) )
                && !$entity->hasMethod($method)
            ) {
                $entity = $this->getResource($redirect);
                #$route->setParams(array('entity' => clone $entity));
            }
        }

        // set this entity route.
        $entity->setRoute($route);

        return $entity;
    }

}
