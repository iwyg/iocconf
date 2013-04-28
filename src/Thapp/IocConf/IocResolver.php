<?php

/**
 * This File is part of the Thapp\IocConf package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\IocConf;

use Illuminate\Container\Container;

/**
 * Class: IocResolver
 *
 * @implements ResolverInterface
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IocResolver implements ResolverInterface
{

    /**
     * id
     *
     * @var string
     */
    protected $id;

    /**
     * class
     *
     * @var string
     */
    protected $class;

    /**
     * scope
     *
     * @var string
     */
    protected $scope;

    /**
     * __construct
     *
     * @param mixed $
     * @access public
     */
    public function __construct(\SimpleXMLElement $entity, $attributes)
    {
        extract($attributes);

        $this->id    = $id;
        $this->class = $class;
        $this->scope = $scope;

        $this->setters   = $this->getEntitySetters($entity);
        $this->arguments = $this->getEntityArguments($entity);
    }

    /**
     * getEntityArguments
     *
     * @param mixed $entity
     * @access protected
     * @return array
     */
    protected function getEntityArguments($entity)
    {
        $arguments  = array();

        foreach ($entity->argument as $argument) {

            $attributes = $argument->attributes();

            $id    = (string)$attributes->id;
            $class = (string)$attributes->class;

            $arguments[] = array(
                array($this, 'resolveArgument'),
                array($id, $class)
            );
        }

        return $arguments;
    }

    /**
     * getEntitySetters
     *
     * @param mixed $entity
     * @access protected
     * @return array
     */
    protected function getEntitySetters($entity)
    {
        $setters = array();

        foreach ($entity->call as $call) {

            $attributes = $call->attributes();
            $arguments  = $this->getEntityArguments($call);

            $method = (string)$attributes->method;

            $setters[(string)$attributes->method] = array(
                array($this, 'resolveSetter'),
                array($arguments, $method)
            );
        }
        return $setters;
    }

    /**
     * resolve
     *
     * @access public
     * @return mixed
     */
    public function resolve()
    {
        return array(
            'id'       => $this->id,
            'class'    => $this->class,
            'scope'    => $this->scope,
            'resolver' => $this
        );
    }

    /**
     * executeCallback
     *
     * @param \Illuminate\Container\Container $app
     * @access public
     * @return mixed normaly returns an object instance
     */
    public function executeCallback(Container $app)
    {
        $args = array();

        if (count($this->arguments) > 0) {
            foreach ($this->arguments as $argument) {

                $callback = array_shift($argument);

                array_unshift($argument[0], $app);

                $args[]   = call_user_func_array($callback, $argument[0]);
            }
        }

        // if no id is given, we cannot resolve the instance directly
        // from the container. Instead, we create a new Instance.
        if (0 === strlen($this->id)) {
            $instance = new \ReflectionClass($this->class);
            $instance = count($args) ? $instance->newInstanceArgs($args) : $instance->newInstance();
        } else {
            $instance = $app->make($this->class, $args);
        }

        if (count($this->setters) > 0) {
            foreach ($this->setters as $setter) {

                $callback = array_shift($setter);

                array_unshift($setter[0], $instance);
                array_unshift($setter[0], $app);

                $args[] = call_user_func_array($callback, $setter[0]);
            }
        }
        return $instance;
    }

    /**
     * resolveArguments
     *
     * @access public
     * @return mixed
     */
    public function resolveArgument(Container $app, $id, $class)
    {
        return $app->make((0 === strlen($id) || $id === $class) ? $class : $id);
    }

    /**
     * resolveSetters
     *
     * @param mixed $app
     * @param mixed $instance
     * @param mixed $arguments
     * @param mixed $method
     * @access public
     * @return mixed
     */
    public function resolveSetter(Container $app, $instance, $arguments, $method)
    {
        $fn    = $arguments[0][0];
        $id    = $arguments[0][1][0];
        $class = $arguments[0][1][1];

        $result = call_user_func_array($fn, array($app, $id, $class));
        return call_user_func_array(array($instance, $method), array($result));
    }
}
