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
     * setters
     *
     * @var array
     */
    protected $setters;

    /**
     * arguments
     *
     * @var array
     */
    protected $arguments;

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
     * get the arguments array from an entity node
     *
     * @param \SimpleXMLElement $entity the entity node
     * @access protected
     * @return array
     */
    protected function getEntityArguments(\SimpleXMLElement $entity)
    {
        $arguments  = array();

        foreach ($entity->argument as $argument) {

            $attributes = $argument->attributes();

            $id    = (string)$attributes->id;
            $class = (string)$attributes->class;

            $arguments[] = array($id, $class);
        }

        return $arguments;
    }

    /**
     * get the setters array from an entity node
     *
     * @param \SimpleXMLElement $entity the entity node
     * @access protected
     * @return array
     */
    protected function getEntitySetters(\SimpleXMLElement $entity)
    {
        $setters = array();

        foreach ($entity->call as $call) {
            $attributes = $call->attributes();
            $arguments  = $this->getEntityArguments($call);
            $setters[(string)$attributes->method] = $arguments;
        }
        return $setters;
    }

    /**
     * resolve the callback data array
     *
     * @access public
     * @return array
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
     * execute the classfactory callback
     *
     * @param \Illuminate\Container\Container $app the IoC container
     * @access public
     * @return mixed normaly returns an object instance
     */
    public function executeCallback(Container $app)
    {
        $args = array();

        if (count($this->arguments) > 0) {
            foreach ($this->arguments as $argument) {
                $args[] = $this->resolveArgument($app, $argument[0], $argument[1]);
            }
        }

        // if no id is given, we cannot resolve the instance directly
        // from the container. Instead, we create a new Instance.
        if (0 === strlen($this->id)) {
            $instance = new \ReflectionClass($this->class);
            $instance = count($args) ? $instance->newInstanceArgs($args) : $instance->newInstance();
        } else {
            $instance = $app->make($this->class, count($args) ? $args : null);
        }

        if (count($this->setters) > 0) {
            foreach ($this->setters as $method => $arguments) {
                $args[] = $this->resolveSetter($app, $instance, $method, $arguments);
            }
        }
        return $instance;
    }

    /**
     * resolve required arguments
     *
     * @param \Illuminate\Container\Container $app the IoC container
     * @param string $id bound id
     * @param string $class classname
     * @access public
     * @return mixed
     */
    public function resolveArgument(Container $app, $id, $class)
    {
        return $app->make((0 === strlen($id) || $id === $class) ? $class : $id);
    }

    /**
     * resolve required setters
     *
     * @param \Illuminate\Container\Container $app the IoC container
     * @param object $instance the class instance
     * @param array $arguments setter arguments
     * @param string $method setter mathod name
     * @access public
     * @return void
     */
    public function resolveSetter(Container $app, $instance, $method, $arguments)
    {
        list($id, $class) = current($arguments);

        $result = $this->resolveArgument($app, $id, $class);
        return call_user_func_array(array($instance, $method), array($result));
    }
}
