<?php

namespace Bartlett\Reflect\Token;

abstract class AbstractToken
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @var integer
     */
    protected $line;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var array
     */
    protected $tokenStream;

    /**
     * Constructor.
     *
     * @param string  $text
     * @param integer $line
     * @param integer $id
     * @param array   $tokens
     */
    public function __construct($text, $line, $id, $tokens)
    {
        $this->text        = $text;
        $this->line        = $line;
        $this->id          = $id;
        $this->tokenStream = $tokens;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }
}
