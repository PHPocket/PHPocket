<?php

namespace PHPocket\Tests\Web;

use PHPocket\Web\HTTPStatusCodes;

class HTTPStatusCodesTest extends \PHPUnit_Framework_TestCase
{

    public function testSomeEntries()
    {
        // We cannot test a dictionary, so we just take
        // some of the values

        $this->assertEquals(
            'HTTP/1.0 404 Not Found',
            HTTPStatusCodes::getFullText(404)
        );
        $this->assertEquals(
            'HTTP/1.1 410 Gone',
            HTTPStatusCodes::getFullText(410)
        );
        $this->assertEquals(
            'HTTP/1.1 418 I\'m a teapot',
            HTTPStatusCodes::getFullText(418)
        );

        $this->assertEquals(
            'HTTP/1.0 200 OK',
            HTTPStatusCodes::getFullText(200)
        );


        // Exception
        try {
            HTTPStatusCodes::getFullText(-1);
            $this->fail();
        } catch( \InvalidArgumentException $e ){
            $this->assertTrue(true);
        }
        try {
            HTTPStatusCodes::getFullText(1000);
            $this->fail();
        } catch( \InvalidArgumentException $e ){
            $this->assertTrue(true);
        }
        try {
            HTTPStatusCodes::getFullText(null);
            $this->fail();
        } catch( \InvalidArgumentException $e ){
            $this->assertTrue(true);
        }
    }


}