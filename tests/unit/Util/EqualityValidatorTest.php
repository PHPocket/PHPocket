<?php

namespace PHPocket\Tests\Util;

use PHPocket\Common\EqualsInterface;
use PHPocket\Util\EqualityValidator;

class EqualityValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testEquals()
    {
        $x = new EqualityValidator();
        $y = new EqualityValidator(true);
        $z = new EqualityValidator(false);

        $this->assertTrue($x->equals($y));
        $this->assertTrue($y->equals($x));
        $this->assertFalse($x->equals($z));
        $this->assertFalse($y->equals($z));
    }

    public function testAreEqualPrimitives()
    {
        $normal = new EqualityValidator(false);
        $strict = new EqualityValidator(true);

        $this->assertTrue($normal->areEqual(5, 5));
        $this->assertTrue($normal->areEqual(5, (int) 5));
        $this->assertTrue($normal->areEqual(5, (float) 5));
        $this->assertTrue($normal->areEqual(5, '5'));
        $this->assertTrue($normal->areEqual(0, false));
        $this->assertTrue($normal->areEqual(0, ''));
        $this->assertTrue($normal->areEqual(false, ''));

        $this->assertTrue($strict->areEqual(5, 5));
        $this->assertTrue($strict->areEqual(5, (int) 5));
        $this->assertFalse($strict->areEqual(5, (float) 5));
        $this->assertFalse($strict->areEqual(5, '5'));
        $this->assertFalse($strict->areEqual(0, false));
        $this->assertFalse($strict->areEqual(0, ''));
        $this->assertFalse($strict->areEqual(false, ''));
    }

    public function testAreEqualNull()
    {
        $x = new EqualityValidator(false);

        // Null equals only to null
        $this->assertTrue($x->areEqual(null, null));
        $this->assertFalse($x->areEqual(null, 0));
        $this->assertFalse($x->areEqual(null, false));
        $this->assertFalse($x->areEqual(null, ''));
    }

    public function testAreEqualObjects()
    {
        $x = new EqualityValidator();

        $this->assertTrue($x->areEqual(
            new MockEq(10, 1),
            new MockEq(10, 2)
        ));
        $this->assertFalse($x->areEqual(
            new MockEq(10, 1),
            new MockEq(10, 1)
        ));

        $this->assertTrue($x->areEqual('exclusion', new MockEq(1,2)));
        $this->assertTrue($x->areEqual(new MockEq(1,2), 'exclusion'));

        $this->assertFalse($x->areEqual(new MockEq(1,1), null));
    }

    public function testInArray()
    {
        $x = new EqualityValidator();

        $this->assertFalse(
            $x->inArray(array(1, 2), new MockEq(1,1))
        );
        $this->assertTrue(
            $x->inArray(array(1, 'exclusion', 2), new MockEq(1,1))
        );
        $this->assertTrue(
            $x->inArray(array(1, new MockEq(1, 2), 2), new MockEq(1,1))
        );
        $this->assertTrue(
            $x->inArray(array(1, new MockEq(1, 2), 2), 'exclusion')
        );
    }
}

class MockEq implements EqualsInterface
{
    public $x;
    public $y;

    function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }


    /**
     * Returns true if $object === 'exclusion'
     * or if object is MockEq, and x === x
     * and y !== y !!!!!!
     *
     * @param mixed $object Object to compare
     * @return mixed
     */
    public function equals($object)
    {
        if ($object === 'exclusion') return true;

        return $object !== null
            && $object instanceof MockEq
            && $object->x === $this->x
            && $object->y !== $this->y;
    }


}
