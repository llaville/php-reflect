<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Tokenizer;

class DefaultTokenizer
{
    protected $tokenStack;

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

    public function setTokens($tokens)
    {
        $this->tokenize($tokens);
    }

    public function setSourceFile($file)
    {
        $this->tokenize(
            token_get_all(
                $file->getContents()
            )
        );
    }

    protected function tokenize($tokens)
    {
        $this->tokenStack = new \SplDoublyLinkedList;

        foreach ($tokens as $id => $token) {
            if (is_array($token)) {
                $text      = $token[1];
                $line      = $token[2];
                $tokenName = token_name($token[0]);

                if ($token[0] == T_WHITESPACE) {
                    $lines = substr_count($text, "\n");
                    if ($lines > 0) {
                        --$lines;
                        $line += $lines;
                    }
                }
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

            if ('T_HALT_COMPILER' == $tokenName) {
                break;
            }
        }
    }
}
