<?php

namespace PHPocket\Tests\DI;


use PHPocket\DI\Configurator;

class ConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCommonPropertiesSet()
    {
        $cnf = new Configurator();
        $x = new MockOne();

        $this->assertNull($x->propertyOne);
        $this->assertNull($x->propertyTwo);
        $cnf->apply($x, array('propertyOne' => 10, 'propertyTwo' => 'str'));
        $this->assertSame(10, $x->propertyOne);
        $this->assertSame('str', $x->propertyTwo);
    }

    public function testSetterFirst()
    {
        $cnf = new Configurator(true, true);
        $x = new MockTwo();

        $this->assertNull($x->x);
        $cnf->applyPropertiesFirst($x, array('x'=>3));
        $this->assertSame(3, $x->x);
        $cnf->applySettersFirst($x, array('x'=>5));
        $this->assertSame(25, $x->x);
        $cnf->apply($x, array('x'=>21));
        $this->assertSame(441, $x->x);
    }

    public function testPropertiesFirst()
    {
        $cnf = new Configurator(true, false);
        $x = new MockThree();

        $this->assertNull($x->x);
        $cnf->apply($x, array('x'=>9));
        $this->assertSame(9, $x->x);

        try{
            $cnf = new Configurator(true, true);
            // Now should be exception
            $cnf->apply($x, array('x'=>9));
            $this->fail();
        } catch( \BadMethodCallException $e ){
            $this->assertTrue(true);
        }
    }

    public function testAdders()
    {
        $cnf = new Configurator(true, true);
        $x = new MockFour();

        $this->assertNull($x->x);
        $cnf->apply($x, array('x'=>2));
        $this->assertSame(8, $x->x);
    }

    public function testStrictMode()
    {
        $cnf = new Configurator(true, true);
        $x = new MockOne();

        try{
            $cnf->apply($x, array('propertyThree'=>123));
            $this->fail();
        } catch (\BadMethodCallException $e){
            $this->assertTrue(true);
        }

        // Exception suppressed, strict mode disabled
        $cnf = new Configurator(false, true);
        $x = new MockOne();
        $cnf->apply($x, array('propertyThree'=>123, 'propertyOne'=>12));
        $this->assertSame(12, $x->propertyOne);
    }

}

class MockOne
{
    public $propertyOne;
    public $propertyTwo;
}

class MockTwo
{
    public $x;

    public function setX($x)
    {
        $this->x = $x * $x;
    }
}

class MockThree
{
    public $x;

    public function setX($x)
    {
        throw new \BadMethodCallException();
    }

}

class MockFour
{
    public $x;

    public function setX($x)
    {
        $this->x = $x * $x;
    }

    public function addX($x)
    {
        $this->x = $x * $x * $x;
    }
}