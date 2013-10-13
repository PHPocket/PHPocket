<?php
namespace PHPocket\Random;

/**
 * Interface for all random generators
 * @package PHPocket\Random
 */
interface RandomInterface
{


    /**
     * Return a random boolean value
     *
     * @return boolean
     */
    public function nextBoolean();
    /**
     * Return a value in range [0, $max] - inclusive
     *
     * @param int $max inclusive
     * @return int
     */
    public function nextInt($max);
    /**
     * Return a value in range (0, $max)
     *
     * @param float $max
     * @return float
     */
    public function nextFloat($max);

}