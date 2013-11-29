<?php

namespace Bartlett\Reflect;

use Bartlett\Reflect\Parser\ParserInterface;
use Bartlett\Reflect\ProviderManager;

interface ManagerInterface
{
    /**
     * Pushes a parser on to the stack.
     *
     * @param ParserInterface $parser
     * @return void
     */
    public function pushParser(ParserInterface $parser);

    /**
     * Pops a parser from the stack.
     *
     * @return ParserInterface
     * @throws \LogicException if parsers stack is empty
     */
    public function popParser();

    /**
     * Returns an instance of the current provider manager.
     *
     * @return ProviderManager
     */
    public function getProviderManager();

    /**
     * Defines the current provider manager.
     *
     * @param ProviderManager $manager
     *
     * @return void
     */
    public function setProviderManager(ProviderManager $manager);
}
