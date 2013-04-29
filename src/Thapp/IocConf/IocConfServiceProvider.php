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

use Illuminate\Support\ServiceProvider;

/**
 * Class: IocConfServiceProvider
 *
 * @uses ServiceProvider
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IocConfServiceProvider extends ServiceProvider
{
    /**
     * register
     *
     * @access public
     * @return void
     */
    public function register()
    {
        $this->package('thapp/iocconf');
    }

    /**
     * boot
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        $ioc = $this->app['xmlconf.ioc'];

        foreach ($ioc->load(array()) as $id => $setup) {

            $resolver = $setup['resolver'];
            $callback = $resolver->make();

            switch ($setup['scope']) {
                case 'singleton':
                    $this->app->singleton($id, $callback);
                break;
                case 'prototype':
                    $this->app->bind($id, $callback);
                break;
                case 'shared':
                    $this->app[$id] = $this->app->share($callback);
                break;
            }
        }
    }
}
