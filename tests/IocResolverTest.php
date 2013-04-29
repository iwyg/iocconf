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
use Thapp\IocConf\IocResolver;

/**
 * Class: IocResolverTest
 *
 * @uses \PHPUnit_Framework_TestCase
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IocResolverTest extends TestCase
{
    /**
     * @var ClassName
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testResolveArgument()
    {
        $bar = m::mock('Bar');
        $container = $this->getContainerMock(function (&$container) use ($bar) {
            $container->shouldReceive('make')->andReturn($bar);
        });

        $xml = $this->load('
            <entity id="acme.foo" class="Foo" scope="prototype">
                <argument class="Bar"/>
            </entity>
            ');

        $resolver = $this->setUpResolver($xml);

        $this->assertSame($bar, $resolver->resolveArgument($container, '', 'Bar'));
    }

    /**
     * @test
     */
    public function testResolveSetter()
    {
        $foo = m::mock('Foo');
        $bar = m::mock('Bar');
        $bar->shouldReceive('setFoo')->with($foo);
        $bar->shouldReceive('getFoo')->andReturn($foo);

        $container = $this->getContainerMock(function (&$container) use ($bar, $foo) {
            $container->shouldReceive('make')->with('Foo')->andReturn($foo);
        });

        $xml = $this->load('
            <entity id="acme.foo" class="Foo" scope="prototype">
                <call method="setFoo">
                    <argument class="Foo"/>
                </call>
            </entity>
        ');

        $resolver = $this->setUpResolver($xml);

        $resolver->resolveSetter($container, $bar, 'setFoo', array(array('', 'Foo')));
        $this->assertSame($foo, $bar->getFoo());
    }

    /**
     * @test
     */
    public function testExecuteCallbackWithSetterInjection()
    {
        $foo = m::mock('Foo');
        $baz = m::mock('Baz');
        $bar = m::mock('Bar');
        $foo->shouldReceive('setBaz')->with($baz);
        $foo->shouldReceive('getBaz')->andReturn($baz);

        $container = $this->getContainerMock(function (&$container) use ($foo, $bar, $baz) {
            $container->shouldReceive('make')->with('Baz')->andReturn($baz);
            $container->shouldReceive('make')->with('Foo', null)->andReturn($foo);
        });

        $xml = $this->load('
            <entity id="acme.foo" class="Foo" scope="prototype">
                <call method="setBaz">
                    <argument class="Baz"/>
                </call>
            </entity>
            ');

        $resolver = $this->setUpResolver($xml);

        $this->assertEquals($foo, $resolver->executeCallback($container));
        $this->assertSame($baz, $foo->getBaz());
    }

    /**
     * @test
     */
    public function testSetPropertyClass()
    {
        list ($resolver, $reflection) = $this->preparePropertyTest();

        $class = $reflection->getProperty('class');
        $class->setAccessible(true);
        $this->assertEquals('Foo', $class->getValue($resolver));
    }

    /**
     * @test
     */
    public function testSetPropertyId()
    {

        list ($resolver, $reflection) = $this->preparePropertyTest();

        $id = $reflection->getProperty('id');
        $id->setAccessible(true);
        $this->assertEquals('acme.foo', $id->getValue($resolver));

    }

    /**
     * @test
     */
    public function testSetPropertyScope()
    {
        list ($resolver, $reflection) = $this->preparePropertyTest();

        $scope = $reflection->getProperty('scope');
        $scope->setAccessible(true);
        $this->assertEquals('prototype', $scope->getValue($resolver));
    }

    /**
     * @test
     */
    public function testSetPropertyArguments()
    {
        list ($resolver, $reflection) = $this->preparePropertyTest();

        $arguments = $reflection->getProperty('arguments');
        $arguments->setAccessible(true);
        $this->assertEquals(array(array('', 'Bar')), $arguments->getValue($resolver));
    }

    /**
     * @test
     */
    public function testSetPropertySetters()
    {
        list ($resolver, $reflection) = $this->preparePropertyTest();

        $setters = $reflection->getProperty('setters');
        $setters->setAccessible(true);
        $this->assertEquals(array('setFoo' =>  array(array('', 'Foo'))), $setters->getValue($resolver));
    }

    protected function preparePropertyTest()
    {
        $xml = $this->load('
            <entity id="acme.foo" class="Foo" scope="prototype">
                <argument class="Bar"/>
                <call method="setFoo">
                    <argument class="Foo"/>
                </call>
            </entity>
            ');

        $resolver = $this->setUpResolver($xml);

        $reflection = new \ReflectionObject($resolver);
        return array($resolver, $reflection);
    }

    /**
     * setUpResolver
     *
     * @param mixed $xml
     * @param array $attributes
     * @access protected
     * @return Thapp\IocConf\IocResolver
     */
    protected function setUpResolver($xml, array $attributes = null)
    {
        return new IocResolver($xml, !is_null($attributes) ? $attributes : array(
            'id'    => 'acme.foo',
            'class' => 'Foo',
            'scope' => 'prototype',
        ));
    }
}
