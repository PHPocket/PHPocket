<?php

namespace PHPocket\Tests\Serialize;


use PHPocket\Serialize\JSON;

class JSONTest extends \PHPUnit_Framework_TestCase
{

    public function testPackCommon()
    {
        $x = new JSON(false);

        $this->assertSame('null', $x->pack(null));
        $this->assertSame('false', $x->pack(false));
        $this->assertSame('true', $x->pack(true));
        $this->assertSame('123', $x->pack(123));
        $this->assertSame('0.2', $x->pack(0.2));
        $this->assertSame('"hello"', $x->pack('hello'));
        $this->assertSame('"\u043c\u0438\u0440"', $x->pack('мир'));
        $this->assertSame('"\u263a"', $x->pack('☺'));

        $this->assertSame('{"one":"two"}', $x->pack(array('one'=>'two')));
        $this->assertSame('["two",3]', $x->packArray(array('one'=>'two', 3)));
    }

    public function testPackUTF()
    {
        $x = new JSON(true);

        $this->assertSame(
            '"мир ё труд 2000 май"',
            $x->pack('мир ё труд 2000 май')
        );
    }

    public function testPackUnicode6()
    {
        $x = new JSON(false);
        $y = new JSON(true);
        $char = "\xf0\x9f\x98\x81";

        $this->assertSame('"\ud83d\ude01"', $x->pack($char));
        $this->assertSame('"' . $char .'"', $y->pack($char));
    }

    public function testUnpack()
    {
        $x = new JSON(false);

        // Wrong format
        $this->assertNull($x->unpack('раз')); // wrong string

        // Common
        $this->assertSame(null, $x->unpack('null'));
        $this->assertSame(true, $x->unpack('true'));
        $this->assertSame(false, $x->unpack('false'));
        $this->assertSame(15, $x->unpack('15'));
        $this->assertSame(0.3, $x->unpack('0.3'));
        $this->assertSame('абв abc', $x->unpack('"абв abc"'));

        // Arrays
        $this->assertSame(array(1,2,'три'), $x->unpack('[1,2,"три"]'));
        $this->assertSame(
            array('one'=>'two'),
            $x->unpackArray('{"one": "two"}')
        );
        $std = new \StdClass();
        $std->one = "two";
        $this->assertEquals($std, $x->unpack('{"one": "two"}'));
    }

}