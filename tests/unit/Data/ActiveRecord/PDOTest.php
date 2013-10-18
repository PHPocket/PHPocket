<?php

namespace PHPocket\Tests\Data\ActiveRecord;

use PHPocket\Data\ActiveRecord\PDO;
use PHPocket\Type\ID;

class PDOTest extends \PHPUnit_Framework_TestCase
{
    private $_pdo;

    public function setUp(){
        $this->_pdo = new \PDO('sqlite::memory:');
        $x = $this->_pdo->exec(file_get_contents(__DIR__ . '/PDOTest.fixture.sql'));
        if ($x === false) {
            throw new \Exception('Fixturing failed');
        }
    }

    public function testLoad()
    {
        $x1 = PDO::load(new ID(1), $this->_pdo, 'User');
        $x2 = PDO::load(new ID(2), $this->_pdo, 'User');
        $x3 = PDO::load(new ID('foo'), $this->_pdo, 'StringKey', 'key');
        $x4 = PDO::load(new ID('hash'), $this->_pdo, 'StringKey', 'key');
        $this->assertEquals('User one', $x1->offsetGet('name'));
        $this->assertEquals(22, $x1->offsetGet('age'));
        $this->assertEquals(0.22, $x1->offsetGet('rating'));

        $this->assertEquals(2342.111, $x2->offsetGet('rating'));

        $this->assertEquals(123, $x3->offsetGet('value'));
        $this->assertEquals(555, $x4->offsetGet('value'));

        $x = PDO::load(new ID(555), $this->_pdo, 'User');
        $this->assertTrue($x->getID()->isEmpty());

        try {
            $x = PDO::load( ID::getNew(), $this->_pdo, 'User' );
            $this->fail();
        } catch (\InvalidArgumentException $e){
            $this->assertTrue(true);
        }
    }

    public function testGettersAndSetters()
    {
        $x = PDO::load(new ID(1), $this->_pdo, 'User');
        try {
            $x->offsetGet('notExistant');
            $this->fail();
        } catch (\InvalidArgumentException $e){
            $this->assertTrue(true);
        }
        try {
            $x->offsetSet('notExistant', 'some');
            $this->fail();
        } catch (\InvalidArgumentException $e){
            $this->assertTrue(true);
        }

        $x->offsetSet('address', null);
        $this->assertNull($x->offsetGet('address'));
        $x->save();
        $x = PDO::load(new ID(1), $this->_pdo, 'User');
        $this->assertNull($x->offsetGet('address'));

        $y = PDO::load(new ID(555), $this->_pdo, 'User');
        try {
            $y->offsetGet('age');
            $this->fail();
        } catch (\BadMethodCallException $e){
            $this->assertTrue(true);
        }
        try {
            $y->offsetSet('age', 'some');
            $this->fail();
        } catch (\BadMethodCallException $e){
            $this->assertTrue(true);
        }
    }

    public function testCreate()
    {
        $x = PDO::createNew($this->_pdo, 'User');
        $x->offsetSet('name', 'Three');
        $x->offsetSet('age', 26);
        $x->save();
        $this->assertEquals(3, $x->getID()->getInt());

        $y = PDO::load($x->getID(), $this->_pdo, 'User');
        $this->assertEquals('Three', $y->offsetGet('name'));
        $this->assertEquals(26, $y->offsetGet('age'));

        $x = PDO::createNew($this->_pdo, 'StringKey', 'key');
        $x->offsetSet('key', 'hashXXX');
        $x->offsetSet('value', 2);
        $x->save();
        $this->assertEquals('hashXXX', (string) $x->getID());

        $y = PDO::load($x->getID(), $this->_pdo, 'StringKey', 'key');
        $this->assertEquals(2, $y->offsetGet('value'));
    }

    public function testUpdate()
    {
        $x = PDO::load(new ID(2), $this->_pdo, 'User');
        $x->offsetSet('name', 'Replaced name');
        $y = PDO::load(new ID(2), $this->_pdo, 'User');
        $this->assertNotEquals('Replaced name', $y->offsetGet('name'));
        $x->save();
        $y = PDO::load(new ID(2), $this->_pdo, 'User');
        $this->assertEquals('Replaced name', $y->offsetGet('name'));

        $x = PDO::load(new ID('hash'), $this->_pdo, 'StringKey', 'key');
        $x->offsetSet('value', 1983);
        $x->save();
        $y = PDO::load(new ID('hash'), $this->_pdo, 'StringKey', 'key');
        $this->assertEquals(1983, $y->offsetGet('value'));

    }

    public function testDelete()
    {
        $x = PDO::load(new ID(2), $this->_pdo, 'User');
        $this->assertFalse($x->getID()->isSpecial());
        $this->assertEquals(44, $x->offsetGet('age'));

        $x->delete();
        $this->assertTrue($x->getID()->isSpecial());
        try{
            $x->offsetGet('age');
            $this->fail();
        } catch( \BadMethodCallException $e){
            $this->assertTrue(true);
        }

        $x = PDO::load(new ID(2), $this->_pdo, 'User');
        $this->assertTrue($x->getID()->isSpecial());
    }
}
