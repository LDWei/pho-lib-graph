<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Lib\Graph;

/**
 * Defines Edge Direction.
 * 
 * This class is a PHP implementation of struct. You can call a 
 * an "In" direction with ```Direction::in()``` and it would
 * still be an instance of the ```Direction::class```
 * 
 * @author  Emre Sokullu <emre@phonetworks.org>
 */
class Direction {
    
    const IN = "in";
    const OUT = "out";

    private $value;
    
    /**
     * Constructor.
     *
     * No need to check for value validity since the method
     * is set to be private, hence cannot be called by others.
     * 
     * @param string $value Direction::in() or Direction::out()
     */
    private function __construct(string $value) {
        $this->value = $value;
    }

    /**
     * Represents incoming
     *
     * @return Direction
     */
    public static function in() {
        return new Direction(self::IN);
    }

    /**
     * Represents outgoin
     *
     * @return Direction
     */
    public static function out() {
        return new Direction(self::OUT);
    }

    /**
     * Verifies identicality
     *
     * @return bool
     */
    public function equals(Direction $direction) {
        return ($this->value == (string) $direction);
    }

    /**
     * {@internal}
     * 
     * Stringifies
     *
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

}