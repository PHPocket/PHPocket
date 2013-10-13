<?php

namespace PHPocket\Tests\Random;

use PHPocket\Random\Mersenne;

class MersenneTest extends \PHPUnit_Framework_TestCase
{
    const ITERATIONS_LIMIT = 10;

    public function testBoolean()
    {
        $x = new Mersenne();

        $i = self::ITERATIONS_LIMIT;
        while ($x->nextBoolean() === false) {
            if ($i-- === 0) $this->fail('Expecting true');
        }
        $i = self::ITERATIONS_LIMIT;
        while ($x->nextBoolean() === true) {
            if ($i-- === 0) $this->fail('Expecting false');
        }
        $this->assertTrue(true);
    }

    public function testInt()
    {
        $size = 3;
        $x = new Mersenne();
        $answers = array();
        for ($i = 0; $i < $size; $i++) {
            $answers[$i] = false;
        }

        $i = self::ITERATIONS_LIMIT * $size;
        while ($i-- > 0) {
            $answers[$x->nextInt($size-1)] = true;
            if (count($answers) != $size) {
                $this->fail('Random overflow');
            }
            $allok = true;
            for ($j = 0; $j < $size; $j++) {
                $allok = $allok && $answers[$j];
            }
            if ($allok) {
                $this->assertTrue(true);
                return;
            }
        }
        $this->fail('Iterations limit reached');
    }

    public function testFloat()
    {
        $max = 1;
        $x = new Mersenne();

        $i = self::ITERATIONS_LIMIT;
        while ($x->nextFloat($max) > $max/2) {
            if ($i-- === 0) $this->fail('Random failed');
        }

        $i = self::ITERATIONS_LIMIT;
        while ($x->nextFloat($max) <= $max/2) {
            if ($i-- === 0) $this->fail('Random failed');
        }
        $this->assertTrue(true);
    }

}