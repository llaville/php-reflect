<?php declare(strict_types=1);

/**
 * ERROR event.
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
 * The ERROR event allows you to learn more about PHP-Parser error raised.
 *
 * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
 * instance with following arguments :
 * - `source` data source identifier
 * - `file`   current file parsed in the data source
 * - `error`  PHP Parser error message
 */
class ErrorEvent extends GenericEvent
{

}
