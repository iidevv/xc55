<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XLite\Core\Database;

class RemoveEmptyRoles extends \Symfony\Component\Console\Command\Command
{
    const CHUNK_LENGTH = 50;

    protected static $defaultName = 'SkinActFulfillmentStaff:RemoveEmptyRoles';

    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;

        foreach (Database::getRepo('XLite\Model\Role')->findFrame($position, $length) as $role) {

            if (trim($role->getName()) === '') {
                Database::getEM()->remove($role);
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
                $output->writeln('Processed: ' . $i . ' roles');
            }

        } while (0 < $processed);

        $output->writeln('Done');

        return Command::SUCCESS;
    }
}