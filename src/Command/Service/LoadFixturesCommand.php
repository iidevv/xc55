<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Exception;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XCart\Doctrine\FixtureLoader;

final class LoadFixturesCommand extends Command
{
    protected static $defaultName = 'xcart:service:load-fixtures';

    private FixtureLoader $fixtureLoader;

    public function __construct(
        FixtureLoader $fixtureLoader
    ) {
        parent::__construct();

        $this->fixtureLoader = $fixtureLoader;
    }

    protected function configure(): void
    {
        $help = <<< HELP
Uploads the X-Cart core and modules data. Usually, uploading is done automatically during the X-Cart installation and the following modules’ installations. However, sometimes manual data upload may be required. 

<info>Arguments:</info>
    <fg=red;bg=gray;options=bold>filelist</> - A comma-separated list of files (sql or yaml) for upload. Each file must be specified with a path to the X-Cart root.

<info>Options:</info>
    <fg=red;bg=gray;options=bold>--allow</>, <fg=red;bg=gray;options=bold>-al</>       - Specifies the models to upload from a file. If this option is not specified, all models from the file are uploaded, excluding the modules specified in the -exclude option. You can specify several times with different values in one command execution.
    <fg=red;bg=gray;options=bold>--exclude</>, <fg=red;bg=gray;options=bold>-ex</>     - Specifies the list of models not to be uploaded. Can't be used simultaneously with the -allow option. You can specify several times with different values in one command execution.
    <fg=red;bg=gray;options=bold>--params</>, <fg=red;bg=gray;options=bold>-p</>       - JSON, a set of key-value pairs in the yaml files. It is used for the administrator profile creation during the installation.

<info>For example:</info>
    <fg=red;bg=gray;options=bold>./bin/console xcart:service:load-fixtures --allow=XLite\Model\Config sql/xlite_data.yaml</>
    <fg=red;bg=gray;options=bold>./bin/console xcart:service:load-fixtures --allow=XLite\Model\Config --allow=XLite\Model\ImageSettings sql/xlite_data.yaml</>
    <fg=red;bg=gray;options=bold>./bin/console xcart:service:load-fixtures --exlude=XLite\Model\LanguageLabel src/modules/Amazon/PayWithAmazon/config/install.yaml</>
HELP;

        $this
            ->setDescription('Uploads the core and modules data.')
            ->setHelp($help)
            ->addArgument('filelist', InputArgument::REQUIRED, 'A comma-separated list of files (sql or yaml) for upload. Each file must be specified with a path to the X-Cart root.')
            ->addOption('allow', 'al', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Specifies the models to upload from a file. If this option is not specified, all models from the file are uploaded, excluding the modules specified in the -exclude option. You can specify several times with different values in one command execution.')
            ->addOption('exclude', 'ex', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Specifies the list of models not to be uploaded. Can\'t be used simultaneously with the -allow option. You can specify several times with different values in one command execution.')
            ->addOption('params', 'p', InputOption::VALUE_OPTIONAL, 'JSON, a set of key-value pairs in the yaml files. It is used for the administrator profile creation during the installation.', '{}');
    }

    /**
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $files = explode(',', $input->getArgument('filelist'));

        $allowed = $input->getOption('allow');
        $exclude = $input->getOption('exclude');

        $params = json_decode(
            $input->getOption('params'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $loadedFiles = [];

        try {
            foreach ($files as $file) {
                if ($this->checkExtension($file, 'yaml')) {
                    $this->fixtureLoader->loadYaml($file, $allowed, $exclude, $params);
                } elseif ($this->checkExtension($file, 'sql')) {
                    $this->fixtureLoader->loadSQL($file);
                }
                $loadedFiles[] = $file;
            }
        } catch (Exception $e) {
            $output->writeln('ERROR:' . $e->getMessage());

            foreach ($loadedFiles as $loadedFile) {
                $output->writeln($loadedFile);
            }
            // Even though the actual result here is FAILURE
            // we return SUCCESS in order to handle the error later
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

    private function checkExtension(string $path, string $extension): bool
    {
        return substr($path, -strlen($extension)) === $extension;
    }
}
