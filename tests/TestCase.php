<?php

namespace Thapp\Tests\IocConf;

use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{


    protected function tearDown()
    {
        m::close();
    }

    protected function load($xml)
    {
        $dom = new \DOMDOcument;
        $dom->loadXML($xml);

        return simplexml_import_dom($dom, 'Thapp\IocConf\IocSimpleXml');
    }

    protected function getContainerMock(\Closure $callback = null)
    {
        $container = m::mock('Application', '\Illuminate\Container\Container');

        if (!is_null($callback)) {
            $callback($container);
        }

        return $container;
    }
}
