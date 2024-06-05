<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XLite\Logic\ImageResize\Generator;
use XLite\Core\Database;

final class ResizeImagesCommand extends Command
{
    protected static $defaultName = 'xcart:service:resize-images';

    protected function configure()
    {
        $this->setDescription('Resize images of the store business units (products, categories, etc.) into thumbnails per the frontend requirements. Options and arguments are not supported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Image resizing process started...');

        Generator::run(['allItems' => true]);
        if ($generator = Generator::getInstance()) {
            foreach ($generator->getSteps() as $step) {
                while ($step->valid()) {
                    $step->run();
                    $step->finalize();
                    $step->next();
                }
            }
        }

        Database::getRepo('XLite\Model\TmpVar')->removeEventState(Generator::getEventName());

        $output->writeln('All images resized');
        $output->writeln('<info>OK</info>');

        return Command::SUCCESS;
    }
}
