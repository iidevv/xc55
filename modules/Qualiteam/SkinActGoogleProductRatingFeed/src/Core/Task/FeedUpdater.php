<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Core\Task;

use XLite\Core\Config;
use XLite\Core\Database;
use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Generator;
use XLite\Model\Task;
use XLite\Model\TmpVar;

/**
 * Periodic feed update
 */
class FeedUpdater extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActGoogleProductRatingFeed generate google product rating feed');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        if (!Database::getRepo(TmpVar::class)->getEventState(Generator::getEventName())) {
            Database::getRepo(TmpVar::class)->initializeEventState(
                Generator::getEventName(),
                ['options' => []]
            );
        }

        $generator = Generator::getInstance();
        $generator->setFeedUpdater($this);
        $generator->generate();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return static::getRenewalPeriod();
    }

    /**
     * Return renewal period
     *
     * @return int
     */
    public static function getRenewalPeriod()
    {
        $period = Config::getInstance()->Qualiteam->SkinActGoogleProductRatingFeed->google_rating_renewal_frequency;

        return in_array($period, static::getAllowedPeriods())
            ? $period
            : static::INT_1_DAY;
    }

    /**
     * Set renewal period
     *
     * @param int $period
     */
    public static function setRenewalPeriod($period)
    {
        $period = in_array($period, static::getAllowedPeriods())
            ? $period
            : static::INT_1_DAY;

        /** @var \XLite\Model\Task $task */
        $task = Database::getRepo(Task::class)->findOneBy(
            ['owner' => FeedUpdater::class]
        );

        if ($task && $task->getTriggerTime()) {
            $time = $task->getTriggerTime() - static::getRenewalPeriod();
            $task->setTriggerTime($time + $period);
        }
    }

    /**
     * @return array
     */
    public static function getAllowedPeriods()
    {
        return [
            static::INT_1_HOUR,
            static::INT_1_DAY,
            static::INT_1_WEEK,
        ];
    }

    /**
     * Merge task model entity
     */
    public function mergeModel()
    {
        if (isset($this->model) && $this->model instanceof \XLite\Model\AEntity) {
            $this->model = Database::getEM()->merge($this->model);
        }
    }
}
