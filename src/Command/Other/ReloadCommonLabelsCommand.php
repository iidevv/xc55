<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Other;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XCart\Command\Helpers;
use XLite\Core\WidgetCache;

class ReloadCommonLabelsCommand extends Command
{
    use Helpers\ModuleTrait;

    protected function configure()
    {
        $this
            ->setName('other:reloadCommonLabels')
            ->setDescription('Load new labels from RuTranslation/install.yaml and xlite_data_lng.yaml')
            ->setHelp('This command loads new labels from common labels yaml files.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Loading labels');
        $files = [
            LC_DIR_MODULES . 'CDev/RuTranslation/install.yaml',
            LC_DIR_ROOT . 'sql/xlite_data_lng.yaml',
        ];

        foreach ($files as $file) {
            $io->text('Loading ' . $file);
            \XLite\Core\Translation::getInstance()->loadLabelsFromYaml($file);
        }

        \XLite\Core\Database::getCacheDriver()->deleteAll();

        /** @var WidgetCache $widgetCache */
        $widgetCache = \XCart\Container::getContainer()->get(WidgetCache::class);
        $widgetCache->deleteAll();

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Database::getEM()->clear();

        return Command::SUCCESS;
    }
}
