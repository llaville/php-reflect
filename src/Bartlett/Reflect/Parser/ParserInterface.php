<?php
/**
 * Common interface to all parsers. 
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Parser;

/**
 * Each parser should implement this interface.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Interface available since Release 2.0.0RC1
 */
interface ParserInterface
{
    /**
     * Parses a php token.
     *
     * @param array $request Command to process
     *
     * @return mixed FALSE if the token is not accepted by the parser
     *               or its object representation
     */
    public function handle($request);
}
