<?php

/**
 * This File is part of the Thapp\IocConf package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Tests\IocConf;

use Mockery as m;

/**
 * Class: TestCase
 *
 * @uses \PHPUnit_Framework_TestCase
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * close Mockery
     *
     * @return void
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * load
     *
     * @param mixed $xml
     * @access protected
     * @return \Thapp\IocConf\IocSimpleXml
     */
    protected function load($xml)
    {
        $dom = new \DOMDOcument;
        $dom->loadXML($xml);

        return simplexml_import_dom($dom, 'Thapp\IocConf\IocSimpleXml');
    }

    /**
     * getContainerMock
     *
     * @param \Closure $callback
     * @access protected
     * @return \Illuminate\Container\Container
     */
    protected function getContainerMock(\Closure $callback = null)
    {
        $container = m::mock('Application', '\Illuminate\Container\Container');

        if (!is_null($callback)) {
            $callback($container);
        }

        return $container;
    }

    /**
     * invokeProtectedMethod
     *
     * @param mixed $object
     * @param mixed $method
     * @param mixed $arguments
     * @access protected
     * @return mixed
     */
    protected function invokeProtectedMethod($object, $method, $arguments)
    {
        $reflection = new \ReflectionObject($object);
        $method = $reflection->getMethod($method);

        $method->setAccessible(true);
        array_unshift($arguments, $object);

        return call_user_func_array(array($method, 'invoke'), $arguments);
    }
}
