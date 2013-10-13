<?php
namespace PHPocket\Random;

/**
 * Randomizer, that provides nice functions for various cases
 * Uses strategy pattern for internal RandomInterface object, which
 * is responsible for providing randomness
 *
 *
 * @package PHPocket\Random
 */
class Randomizer implements RandomInterface
{
    /**
     * Randomness provider
     *
     * @var RandomInterface
     */
    protected $_random = null;

    /**
     * Constructor
     *
     * @param RandomInterface|null $randomProvider <p>
     * if provided, uses object as randomness provider
     * </p>
     */
    public function __construct(RandomInterface $randomProvider = null)
    {
        if ($randomProvider !== null) {
            $this->setRandomProvider($randomProvider);
        }
    }

    /**
     * Sets internal randomness provider
     *
     * @param RandomInterface $randomProvider
     */
    public function setRandomProvider(RandomInterface $randomProvider)
    {
        $this->_random = $randomProvider;
    }

    /**
     * Returns randomness provider
     *
     * @return RandomInterface
     */
    public function getRandomProvider()
    {
        if ($this->_random === null) {
            $this->_random = new Mersenne();
        }
        return $this->_random;
    }

    /**
     * Return a random boolean value
     *
     * @return boolean
     */
    public function nextBoolean()
    {
        return $this->getRandomProvider()->nextBoolean();
    }

    /**
     * Return a value in range [0, $max] - inclusive
     *
     * @param int $max inclusive
     * @return int
     */
    public function nextInt($max)
    {
        return $this->getRandomProvider()->nextInt($max);
    }

    /**
     * Return a value in range (0, $max)
     *
     * @param float $max
     * @return float
     */
    public function nextFloat($max)
    {
        return $this->getRandomProvider()->nextFloat($max);
    }

    /**
     * Returns a random integer value inside range $min,$max
     *
     * @param int  $min       Minimal value
     * @param int  $max       Max value
     * @param bool $inclusive if true, random value could be $min or $max
     *
     * @return int
     */
    public function nextIntInRange($min, $max, $inclusive = true)
    {
        if (!$inclusive) {
            $min++;
            $max--;
        }

        if ( $min > $max ) {
            // Swapping
            list($min, $max) = array($max, $min);
        }

        return $this->nextInt($max-$min) + $min;
    }

    /**
     * Returns a random index of the array of any other countable
     *
     * @param array|\Countable $collection
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function nextIndex($collection)
    {
        if (empty($collection)) {
            throw new \InvalidArgumentException('Empty argument');
        }
        if (!is_array($collection) && !($collection instanceof \Countable)) {
            throw new \InvalidArgumentException('Array or Countable expected');
        }

        return $this->nextInt(count($collection) - 1);
    }

    /**
     * Returns a random element from collection
     *
     * @param array|\Iterator $collection            collection of Items
     * @param bool            $extractValuesForArray
     *
     * @return null|mixed
     *
     * @throws \InvalidArgumentException if provided collection is not array
     * or Iterator
     */
    public function nextItem($collection, $extractValuesForArray = true)
    {
        if (empty($collection)) {
            return null;
        }
        $values = array();
        if (is_array($collection)) {
            if (!$extractValuesForArray) {
                // We assuming, that $array is common linear array
                $values = $collection;
            } else {
                $values = array_values($collection);
            }
        } else if ($collection instanceof \Iterator) {
            // Iterating over object
            foreach ($collection as $row) {
                $values[] = $row;
            }
        } else {
            throw new \InvalidArgumentException('Expecting array or Iterator');
        }

        return $values[$this->nextIndex($values)];
    }
}