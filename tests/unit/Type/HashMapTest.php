<?php

namespace PHPocket\Tests\Type;


use PHPocket\Type\HashMap;

class HashMapTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        // Default constructor
        $x = new HashMap();
        $this->assertTrue($x->isEmpty());
        $this->assertTrue($x->isAssociative());

        // Null constructor
        $x = new HashMap(null);
        $this->assertTrue($x->isEmpty());

        // Array constructor
        $x = new HashMap(array('one'=>'two','three'=>'four'));
        $this->assertCount(2, $x);
        $this->assertEquals('four', $x->offsetGet('three'));

        // HashMap constructor
        $y = new HashMap($x);
        $this->assertCount(2, $y);
        $this->assertEquals('four', $y->offsetGet('three'));
    }

    public function testCount()
    {
        $this->assertCount(0, new HashMap());
        $this->assertCount(0, new HashMap(null));
        $this->assertCount(1, new HashMap(array('one'=>'two')));
        $this->assertCount(5, new HashMap(array(1,2,3,4,5)));
    }

    public function testIsEmpty()
    {
        $x = new HashMap();
        $this->assertTrue($x->isEmpty());
        $x->offsetSet('pff', 'xxx');
        $this->assertFalse($x->isEmpty());
        $x->offsetUnset('pff');
        $this->assertTrue($x->isEmpty());
        $x->offsetSet('pff', null);
        $this->assertFalse($x->isEmpty());
    }

    public function testEquals()
    {
        ////// Compare to HashMap
        $x = new HashMap();
        // Compare to null
        $this->assertFalse($x->equals(null));
        // Compare to empty
        $this->assertTrue($x->equals(new HashMap()));
        $x->offsetSet('foo', 'bar');
        $this->assertFalse($x->equals(new HashMap()));

        ////// Compare to array
        $x = new HashMap();
        $this->assertTrue($x->equals(array()));
        $x->offsetSet('for', 'bar');
        $x->offsetSet('one', 'two');
        $this->assertTrue($x->equals(array('for' => 'bar', 'one' => 'two')));
        $this->assertFalse($x->equals(array('one' => 'bar', 'for' => 'two')));
        $this->assertTrue($x->equals(array('one' => 'two', 'for' => 'bar')));
        $this->assertFalse($x->equals(array('for' => 'bar')));
    }

    public function testContainsKey()
    {
        $x = new HashMap(array('foo' => 'bar'));
        $this->assertTrue($x->containsKey('foo'));
        $this->assertFalse($x->containsKey('bar'));
        $x->offsetSet('foo', null);
        $this->assertTrue($x->containsKey('foo'));
    }

    public function testContainsValue()
    {
        $x = new HashMap(array('foo' => 'bar'));
        $this->assertTrue($x->containsValue('bar'));
        $this->assertFalse($x->containsValue('foo'));
    }

    public function testIntersect()
    {
        $x = new HashMap(
            array(
            'a' => 'apple', 'b' => 'banana', 'c' => 'cherry'
            )
        );
        $y = array('b' => 'banana', 'a' => 'apple', 'd' => 'donut');

        $z = $x->intersect($y);
        $this->assertCount(2, $z);
        $this->assertTrue($z->containsKey('a'));
        $this->assertTrue($z->containsKey('b'));
        $this->assertFalse($z->containsKey('c'));
        $this->assertFalse($z->containsKey('d'));
    }

    public function testDiffBoth()
    {
        $x = new HashMap(
            array(
                'a' => 'apple', 'b' => 'banana', 'c' => 'cherry'
            )
        );
        $y = array('b' => 'banana', 'a' => 'apple', 'd' => 'donut');

        $z = $x->diff($y);
        $this->assertCount(2, $z);
        $this->assertFalse($z->containsKey('a'));
        $this->assertFalse($z->containsKey('b'));
        $this->assertTrue($z->containsKey('c'));
        $this->assertTrue($z->containsKey('d'));
    }

    public function testMerge()
    {
        $x = new HashMap(
            array(
                'a' => 'apple', 'b' => 'banana'
            )
        );

        $y = $x->merge(array('b' => 'boomerang', 'c' => 'cherry'));
        $this->assertCount(2, $x);
        $this->assertCount(3, $y);
        $this->assertEquals('banana', $x['b']);
        $this->assertEquals('apple', $y['a']);
        $this->assertEquals('boomerang', $y['b']);
        $this->assertEquals('cherry', $y['c']);
    }

    public function testMap()
    {
        $x = new HashMap(array('p' => 9, 'q' => 25));
        $y = $x->map('sqrt');

        $this->assertEquals(9, $x['p']); // Immutable
        $this->assertEquals(25, $x['q']); // Immutable
        $this->assertEquals(3, $y['p']);
        $this->assertEquals(5, $y['q']);
    }

    public function testTrim()
    {
        $x = new HashMap(
            array(
                'p'=>9,
                'q'=>null, 'r'=>false, 'z' => ''
            )
        );
        $this->assertCount(4, $x);
        $y = $x->trim();
        $this->assertCount(4, $x); // Immutable
        $this->assertCount(1, $y);
        $this->assertTrue(isset($x['q']));
        $this->assertFalse(isset($y['q']));
        $this->assertTrue(isset($x['p']));
    }

    public function testGetKeys()
    {
        $x = new HashMap(array('p' => 9, 'q' => 0.3));
        $this->assertEquals(array('p', 'q'), $x->getKeys());
    }

    public function testGetValues()
    {
        $x = new HashMap(array('p' => 9, 'q' => 0.3));
        $this->assertEquals(array(9, 0.3), $x->getValues());
    }

    public function testIsAssociative()
    {
        $x = new HashMap();
        $this->assertTrue($x->isAssociative());
        $x = new HashMap(array('p' => 9, 'q' => 0.3));
        $this->assertTrue($x->isAssociative());
        $x = new HashMap(array(9, 0.3));
        $this->assertFalse($x->isAssociative());
    }

    public function testTraversable()
    {
        $x = new HashMap(array('p' => 9, 'q' => 0.3));
        $keys = array('p', 'q');
        $values = array(9, 0.3);
        foreach ($x as $k => $v) {
            $this->assertSame(array_shift($keys), $k);
            $this->assertSame(array_shift($values), $v);
        }
    }

    public function testArrayAccess()
    {
        $x = new HashMap(array('p' => 9, 'q' => 0.3));
        $this->assertTrue(isset($x['p']));
        $this->assertFalse(isset($x['pp']));
        $this->assertSame(0.3, $x['q']);
        unset($x['q']);
        $this->assertFalse(isset($x['q']));
        $x['q'] = 777;
        $this->assertTrue(isset($x['q']));
        $this->assertSame(777, $x['q']);
    }

}