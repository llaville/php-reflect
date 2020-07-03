<?php declare(strict_types=1);

/**
 * PROGRESS event.
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
 * The PROGRESS event allows you to know what file of the data source
 * is ready to be parsed.
 *
 * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
 * instance with following arguments :
 * - `source` data source identifier
 * - `file`   current file parsed in the data source
 */
class ProgressEvent extends GenericEvent
{

}
