<?php

namespace Bartlett\Reflect\Tokenizer;

class DefaultTokenizer
{
    protected $tokenStack;
    protected $file;

    protected static $customTokens = array(
        '(' => 'T_OPEN_BRACKET',
        ')' => 'T_CLOSE_BRACKET',
        '[' => 'T_OPEN_SQUARE',
        ']' => 'T_CLOSE_SQUARE',
        '{' => 'T_OPEN_CURLY',
        '}' => 'T_CLOSE_CURLY',
        ';' => 'T_SEMICOLON',
        '.' => 'T_DOT',
        ',' => 'T_COMMA',
        '=' => 'T_EQUAL',
        '<' => 'T_LT',
        '>' => 'T_GT',
        '+' => 'T_PLUS',
        '-' => 'T_MINUS',
        '*' => 'T_MULT',
        '/' => 'T_DIV',
        '?' => 'T_QUESTION_MARK',
        '!' => 'T_EXCLAMATION_MARK',
        ':' => 'T_COLON',
        '"' => 'T_DOUBLE_QUOTES',
        '@' => 'T_AT',
        '&' => 'T_AMPERSAND',
        '%' => 'T_PERCENT',
        '|' => 'T_PIPE',
        '$' => 'T_DOLLAR',
        '^' => 'T_CARET',
        '~' => 'T_TILDE',
        '`' => 'T_BACKTICK'
    );

    public function getTokens()
    {
        $this->tokenStack->rewind();
        return $this->tokenStack;
    }

    public function setSourceFile($file)
    {
        $this->file = $file;
        $this->tokenStack = new \SplDoublyLinkedList;
        $this->tokenize();
    }

    protected function tokenize()
    {
        $source = $this->file->getContents();
        $line   = 1;
        $tokens = token_get_all($source);

        foreach ($tokens as $id => $token) {
            if (is_array($token)) {
                $text      = $token[1];
                $tokenName = token_name($token[0]);
            } else {
                $text      = $token;
                $tokenName = self::$customTokens[$token];
            }
            $this->tokenStack->push(
                array(
                    $tokenName,
                    $text,
                    $line,
                    $id
                )
            );

            $lines = substr_count($text, "\n");
            $line += $lines;

            if ('T_HALT_COMPILER' == $tokenName) {
                break;
            }
        }
    }
}
