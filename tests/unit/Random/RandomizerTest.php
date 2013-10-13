<?php

namespace PHPocket\Tests\Random;


use PHPocket\Random\Randomizer;

class RandomizerTest extends \PHPUnit_Framework_TestCase
{
    const ITERATIONS_LIMIT = 10;

    public function testNextIndex()
    {
        $x = new Randomizer();
        $array = array(1,2,3,4);
        $size = count($array);

        $answers = array();
        for ($i = 0; $i < $size; $i++) {
            $answers[$i] = false;
        }

        $i = self::ITERATIONS_LIMIT * $size;
        while ($i-- > 0) {
            $answers[$x->nextIndex($array)] = true;
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

    public function testNextItem()
    {
        $x = new Randomizer();
        $array = array("one", "two");

        $i = self::ITERATIONS_LIMIT;
        while (($current = $x->nextItem($array)) != "two") {
            if ($current === null) {
                $this->fail('Null received');
            }
            if ($i-- === 0) $this->fail('Random failed');
        }
        $i = self::ITERATIONS_LIMIT;
        while (($current = $x->nextItem($array)) != "one") {
            if ($current === null) {
                $this->fail('Null received');
            }
            if ($i-- === 0) $this->fail('Random failed');
        }
        $this->assertTrue(true);
    }
}