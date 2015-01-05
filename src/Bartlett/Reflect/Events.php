<?php

namespace Bartlett\Reflect;

/**
 * Contains all events dispatched.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.7.0
 */
final class Events
{
    /**
     * The PROGRESS event allows you to know what file of the data source
     * is ready to be parsed.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
     * instance with following arguments :
     * - `source` data source identifier
     * - `file`   current file parsed in the data source
     *
     * @var string
     */
    const PROGRESS = 'reflect.progress';

    /**
     * The SUCCESS event allows you to get the AST (Abstract Syntax Tree)
     * from a live request. A cached request will not trigger this event.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
     * instance with following arguments :
     * - `source` data source identifier
     * - `file`   current file parsed in the data source
     * - `ast`    the Abstract Syntax Tree result (serialized)
     *
     * @var string
     */
    const SUCCESS = 'reflect.success';

    /**
     * The ERROR event allows you to learn more about PHP-Parser error raised.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
     * instance with following arguments :
     * - `source` data source identifier
     * - `file`   current file parsed in the data source
     * - `error`  PHP Parser error message
     *
     * @var string
     */
    const ERROR = 'reflect.error';

    /**
     * The COMPLETE event allows you to be notified when a data source parsing
     * is over.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
     * instance with following arguments :
     * - `source` data source identifier
     *
     * @var string
     */
    const COMPLETE = 'reflect.complete';

    /**
     * The BUILD event allows you to learn what are processes applied during AST building.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\GenericEvent
     * instance with following arguments :
     * - `method` current process
     * - `node`   current node visited
     *
     * @var string
     */
    const BUILD = 'ast.build';
}
