<?php

namespace Bartlett\Reflect\PhpParser;

use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;

/**
 * Version of PHP-Parser 1.3 Emulative Lexer that support new option 'allowKeywordsReserved'
 * that allow to detect usage of keywords reserved when running on a PHP 5.3 platform
 *
 * @link https://github.com/nikic/PHP-Parser/issues/202
 */
class EmulativeLexer extends Emulative
{
    protected $allowKeywordsReserved;
    protected $inObjectDeclaration;
    protected $keywordsReserved;

    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->inObjectDeclaration = true;

        if (isset($options['allowKeywordsReserved'])
            && is_bool($options['allowKeywordsReserved'])
        ) {
            $this->allowKeywordsReserved = $options['allowKeywordsReserved'];
        } else {
            // auto-detect depending of platform
            $this->allowKeywordsReserved = version_compare(PHP_VERSION, self::PHP_5_4, '<');
        }

        $this->keywordsReserved = array(
            'finally'       => Parser::T_FINALLY,
            'yield'         => Parser::T_YIELD,
            'callable'      => Parser::T_CALLABLE,
            'insteadof'     => Parser::T_INSTEADOF,
            'trait'         => Parser::T_TRAIT,
            '__trait__'     => Parser::T_TRAIT_C,
        );
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null)
    {
        $token = Lexer::getNextToken($value, $startAttributes, $endAttributes);

        // replace new keywords by their respective tokens. This is not done
        // if we currently are in an object access (e.g. in $obj->namespace
        // "namespace" stays a T_STRING tokens and isn't converted to T_NAMESPACE)
        if (Parser::T_STRING === $token && !$this->inObjectAccess && $this->inObjectDeclaration) {
            if (isset($this->newKeywords[strtolower($value)])) {
                return $this->newKeywords[strtolower($value)];
            }
        } elseif (isset($this->keywordsReserved[strtolower($value)]) && $this->allowKeywordsReserved && !$this->inObjectDeclaration) {
            $token = Parser::T_STRING;
        } else {
            // keep track of whether we currently are in an object declaration context
            $this->inObjectDeclaration = in_array($token, array(Parser::T_COMMENT, Parser::T_DOC_COMMENT, ord(';'), ord('}'), ord('{')));

            // keep track of whether we currently are in an object access (after ->)
            $this->inObjectAccess = Parser::T_OBJECT_OPERATOR === $token;
        }

        return $token;
    }
}
