<?php
/**
 * Helper class to format time string.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Util;

/**
 * Helper class to format time string.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha3+1
 */
class Timer
{
    /**
     * Formats the elapsed time as a string.
     *
     * This code has been copied and adapted from phpunit/php-timer
     *
     * @param int $time The period duration (in milliseconds)
     *
     * @return string
     */
    public static function toTimeString($time)
    {
        $times = array(
            'hour'   => 3600000,
            'minute' => 60000,
            'second' => 1000
        );

        $ms = $time;

        foreach ($times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }
        return $ms . ' ms';
    }
}
