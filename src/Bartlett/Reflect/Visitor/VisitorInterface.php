<?php
/**
 * Common interface to all analysers using sniffs accessible through the AnalyserManager.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Visitor;

/**
 * Interface that all analysers using sniffs must implement.
 */
interface VisitorInterface
{
    public function setUpBeforeVisitor();
    public function tearDownAfterVisitor();
}
