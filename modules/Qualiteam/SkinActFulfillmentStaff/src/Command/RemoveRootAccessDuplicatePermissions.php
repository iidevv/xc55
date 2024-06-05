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

class RemoveRootAccessDuplicatePermissions extends \Symfony\Component\Console\Command\Command
{
    const CHUNK_LENGTH = 20;

    protected static $defaultName = 'SkinActFulfillmentStaff:RemoveRootAccessDuplicatePermissions';

    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;

        foreach (Database::getRepo('XLite\Model\Role\Permission')->findFrame($position, $length) as $permission) {

            if ($permission->getCode() === 'root access'
                && $permission->getId() !== 1
            ) {
                Database::getEM()->remove($permission);
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
                $output->writeln('Processed: ' . $i . ' permissions');
            }

        } while (0 < $processed);

        $output->writeln('Done');

        return Command::SUCCESS;
    }
}