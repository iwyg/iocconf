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
 * Class: ResolverInterface
 *
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ResolverInterface
{
    /**
     * resolve the callback data array
     *
     * @access public
     * @return array
     */
    public function resolve();

    /**
     * execute the classfactory callback
     *
     * @param \Illuminate\Container\Container $app the IoC container
     * @access public
     * @return mixed normaly returns an object instance
     */
    public function executeCallback(Container $app);

    /**
     * resolve required arguments
     *
     * @param \Illuminate\Container\Container $app the IoC container
     * @param string $id bound id
     * @param string $class classname
     * @access public
     * @return mixed
     */
    public function resolveArgument(Container $app, $id, $class);

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
    public function resolveSetter(Container $app, $instance, $arguments, $method);


}
