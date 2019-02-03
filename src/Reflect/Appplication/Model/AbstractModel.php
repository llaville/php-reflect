<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Model;

/**
 * A base class for all Model objects.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
abstract class AbstractModel
{
    protected $node;
    protected $extension;

    /**
     * Base Model class constructor
     */
    public function __construct($node)
    {
        $this->node = $node;

        $versions = $node->getAttribute('compatinfo');
        if ($versions === null) {
            $this->extension = 'user';
        } else {
            $this->extension = $versions['ext.name'];
        }
    }

    /**
     * Gets doc comments.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->node->getDocComment();
    }

    /**
     * Gets the starting line number.
     *
     * @return int
     * @see    getEndLine()
     */
    public function getStartLine()
    {
        return $this->node->getAttribute('startLine');
    }

    /**
     * Gets the ending line number.
     *
     * @return int
     * @see    getStartLine()
     */
    public function getEndLine()
    {
        return $this->node->getAttribute('endLine');
    }

    /**
     * Gets the filename of the file in which the element has been defined.
     *
     * @return string
     */
    public function getFileName()
    {
        return realpath($this->node->getAttribute('fileName', ''));
    }

    /**
     * Gets extension name.
     *
     * @return string
     */
    public function getExtensionName()
    {
        return $this->extension;
    }
}
