<?php

namespace Bartlett\Reflect\PhpParser\Lexer;

/**
 * This lexer is a feature available on PHP-Parser 1.1.0
 *
 * @author Nikita Popov
 * @author Christoph M. Becker
 * @link   https://gist.github.com/nikic/04fce01e69ae5b7b44f8
 * @link   https://github.com/nikic/PHP-Parser/issues/136
 */

class TokenOffsets extends \PhpParser\Lexer\Emulative
{
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null)
    {
        $token = parent::getNextToken($value, $startAttributes, $endAttributes);
        $startAttributes['startTokenPos'] = $endAttributes['endTokenPos'] = $this->pos;
        return $token;
    }

    public function getTokens()
    {
        return $this->tokens;
    }
}
