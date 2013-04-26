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

use Illuminate\Support\SerializableClosure as SClosure;

/**
 * Class: SerializableClosure
 *
 * @uses Illuminate\Support\SerializableClosure
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class SerializableClosure extends SClosure
{
    /**
     * {@inheritDoc}
     */
    protected function getCodeFromFile()
    {
        $file = $this->getFile();

        $code = '';

        // Next, we will just loop through the lines of the file until we get to the end
        // of the Closure. Then, we will return the complete contents of this Closure
        // so it can be serialized with these variables and stored for later usage.
        while ($file->key() < $this->reflection->getEndLine())
        {
            $code .= $file->current(); $file->next();
        }

        preg_match('/function\s?\(/', $code, $matches, PREG_OFFSET_CAPTURE);
        $begin = $matches[0][1];

        $code = substr($code, $begin, strrpos($code, '}') - $begin + 1);

        return $code;
    }
}
