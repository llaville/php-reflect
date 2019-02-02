<?php

declare(strict_types=1);

namespace Bartlett\Reflect\Presentation\Console;

use Bartlett\Reflect\Application\Command\AnalyserListHandler;
use Bartlett\Reflect\Application\Command\AnalyserRunHandler;
use Bartlett\Reflect\Application\Command\ConfigValidateHandler;
use Bartlett\Reflect\Application\Command\DiagnoseHandler;
use Bartlett\Reflect\Application\Command\PluginListHandler;
use Bartlett\Reflect\Application\Command\ReflectionClassHandler;
use Bartlett\Reflect\Application\Command\ReflectionFunctionHandler;
use Bartlett\Reflect\Application\Command\DiagramClassHandler;
use Bartlett\Reflect\Application\Command\DiagramPackageHandler;

use Bartlett\Reflect\Application\Command\AnalyserListCommand as AppAnalyserListCommand;
use Bartlett\Reflect\Application\Command\AnalyserRunCommand as AppAnalyserRunCommand;
use Bartlett\Reflect\Application\Command\ConfigValidateCommand as AppConfigValidateCommand;
use Bartlett\Reflect\Application\Command\DiagnoseCommand as AppDiagnoseCommand;
use Bartlett\Reflect\Application\Command\PluginListCommand as AppPluginListCommand;
use Bartlett\Reflect\Application\Command\ReflectionClassCommand as AppReflectionClassCommand;
use Bartlett\Reflect\Application\Command\ReflectionFunctionCommand as AppReflectionFunctionCommand;
use Bartlett\Reflect\Application\Command\DiagramClassCommand as AppDiagramClassCommand;
use Bartlett\Reflect\Application\Command\DiagramPackageCommand as AppDiagramPackageCommand;

use Bartlett\Reflect\Presentation\Console\Command\AnalyserListCommand;
use Bartlett\Reflect\Presentation\Console\Command\AnalyserRunCommand;
use Bartlett\Reflect\Presentation\Console\Command\ConfigValidateCommand;
use Bartlett\Reflect\Presentation\Console\Command\DiagnoseCommand;
use Bartlett\Reflect\Presentation\Console\Command\PluginListCommand;
use Bartlett\Reflect\Presentation\Console\Command\ReflectionClassCommand;
use Bartlett\Reflect\Presentation\Console\Command\ReflectionFunctionCommand;
use Bartlett\Reflect\Presentation\Console\Command\DiagramClassCommand;
use Bartlett\Reflect\Presentation\Console\Command\DiagramPackageCommand;

