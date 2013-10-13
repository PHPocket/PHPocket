<?php

namespace PHPocket\Random;

/**
 * Class Mersenne
 * A random generator based on standard PHP functions mt_rand
 *
 * @package PHPocket\Random
 */
class Mersenne implements RandomInterface
{


    /**
     * Return a random boolean value
     *
     * @return boolean
     */
    public function nextBoolean()
    {
        return \mt_rand(0, 1) === 1;
    }


    /**
     * Return a value in range [0, $max] inclusive
     *
     * @param int $max
     * @return int
     */
    public function nextInt($max)
    {
        return \mt_rand(0, $max);
    }

    /**
     * Return a value in range (0, $max)
     *
     * @param float $max
     * @return float
     */
    public function nextFloat($max)
    {
        return $max * (\mt_rand() / \mt_getrandmax());
    }

}