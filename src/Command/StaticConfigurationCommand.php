<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XCart\Domain\StaticConfigDomain;
use XCart\Kernel;

final class StaticConfigurationCommand extends Command
{
    protected static $defaultName = 'xcart:static-configuration:dump';

    private Kernel $kernel;

    private StaticConfigDomain $staticConfigDomain;

    public function __construct(Kernel $kernel, StaticConfigDomain $staticConfigDomain)
    {
        parent::__construct(static::$defaultName);

        $this->kernel             = $kernel;
        $this->staticConfigDomain = $staticConfigDomain;
    }

    protected function configure(): void
    {
        $help = <<< HELP
Prints a static configuration dump. The configuration is divided into ENV variables and the X-Cart configuration bundle. Default values and current values are printed in separate columns. 

<info>Options:</info>
    <fg=red;bg=gray;options=bold>--changed-only</>, <fg=red;bg=gray;options=bold>-c</>           - Print variables with updated values.
    <fg=red;bg=gray;options=bold>--conf-vars</> | <fg=red;bg=gray;options=bold>--no-conf-vars</> - Print (default value) or don't print configuration variables.
    <fg=red;bg=gray;options=bold>--env-vars</> | <fg=red;bg=gray;options=bold>--no-env-vars</>   - Print (default value) or don't print environment variables.
HELP;

        $this
            ->setDescription('Prints a static configuration dump.')
            ->setHelp($help)
            ->addOption('changed-only', 'c', InputOption::VALUE_NONE, 'Print variables with updated values.')
            ->addOption('conf-vars', 'cv', InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE, 'Print (default value) or don\'t print configuration variables.')
            ->addOption('env-vars', 'ev', InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE, 'Print (default value) or don\'t print environment variables.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('conf-vars') !== false) {
            $this->showConfigurationVars($input, $output);
        }

        if ($input->getOption('env-vars') !== false) {
            $this->showEnvironmentVars($input, $output);
        }

        return Command::SUCCESS;
    }

    private function showConfigurationVars(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Configuration variables (defined in yaml files):');

        $data = [];

        $original = $this->staticConfigDomain->getOriginal();
        foreach ($this->staticConfigDomain->getConfig() as $section => $sectionData) {
            foreach ($sectionData as $variable => $value) {
                $data[$section . $variable] = [
                    'section'  => $section,
                    'variable' => $variable,
                    'value'    => $value,
                    'original' => $original[$section][$variable] ?? null,
                ];
            }
        }

        usort($data, static fn($a, $b) => $a['section'] <=> $b['section'] ?: $a['variable'] <=> $b['variable']);

        $table = new Table($output);

        $table->setHeaders(['Variable', 'Value', 'Original']);
        $table->setColumnMaxWidth(1, 60);
        $table->setColumnMaxWidth(2, 60);

        $section = null;

        $sectionStyle = new TableCellStyle(['fg' => 'green']);
        $changedStyle = new TableCellStyle(['fg' => 'yellow']);

        foreach ($data as $datum) {
            $value    = $this->convertToString($datum['value']);
            $original = $this->convertToString($datum['original']);

            $isChanged = $value != $original;

            if (!$isChanged && $input->getOption('changed-only')) {
                continue;
            }

            if ($datum['section'] !== $section) {
                $table->addRow(new TableSeparator());
                $table->addRow([new TableCell($datum['section'], ['colspan' => 3, 'style' => $sectionStyle])]);
                $section = $datum['section'];
            }

            $table->addRow([
                new TableCell($datum['variable'], $isChanged ? ['style' => $changedStyle] : []),
                $value,
                $original,
            ]);
        }

        $table->render();
    }

    private function showEnvironmentVars(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Environment variables:');

        $container = $this->kernel->getContainer();
        $data      = (new \Symfony\Component\Dotenv\Dotenv())->parse(file_get_contents($container->getParameter('kernel.project_dir') . '/.env'));

        $table = new Table($output);

        $table->setHeaders(['Variable', 'Value', 'Original']);
        $table->setColumnMaxWidth(1, 60);
        $table->setColumnMaxWidth(2, 60);

        $changedStyle = new TableCellStyle(['fg' => 'yellow']);
        $oldStyle     = new TableCellStyle(['fg' => 'gray']);

        foreach ($data as $variable => $original) {
            $value = $_ENV[$variable] ?? '';

            $isChanged = $value !== $original;

            if (!$isChanged && $input->getOption('changed-only')) {
                continue;
            }

            $table->addRow([
                $variable,
                new TableCell($value, $isChanged ? ['style' => $changedStyle] : []),
                new TableCell($original, $isChanged ? ['style' => $oldStyle] : []),
            ]);
        }

        $table->render();
    }

    private function convertToString($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            if ($value && is_int(array_keys($value)[0])) {
                return implode(', ', $value);
            }

            $result = [];
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    $v = $this->convertToString($v);
                }

                $result[] = "$k => $v";
            }

            return implode("\n", $result);
        }

        return (string) $value;
    }
}
