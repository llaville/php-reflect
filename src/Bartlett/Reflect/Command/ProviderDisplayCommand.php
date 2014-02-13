<?php

namespace Bartlett\Reflect\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

use Bartlett\Reflect\Tokenizer\DefaultTokenizer;

class ProviderDisplayCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('provider:display')
            ->setDescription('Show source of a file in a data source.')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Relative path to a file in the data source'
            )
            ->addOption(
                'alias',
                null,
                InputOption::VALUE_NONE,
                'If set, the source refers to its alias'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = trim($input->getArgument('source'));
        if ($input->getOption('alias')) {
            $alias = $source;
        } else {
            $alias = false;
        }

        $var = $this->getApplication()->getJsonConfigFile();

        if (!is_array($var)
            || !isset($var['source-providers'])
        ) {
            throw new \Exception(
                'The json configuration file has an invalid format'
            );
        }

        if (is_array($var['source-providers'])) {
            $providers = $var['source-providers'];
        } else {
            $providers = array($var['source-providers']);
        }

        foreach ($providers as $provider) {
            if ($this->findProvider($provider, $source, $alias) === false) {
                continue;
            }

            $rows   = array();
            $rows[] = array('<info>Source</info>', '', '');
            $rows[] = array($this->source[0], '', '');
            $rows[] = array('<info>Relative Path Name</info>', '<info>Date</info>', '<info>Size</info>');

            foreach ($this->finder as $file) {
                if ($file->getRelativePathname() == $input->getArgument('file')) {
                    $rows[] = array(
                        $file->getRelativePathname(),
                        date(\DateTime::W3C, $file->getMTime()),
                        sprintf('%7d', $file->getSize())
                    );
                    break;
                }
            }

            $table = $this->getApplication()
                ->getHelperSet()
                ->get('table')
                ->setLayout(TableHelper::LAYOUT_COMPACT)
            ;

            $table->setRows($rows)
                ->render($output)
            ;

            $tokenizer = new DefaultTokenizer;
            $tokenizer->setSourceFile($file);

            $rows = array();

            foreach ($tokenizer->getTokens() as $token) {
                if ($token[0] == 'T_WHITESPACE') {
                    $text = '';
                } else {
                    $text = str_replace(array("\r", "\n"), '', $token[1]);

                    if (strlen($text) > 40) {
                        $text = explode("\n", wordwrap($text, 40));
                        $text = $text[0];
                    }
                }
                $rows[] = array(
                    sprintf('%5d', $token[3]),
                    sprintf('%-30s', $token[0]),
                    sprintf('%5d', $token[2]),
                    sprintf('%s', $text)
                );
            }

            $table->setHeaders(array('Id', 'Token', 'Line', 'Text'))
                ->setRows($rows)
                ->render($output)
            ;
            return;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