use Bartlett\UmlWriter\Processor\ProcessorInterface;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Symfony Console Application.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  bartlett/php-reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * @link http://patorjk.com/software/taag/#p=display&f=Standard&t=phpReflect
     */
    protected static $logo = "        _           ____       __ _           _
  _ __ | |__  _ __ |  _ \ ___ / _| | ___  ___| |_
 | '_ \| '_ \| '_ \| |_) / _ \ |_| |/ _ \/ __| __|
 | |_) | | | | |_) |  _ <  __/  _| |  __/ (__| |_
 | .__/|_| |_| .__/|_| \_\___|_| |_|\___|\___|\__|
 |_|         |_|

";

    /** @var string */
    private $baseDir;

    private $eventDispatcher;

    public function __construct(string $name = 'UNKNOWN')
    {
        try {
            $version = \Jean85\PrettyVersions::getVersion('bartlett/php-reflect')->getPrettyVersion();
        } catch (\OutOfBoundsException $e) {
            $version = 'UNKNOWN';
        }
        parent::__construct($name, $version);

        $this->baseDir = dirname(dirname(dirname(dirname(__DIR__))));
    }

    protected function getDefaultCommands() : array
    {
        $locator = new InMemoryLocator();
        $locator->addHandler(new AnalyserListHandler(), AppAnalyserListCommand::class);
        $locator->addHandler(new AnalyserRunHandler(), AppAnalyserRunCommand::class);
        $locator->addHandler(new DiagnoseHandler(), AppDiagnoseCommand::class);
        $locator->addHandler(new ConfigValidateHandler(), AppConfigValidateCommand::class);
        $locator->addHandler(new PluginListHandler(), AppPluginListCommand::class);
        $locator->addHandler(new ReflectionClassHandler(), AppReflectionClassCommand::class);
        $locator->addHandler(new ReflectionFunctionHandler(), AppReflectionFunctionCommand::class);

        if (interface_exists(ProcessorInterface::class)) {
            $locator->addHandler(new DiagramClassHandler(), AppDiagramClassCommand::class);
            $locator->addHandler(new DiagramPackageHandler(), AppDiagramPackageCommand::class);
        }

        $handlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            $locator,
            new InvokeInflector()
        );

        $commandBus = new CommandBus([$handlerMiddleware]);

        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new AnalyserListCommand($commandBus);
        $defaultCommands[] = new AnalyserRunCommand($commandBus);
        $defaultCommands[] = new DiagnoseCommand($commandBus);
        $defaultCommands[] = new ConfigValidateCommand($commandBus);
        $defaultCommands[] = new PluginListCommand($commandBus);
        $defaultCommands[] = new ReflectionClassCommand($commandBus);
        $defaultCommands[] = new ReflectionFunctionCommand($commandBus);

        if (interface_exists(ProcessorInterface::class)) {
            $defaultCommands[] = new DiagramClassCommand($commandBus);
            $defaultCommands[] = new DiagramPackageCommand($commandBus);
        }

        return $defaultCommands;
    }

    public function getHelp()
    {
        return '<comment>' . static::$logo . '</comment>' . parent::getHelp();
    }

    public function getVendorDir() : string
    {
        return $this->baseDir . DIRECTORY_SEPARATOR . 'vendor';
    }

    public function getBaseDir() : string
    {
        return $this->baseDir;
    }

    public function getBaseAnalyserDir() : string
    {
        return $this->baseDir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Reflect' . DIRECTORY_SEPARATOR . 'Analyser';
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $stopwatch = new Stopwatch();

        $dispatcher->addListener(
            ConsoleEvents::COMMAND,
            function (ConsoleCommandEvent $event) use ($stopwatch) {
                // Just before executing any command
                $stopwatch->start($event->getCommand()->getName());
            }
        );

        $dispatcher->addListener(
            ConsoleEvents::TERMINATE,
            function (ConsoleTerminateEvent $event) use ($stopwatch) {
                // Just after executing any command
                $command = $event->getCommand();

                $consoleEvent = $stopwatch->stop($command->getName());

                $input  = $event->getInput();
                $output = $event->getOutput();

                if (false === $input->hasParameterOption('--profile')) {
                    return;
                }

                $time   = $consoleEvent->getDuration();
                $memory = $consoleEvent->getMemory();

                $text = sprintf(
                    '%s<comment>Time: %s, Memory: %4.2fMb</comment>',
                    PHP_EOL,
                    $time, //Timer::toTimeString($time),
                    $memory / (1024 * 1024)
                );
                $output->writeln($text);
            }
        );

        $this->eventDispatcher = $dispatcher;
        parent::setDispatcher($dispatcher);
    }

    public function getDispatcher()
    {
        if (!$this->eventDispatcher) {
            //$this->setDispatcher(new CacheAwareEventDispatcher());
            $this->setDispatcher(new EventDispatcher());
        }
        return $this->eventDispatcher;
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $target = $input->getParameterOption('--output');
            if ($target === false) {
                $output = new ConsoleOutput();
            } else {
                $url  = parse_url($target);
                $mode = 'w';

                if (isset($url['scheme']) && $url['scheme'] == 'ftp') {
                    $options = array($url['scheme'] => array('overwrite' => true));
                    $context = stream_context_create($options);
                    $output  = new StreamOutput(fopen($target, $mode, false, $context), null, false);
                } else {
                    $output  = new StreamOutput(fopen($target, $mode), null, false);
                }
            }
            $output->getFormatter()->setStyle('diagpass', new OutputFormatterStyle('green', null, ['reverse']));
            $output->getFormatter()->setStyle('warning', new OutputFormatterStyle('black', 'yellow'));
            $output->getFormatter()->setStyle('debug', new OutputFormatterStyle('black', 'cyan'));
            $output->getFormatter()->setStyle('php', new OutputFormatterStyle('white', 'magenta'));
            $output->getFormatter()->setStyle('ext', new OutputFormatterStyle('white', 'blue'));
        }

        $this->getDispatcher();

        parent::run($input, $output);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (\Phar::running()
            && true === $input->hasParameterOption('--manifest')
        ) {
            $manifest = 'phar://' . strtolower($this->getName()) . '.phar/manifest.txt';

            if (file_exists($manifest)) {
                $out = file_get_contents($manifest);
                $exitCode = 0;
            } else {
                $fmt = $this->getHelperSet()->get('formatter');
                $out = $fmt->formatBlock('No manifest defined', 'error');
                $exitCode = 1;
            }
            $output->writeln($out);
            return $exitCode;
        }

        $exitCode = parent::doRun($input, $output);

        return $exitCode;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption(
                '--no-plugins',
                null,
                InputOption::VALUE_NONE,
                'Disables all plugins.'
            )
        );
        $definition->addOption(
            new InputOption(
                '--profile',
                null,
                InputOption::VALUE_NONE,
                'Display timing and memory usage information.'
            )
        );
        $definition->addOption(
            new InputOption(
                '--progress',
                null,
                InputOption::VALUE_NONE,
                'Show progress bar.'
            )
        );
        $definition->addOption(
            new InputOption(
                '--output',
                null,
                InputOption::VALUE_REQUIRED,
                'Write results to file or URL.'
            )
        );
        if (\Phar::running()) {
            $definition->addOption(
                new InputOption(
                    '--manifest',
                    null,
                    InputOption::VALUE_NONE,
                    'Show which versions of dependencies are bundled.'
                )
            );
        }
        return $definition;
    }
}
