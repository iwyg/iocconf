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

use Thapp\XmlConf\XmlConfigReader;
use Illuminate\Container\Container;

/**
 * Class: IocConfigReader
 *
 * @uses XmlConfigReader
 *
 * @package Thapp\IocConf
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IocConfigReader extends XmlConfigReader
{
    /**
     * Illuminate\Container\Container
     *
     * @var mixed
     */
    protected $container;

    /**
     * setContainer
     *
     * @param Illuminate\Container\Container $container
     * @access public
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * load
     *
     * @param mixed $default
     * @access public
     * @return mixed
     */
    public function load($default = null)
    {
        if (file_exists($this->xmlfile)) {

            $data = $this->parse();
            //$this->cache->write($data);
            return $data;
        }

        return $default;
    }
}
