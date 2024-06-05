<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XLite\Core\Database;


class MakeCustomersVerifiedCommand extends Command
{
    const CHUNK_LENGTH = 20;

    protected static $defaultName = 'SkinActVerifiedCustomer:MakeCustomersVerified';

    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;

        foreach (Database::getRepo('XLite\Model\Order')->findFrame($position, $length) as $order) {

            if ($order->getOrigProfile()) {
                $order->getOrigProfile()->makeVerified();
            }

            $processed++;
        }

        if (0 < $processed) {
            Database::getEM()->flush();
        }

        return $processed;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $i = 0;

        do {

            $processed = $this->updateChunk($i, static::CHUNK_LENGTH);

            if (0 < $processed) {
                Database::getEM()->clear();
            }

            $i += $processed;

            if ($processed > 0) {
                $output->writeln('Processed: ' . $i . ' orders');
            }

        } while (0 < $processed);

        $output->writeln('Done');

        return Command::SUCCESS;
    }

}