<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Task;

use GuzzleHttp\Exception\GuzzleException;
use Qualiteam\SkinActAftership\Helpers\TrackingsHelper;
use Qualiteam\SkinActAftership\Utils\Slug;
use XLite\Core\Database;
use XLite\Model\OrderTrackingNumber;

/**
 * Class check tracking numbers to sync aftership
 */
class CheckTrackNumbersToSyncAftership extends \XLite\Core\Task\Base\Periodic
{
    public function getTitle()
    {
        return 'Check a track numbers to sync aftership';
    }

    /**
     * @throws GuzzleException
     */
    protected function runStep()
    {
        error_reporting(E_ALL & ~E_WARNING);

        $i = 0;

        do {

            $processed = $this->updateChunk($i);

            if (0 < $processed) {
                Database::getEM()->clear();
            }

            $i += $processed;

        } while (0 < $processed);
    }

    public function updateChunk($position = 0, $length = 20)
    {
        $processed = 0;

        /** @var OrderTrackingNumber $trackingNumber */
        foreach (Database::getRepo(OrderTrackingNumber::class)->getAllTrackingsForAftershipSync($position, $length) as $trackingNumber) {
            $aftershipResult = TrackingsHelper::addAftershipTracking(
                $trackingNumber->getValue(),
                Slug::getSlugByName($trackingNumber->getAftershipCourierName()),
            );

            if (TrackingsHelper::hasAftershipResult($aftershipResult)) {
                $trackingNumber->setAftershipSync(true);
            }

            sleep(1);

            $processed++;
        }

        if (0 < $processed) {
            Database::getEM()->flush();
        }

        return $processed;
    }

    protected function getPeriod()
    {
        return static::INT_10_MIN;
    }
}