<?php

/**
 * This File is part of the Thapp\IocConf package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Tests\IocConf;

use Mockery as m;
use Thapp\IocConf\IocSimpleXml;

/**
 * Class: IocSimpleXmlTest
 *
 * @uses TestCase
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IocSimpleXmlTest extends TestCase
{
    /**
     * @test
     */
    public function testGetAttributeValue()
    {
        $xml    = $this->load('<test id="acme.foo"></test>');
        $this->assertEquals('acme.foo', $xml->get('id'));
    }

    /**
     * @test
     */
    public function testGetAttributeValueIsInteger()
    {
        $xml    = $this->load('<test id="22"></test>');
        $this->assertTrue(is_int($xml->get('id')));
    }

    /**
     * @test
     */
    public function testGetAttributeValueIsFloat()
    {
        $xml    = $this->load('<test id="2.3"></test>');
        $this->assertTrue(is_float($xml->get('id')));
    }

    /**
     * @test
     */
    public function testGetAttributeValueIsString()
    {
        $xml    = $this->load('<test id="2,3"></test>');
        $this->assertTrue(is_string($xml->get('id')));
    }

    /**
     * @test
     */
    public function testParseShouldReturnArray()
    {
        $xml    = $this->getXml();
        $result = $xml->parse();

        $this->assertTrue(isset($result['acme.foo']));
    }

    /**
     * @test
     */
    public function testParseShouldIncludeResolver()
    {
        $xml    = $this->getXml();
        $result = $xml->parse();

        $this->assertTrue(isset($result['acme.foo']['resolver']));
    }

    /**
     * @test
     */
    public function testParsedArrayResolverShouldBeResolverInstance()
    {
        $xml    = $this->getXml();
        $result = $xml->parse();

        $this->assertInstanceOf('Thapp\IocConf\IocResolver', $result['acme.foo']['resolver']);
    }

    /**
     * @test
     */
    public function testExecuteCallbackSouldCreateCorrectScope()
    {
        $mock = m::mock('Foo');
        $container = $this->getContainerMock(function (&$container) use ($mock) {
            $container->shouldReceive('make')->andReturn($mock);
        });

        $xml      = $this->getXml();
        $result   = $xml->parse();
        $resolved = $result['acme.foo']['resolver']->executeCallback($container);

        $this->assertSame($mock, $resolved);

    }

    /**
     * getXml
     *
     * @access protected
     * @return \Thapp\IocConf\IocSimpleXml
     */
    protected function getXml()
    {
        return $this->load('
            <container>
                <entities>
                    <entity id="acme.foo" class="Foo">
                    </entity>
                </entities>
            </container>
        ');
    }
}
