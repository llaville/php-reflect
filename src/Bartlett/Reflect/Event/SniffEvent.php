<?php declare(strict_types=1);

/**
 * SNIFF event.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 4.4.0
 */

namespace Bartlett\Reflect\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * The SNIFF event allows you to learn what are sniff processes during AST traverse.
 */
class SniffEvent extends GenericEvent
{

}
