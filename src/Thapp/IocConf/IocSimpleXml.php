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

use \SimpleXMLElement;
use Illuminate\Container\Container;
use Thapp\XmlConf\SimpleXmlConfigInterface;

/**
 * Class: IocSimpleXml
 *
 * @implements SimpleXmlConfigInterface
 * @uses SimpleXMLElement
 *
 * @package Thapp\IocConf
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IocSimpleXml extends SimpleXMLElement implements SimpleXmlConfigInterface
{
    /**
     * parse
     *
     * @access public
     * @return array
     */
    public function parse()
    {
        $result = array();

        foreach ($this->getEntites() as $entity) {
            $this->parseEntity($entity, $result);
        }

        return $result;
    }

    /**
     * getEntites
     *
     * @param \SimpleXMLElement $container
     * @access protected
     * @return array|\SimpleXMLElement
     */
    protected function getEntites()
    {
        return is_object($this->entities->entity) ? $this->entities->entity : array();
    }

    /**
     * parseEntity
     *
     * @param \SimpleXMLElement $entity
     * @access protected
     * @return void
     */
    protected function parseEntity(\SimpleXMLElement $entity, array &$result = array())
    {
        $attributes = $entity->attributes();

        $id        = (string)$attributes->id;
        $class     = (string)$attributes->class;

        $result[0 === strlen($id) ? $class : $id] = $this->createResolver($entity, $attributes);
    }

    /**
     * createResolver
     *
     * @param \SimpleXMLElement $entity
     * @param \SimpleXMLElement $attributes
     * @access protected
     * @return array
     */
    protected function createResolver(\SimpleXMLElement $entity, $attributes)
    {
        $me        = $this;
        $id        = (string)$attributes->id;
        $class     = (string)$attributes->class;
        $setters   = $this->getEntitySetters($entity);
        $arguments = $this->getEntityArguments($entity);

        $result = array(
            'id'       => $id,
            'class'    => $class,
            'scope'    => (string)$attributes->scope,
            'callback' => new SerializableClosure(function (Container $app) use ($id, $class, $arguments, $setters)
            {
                $args = array();

                if (count($arguments) > 0) {
                    foreach ($arguments as $argument) {
                        $args[] = $argument($app);
                    }
                }

                // if no id is given, we cannot resolve the instance directly
                // from the container. Instead, we create a new Instance.
                if (0 === strlen($id)) {
                    $instance = new \ReflectionClass($class);
                    $instance = count($args) ? $instance->newInstanceArgs($args) : $instance->newInstance();
                } else {
                    $instance = $app->make($class, $args);
                }

                if (count($setters) > 0) {
                    foreach ($setters as $setter) {
                        $setter($app, $instance);
                    }
                }

                return $instance;
            })
        );

        return $result;
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


            $arguments[] = new SerializableClosure(

                function (Container $app) use ($id, $class)
                {
                    return $app->make((0 === strlen($id) || $id === $class) ? $class : $id);
                }

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

            $id     = (string)$attributes->id;
            $class  = (string)$attributes->class;
            $method = (string)$attributes->method;

            $setters[(string)$attributes->method] = new SerializableClosure(

                function (Container $app, $instance) use ($id, $arguments, $method)
                {
                    $fn = current($arguments);
                    return call_user_func_array(array($instance, $method), array($fn($app)));
                }

            );

        }
        return $setters;
    }
}
