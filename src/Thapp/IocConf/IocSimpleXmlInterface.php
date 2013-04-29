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

use Thapp\XmlConf\SimpleXmlConfigInterface;

/**
 * Class: IocSimpleXmlInterface
 *
 * @uses SimpleXmlConfigInterface
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface IocSimpleXmlInterface extends SimpleXmlConfigInterface
{
    /**
     * get an attribute value
     *
     * @param mixed $attribute
     * @access public
     * @return string|int|double
     */
    public function get($attribute);
}
