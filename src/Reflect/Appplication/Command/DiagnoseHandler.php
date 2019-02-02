<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Application\Command;

use ZendDiagnostics\Check\ExtensionLoaded;
use ZendDiagnostics\Check\PhpVersion;
use ZendDiagnostics\Runner\Runner;

/**
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class DiagnoseHandler implements CommandHandlerInterface
{
    public function __invoke(DiagnoseCommand $command): array
    {
        $runner = new Runner();

        $runner->addCheck(new PhpVersion('7.1.0'));

        $extensions = ['tokenizer', 'pcre', 'spl', 'json', 'date', 'reflection'];
        $runner->addCheck(new ExtensionLoaded($extensions));

        $response = [
            'checks'  => $runner->getChecks(),
            'results' => $runner->run(),
        ];

        return $response;
    }
}
