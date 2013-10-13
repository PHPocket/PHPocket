<?php
namespace PHPocket\Tests\Web\Headers;


use PHPocket\Web\Headers\Header;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndGetters()
    {
        $x = new Header('Location ', 'http://google.com');
        $this->assertSame('Location', $x->getType());
        $this->assertSame('http://google.com', $x->getValue());
    }

    public function testToString()
    {
        $x = new Header('Location ', 'http://google.com');
        $this->assertSame('Location: http://google.com', (string) $x);
    }

    public function testEqualsAndSame()
    {
        $x = new Header('Location ', 'http://google.com');
        $y = new Header('Location ', 'http://yahoo.com');
        $z = new Header('Location ', 'http://google.com');
        $q = new Header('Content-Type', '');

        $this->assertTrue($x->equals($z));
        $this->assertTrue($z->equals($x));
        $this->assertFalse($x->equals($y));
        $this->assertFalse($y->equals($x));

        $this->assertTrue($x->sameType($z));
        $this->assertTrue($z->sameType($x));
        $this->assertTrue($x->sameType($y));
        $this->assertTrue($y->sameType($z));

        $this->assertFalse($x->sameType($q));
        $this->assertFalse($x->equals($q));
    }

    public function testJSON()
    {
        $x = new Header('Location ', 'http://google.com');
        $this->assertSame('{"Location":"http://google.com"}', $x->toSimpleJSON());
        $x = new Header('Content-Type ', null);
        $this->assertSame('{"Content-Type":null}', $x->toSimpleJSON());
    }
}