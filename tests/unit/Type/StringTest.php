<?php
namespace PHPocket\Tests\Type;


use PHPocket\Type\String;

class StringTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $this->assertEquals('', (string) new String());
        $this->assertEquals('', (string) new String(null));
        $this->assertEquals('abc', (string) new String('abc'));
        $this->assertSame('abc', (string) new String('abc'));
        $this->assertEquals('1234', (string) new String(1234));
        $this->assertEquals('abc', (string) new String(new String('abc')));
        $this->assertNotEquals('Аф', new String(chr(0xc0) . chr(0xf4)));
        $this->assertEquals('Аф', new String(chr(0xc0) . chr(0xf4), 'windows-1251'));
        $this->assertEquals('Àô', new String(chr(0xc0) . chr(0xf4), 'windows-1252'));
        $this->assertEquals('Аф', new String('Аф', 'utf-8'));
        $this->assertEquals('Аф', new String('Аф', 'UTF8'));
    }

    public function testLength()
    {
        $x = new String('hello, world');
        $this->assertSame(12, $x->length());
        $this->assertSame(12, $x->count());
        $this->assertSame(12, $x->byteSize());
        $this->assertFalse($x->isEmpty());
        $this->assertFalse($x->isEmptyOrWhiteSpace());

        $x = new String('Русский mixed');
        $this->assertSame(13, $x->length());
        $this->assertSame(13, $x->count());
        $this->assertSame(20, $x->byteSize());
        $this->assertFalse($x->isEmpty());
        $this->assertFalse($x->isEmptyOrWhiteSpace());

        $x = new String();
        $this->assertSame(0, $x->length());
        $this->assertSame(0, $x->count());
        $this->assertSame(0, $x->byteSize());
        $this->assertTrue($x->isEmpty());
        $this->assertTrue($x->isEmptyOrWhiteSpace());
    }

    public function testWhitespaces()
    {
        $x = new String('   ');
        $this->assertSame(3, $x->length());
        $this->assertSame(3, $x->byteSize());
        $this->assertFalse($x->isEmpty());
        $this->assertTrue($x->isEmptyOrWhiteSpace());

        $y = $x->trim();
        $this->assertSame(0, $y->length());
        $this->assertSame(0, $y->byteSize());
        $this->assertTrue($y->isEmpty());
        $this->assertTrue($y->isEmptyOrWhiteSpace());

        $x = new String('x ');
        $this->assertEquals('x ', $x);
        $this->assertEquals('x', $x->trim());

        $x = new String(' x ');
        $this->assertEquals(' x ', $x);
        $this->assertEquals('x', $x->trim());
    }

    public function testSubstrings()
    {
        $x = new String('Hello, world!');
        $this->assertSame($x, $x->substring(0));

        $this->assertEquals('!', $x->charAt(-1));
        $this->assertEquals('!', $x->substring(-1));
        $this->assertEquals('!', $x->substring(-1, 2));

        $this->assertEquals('d', $x->charAt(-2));
        $this->assertEquals('d!', $x->substring(-2));

        $this->assertEquals('e', $x->charAt(1));
        $this->assertEquals('el', $x->substring(1, 2));

        $this->assertEquals('world!', $x->substring(7));

        $this->assertEquals('Hello, world!123', $x->concat(123));

        $this->assertTrue($x->contains('world'));
        $this->assertFalse($x->contains('hell'));
    }

    public function testCmp()
    {
        $x = new String('Abc');
        $this->assertLessThan(0, $x->compareTo('Bbc'));
        $this->assertSame(0, $x->compareTo('Abc'));
        $this->assertLessThan(0, $x->compareTo('abc'));

        $this->assertSame(0, $x->compareToIgnoreCase('abc'));
    }

    public function testEquals()
    {
        $c = new String('Яq');
        $this->assertTrue($c->equals('Яq'));
        $this->assertFalse($c->equals('ЯQ'));
        $this->assertFalse($c->equals('Яq '));
        $this->assertTrue($c->equalsIgnoreCase('яQ'));
        $this->assertFalse($c->equalsIgnoreCase(' яQ'));

        $this->assertTrue($c->startsWith('Я'));
        $this->assertFalse($c->startsWith('я'));

        $this->assertTrue($c->endsWith('q'));
        $this->assertFalse($c->endsWith('Q'));
    }

    public function testRegexes()
    {
        $x = new String('test@example.com');
        $y = new String('one123two456three');

        $this->assertTrue($x->matches('/^[a-z]+@[a-z\.]+$/'));
        $this->assertFalse($y->matches('/^[a-z]+@[a-z\.]+$/'));


        $this->assertEquals(array('one','two','three'), $y->split('/[0-9]+/', null, false));
        $this->assertEquals(array('one','two456three'), $y->split('/[0-9]+/', 2, false));

        $this->assertEquals(
            array(new String('one'), new String('two'), new String('three')),
            $y->split('/[0-9]+/', null, true)
        );
    }

    public function testExplode()
    {
        $x = new String('1;2;3;four');
        $this->assertEquals(array(1, 2, 3, 'four'), $x->explode(';', false));
        $x = new String('1 2');
        $this->assertEquals(
            array(new String('1'), new String(2)),
            $x->explode(' ')
        );
    }

}