<?php

namespace Thapp\Tests\IocConf;

use Mockery as m;
use Thapp\IocConf\IocSimpleXml;

class IocSimpleXmlTest extends TestCase
{

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
        $this->assertTrue(isset($result['acme.foo']['resolver']));
        $this->assertInstanceOf('Thapp\IocConf\IocResolver', $result['acme.foo']['resolver']);
    }

    /**
     * testCallbackShouldInvokeContainerMake
     *
     * @access public
     * @return mixed
     */
    public function testExecuteCallbackSouldCreateCorrectScope()
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
        $resolved = $result['acme.foo']['resolver']->executeCallback($container);

        $this->assertSame($mock, $resolved);

    }

}
