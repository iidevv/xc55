<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Command;

use Qualiteam\SkinActAftership\Helpers\TrackingsHelper;
use Qualiteam\SkinActAftership\Utils\Slug;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XLite\Core\Database;
use XLite\Model\OrderTrackingNumber;

/**
 * Make aftership
 */
class MakeAftershipTrackingNumber extends Command
{
    const CHUNK_LENGTH = 20;

    protected static $defaultName = 'SkinActAftership:MakeAftershipTrackingNumber';

    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;

        /** @var OrderTrackingNumber $trackingNumber */
        foreach (Database::getRepo(OrderTrackingNumber::class)->findFrame($position, $length) as $trackingNumber) {

            if (
                !empty($trackingNumber->getAftershipCourierName())
                && !$trackingNumber->getAftershipSync()
                && !empty($trackingNumber->getValue())
            ) {
                $aftershipResult = TrackingsHelper::addAftershipTracking(
                    $trackingNumber->getValue(),
                    Slug::getSlugByName($trackingNumber->getAftershipCourierName()),
                );

                if (TrackingsHelper::hasAftershipResult($aftershipResult)) {
                    $trackingNumber->setAftershipSync(true);
                }

                sleep(1);
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

            $processed = $this->updateChunk($i);

            if (0 < $processed) {
                Database::getEM()->clear();
            }

            $i += $processed;

            if ($processed > 0) {
                $output->writeln('Processed: ' . $i . ' tracking number');
            }

        } while (0 < $processed);

        $output->writeln('Done');

        return Command::SUCCESS;
    }
}