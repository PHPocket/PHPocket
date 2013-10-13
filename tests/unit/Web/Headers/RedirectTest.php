<?php
namespace PHPocket\Tests\Web\Headers;


use PHPocket\Web\Headers\Redirect;

class RedirectTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $x = new Redirect('   http://ya.ru ');
        $this->assertSame('Location', $x->getType());
        $this->assertSame('http://ya.ru', $x->getValue());
    }

}