<?php

namespace PHPocket\Tests\Type;

use PHPocket\Type\ID;

class IDTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $x = new ID(5);
        $x = new ID('5');
        $x = new ID('string');

        try{
            $x = new ID('');
            $this->fail('Expecting exception');
        } catch (\Exception $e){}
        try{
            $x = new ID(0);
            $this->fail('Expecting exception');
        } catch (\Exception $e){}
        try{
            $x = new ID(null);
            $this->fail('Expecting exception');
        } catch (\Exception $e){}
        try{
            $x = new ID(true);
            $this->fail('Expecting exception');
        } catch (\Exception $e){}
        try{
            $x = new ID(false);
            $this->fail('Expecting exception');
        } catch (\Exception $e){}
    }

    public function testCheck()
    {
        $x = new ID(10);
        $this->assertFalse($x->isSpecial());
        $this->assertFalse($x->isNew());
        $this->assertFalse($x->isEmpty());

        $x = ID::getEmpty();
        $this->assertTrue($x->isSpecial());
        $this->assertFalse($x->isNew());
        $this->assertTrue($x->isEmpty());

        $x = ID::getNew();
        $this->assertTrue($x->isSpecial());
        $this->assertTrue($x->isNew());
        $this->assertFalse($x->isEmpty());
    }

    public function testGetters()
    {
        $x = new ID('10');
        $this->assertSame(10, $x->getInt());
        $this->assertSame('10', $x->getString());
        $this->assertSame(10, $x->getValue());
        $this->assertSame('10', (string) $x->getValue());

        $x = new ID(10);
        $this->assertSame(10, $x->getInt());
        $this->assertSame('10', $x->getString());
        $this->assertSame(10, $x->getValue());
        $this->assertSame('10', (string) $x->getValue());

        $x = ID::getNew();
        try{
            $x->getValue();
            $this->fail('Expecting exception');
        } catch (\Exception $e){}

        $x = ID::getEmpty();
        try{
            $x->getValue();
            $this->fail('Expecting exception');
        } catch (\Exception $e){}
    }

    public function testEquality()
    {
        $x = new ID('10');
        $this->assertFalse($x->equals(ID::getEmpty()));
        $this->assertFalse($x->equals(ID::getNew()));
        $this->assertFalse($x->equals(new $x('11')));
        $this->assertTrue($x->equals(new $x('10')));
        $this->assertTrue($x->equals(new $x(10)));
        $this->assertTrue($x->equals(10));
        $this->assertTrue($x->equals('10'));

        $y = ID::getEmpty();
        $this->assertTrue($y->equals(null));
        $this->assertTrue($y->equals(''));
        $this->assertTrue($y->equals(0));
        $this->assertTrue($y->equals(ID::getEmpty()));
        $this->assertFalse($y->equals($x));
        $this->assertFalse($y->equals(10));
        $this->assertFalse($y->equals('11'));
        $this->assertFalse($y->equals(ID::getNew()));

        $y = ID::getNew();
        $this->assertFalse($y->equals(null));
        $this->assertFalse($y->equals(''));
        $this->assertFalse($y->equals(0));
        $this->assertFalse($y->equals(ID::getEmpty()));
        $this->assertFalse($y->equals($x));
        $this->assertFalse($y->equals(10));
        $this->assertFalse($y->equals('11'));
        $this->assertTrue($y->equals(ID::getNew()));
    }

    public function testToString()
    {
        $this->assertSame('10', (string) new ID(10));
        $this->assertSame('10', (string) new ID('10'));
        $this->assertSame('', (string) ID::getEmpty());
        $this->assertSame('', (string) ID::getNew());
    }
}