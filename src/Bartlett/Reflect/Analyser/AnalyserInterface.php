<?php
/**
 * Common interface to all analysers accessible through the AnalyserManager.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect;

/**
 * Interface that all analysers must implement.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.0.0RC2
 */
interface AnalyserInterface
{
    public function getSubject();

    public function getCurrentFile();

    public function getTokens();

    public function setSubject(Reflect $reflect);

    public function setTokens(array $tokens);

    public function setCurrentFile($path);

    public function getMetrics();

    public function getName();

    public function getNamespace();

    public function getShortName();
}
