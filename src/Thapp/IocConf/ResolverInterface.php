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
     * resolve
     *
     * @access public
     * @return mixed
     */
    public function resolve();

    /**
     * executeCallback
     *
     * @param mixed $
     * @access public
     * @return mixed
     */
    public function executeCallback(Container $app);

    /**
     * resolveArgument
     *
     * @param \Illuminate\Container\Container $app
     * @param mixed $id
     * @param mixed $class
     * @access public
     * @return mixed
     */
    public function resolveArgument(Container $app, $id, $class);

    /**
     * resolveSetter
     *
     * @param Container $app
     * @param mixed $instance
     * @param mixed $arguments
     * @param mixed $method
     * @access public
     * @return mixed
     */
    public function resolveSetter(Container $app, $instance, $arguments, $method);


}
