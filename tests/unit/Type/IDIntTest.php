<?php
namespace PHPocket\Tests\Type;


use PHPocket\Type\IDInt;

class IDIntTest extends IDTest
{

    public function testIntConstructor()
    {
        $x = new IDInt(10);
        $this->assertEquals(10, $x->getValue());

        $x = new IDInt('10');
        $this->assertEquals(10, $x->getValue());

        $this->assertTrue($x->equals(10));
        $this->assertFalse($x->isSpecial());

        try{
            $x = new IDInt('10a');
            $this->fail('Expecting exception');
        } catch(\Exception $e){}

        try{
            $x = new IDInt(0.1);
            $this->fail('Expecting exception');
        } catch(\Exception $e){}
    }

}