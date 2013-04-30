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
class IocSimpleXml extends SimpleXMLElement implements IocSimpleXmlInterface
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
     * {@inheritDoc}
     */
    public function get($attribute)
    {
        if (!is_null($attributes = $this->attributes())) {
            return $attributes->get($attribute);
        }

        return $this->getAttributeValue($attribute);
    }

    /**
     * getAttributeValue
     *
     * @param string $attribute
     * @access protected
     * @return string|float|int
     */
    protected function getAttributeValue($attribute)
    {
        $value = (string)$this->{$attribute};

        if (is_numeric($value)) {
            return false !== strpos($value, '.') ? floatval($value) : intval($value);
        }

        return $value;
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
        $scope     = (string)$attributes->scope;

        $attr      = compact(array('id', 'class', 'scope'), $id, $class, $scope);

        $resolver = new IocResolver($entity, $attr);
        $result[0 === strlen($id) ? $class : $id] = $resolver->resolve();
    }
}
