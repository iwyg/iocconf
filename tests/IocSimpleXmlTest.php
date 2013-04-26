<?php

namespace Thapp\Tests\IocConf;

use Mockery as m;
use Thapp\IocConf\IocSimpleXml;
use Thapp\IocConf\SerializableClosure;

class IocSimpleXmlTest extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    public function testParseShouldReturnArray()
    {
        $xml = $this->load('
            <container>
                <entities>
                    <entity id="acme.foo" class="Foo">
                    </entity>
                </entities>
            </container>
            ');

        $result = $xml->parse();

        $this->assertTrue(isset($result['acme.foo']));
        $this->assertTrue(isset($result['acme.foo']['callback']));
        $this->assertInstanceOf('Thapp\IocConf\SerializableClosure', $result['acme.foo']['callback']);
    }

    /**
     * testCallbackShouldInvokeContainerMake
     *
     * @access public
     * @return mixed
     */
    public function testCallbackShouldInvokeContainerMake()
    {
        $mock = m::mock('Foo');
        $container = $this->getContainerMock(function (&$container) use ($mock) {
            $container->shouldReceive('make')->andReturn($mock);
        });

        $xml = $this->load('
            <container>
                <entities>
                    <entity id="acme.foo" class="Foo">
                    </entity>
                </entities>
            </container>
            ');

        $result   = $xml->parse();
        $resolved = $result['acme.foo']['callback']($container);

        $this->assertSame($mock, $resolved);

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
