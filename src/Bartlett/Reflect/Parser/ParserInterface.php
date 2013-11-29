<?php

namespace Bartlett\Reflect\Parser;

interface ParserInterface
{
    /**
     * Parses a php token.
     *
     * @param array $request Command to process
     *
     * @return object
     */
    public function handle($request);
}
