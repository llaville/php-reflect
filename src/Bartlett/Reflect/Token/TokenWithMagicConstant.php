<?php

namespace Bartlett\Reflect\Token;

/**
 * @link http://www.php.net/manual/en/language.constants.predefined.php
 *       Magic constants
 */
abstract class TokenWithMagicConstant extends TokenWithScope
{
    protected $name;

    public function getName()
    {
        if ($this->name === null) {
            $this->name = $this->text;
        }
        return $this->name;
    }
}
