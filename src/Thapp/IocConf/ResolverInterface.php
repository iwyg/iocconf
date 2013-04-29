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
     * make
     *
     * @access public
     * @return \Closure
     */
    public function make();
}
