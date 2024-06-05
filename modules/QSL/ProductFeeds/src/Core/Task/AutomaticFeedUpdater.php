<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Core\Task;

use QSL\ProductFeeds\Core\EventListener\GenerateFeeds;
use QSL\ProductFeeds\Model\ProductFeed;

/**
 * Scheduled task that initiates the automatic update for product feeds.
 *
 * This task just initiates the process like the "Generate feeds" back-end button does.
 * GenerateFeeds event listener is the class that generates feeds actually.
 */
class AutomaticFeedUpdater extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Return title for the task.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Automatic feed update';
    }

    /**
     * Return the delay (in seconds) between performing task steps.
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return 3600; // = 1 hour (equals to the minimum allowed delay between automatic feed updates)
    }

    /**
     * Run a task step.
     *
     * @return void
     */
    protected function runStep()
    {
        if ($this->isFeedGenerationStarted()) {
            return;
        }

        $count = 0;

        /** @var ProductFeed $feed */
        foreach ($this->getProductFeedsForUpdate() as $feed) {
            if (!$feed->isInProgress()) {
                $count++;
                $feed->queue();
            }
        }

        if ($count) {
            \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState(GenerateFeeds::EVENT_NAME);
            \XLite\Core\Database::getEM()->flush();

            while ($count) {
                GenerateFeeds::handle(GenerateFeeds::EVENT_NAME);
                $count--;
            }

            $task = \XLite\Core\Database::getRepo('XLite\Model\EventTask')
                ->findOneBy(['name' => GenerateFeeds::EVENT_NAME]);
            if ($task) {
                \XLite\Core\Database::getEM()->remove($task);
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    /**
     * Check whether the feed generation process is already going.
     *
     * @return boolean
     */
    protected function isFeedGenerationStarted()
    {
        return is_object(
            \XLite\Core\Database::getRepo('XLite\Model\EventTask')->findOneByName(
                \QSL\ProductFeeds\Core\EventListener\GenerateFeeds::EVENT_NAME
            )
        );
    }

    /**
     * Get the list of feeds which are ready to be refreshed automatically.
     *
     * @return array
     */
    protected function getProductFeedsForUpdate()
    {
        $feeds = [];

        foreach ($this->getRepository()->findFinishedFeeds() as $feed) {
            $minDelay = (int) $feed->getGenerator()->getAutoRefreshDelay();
            if ($minDelay && (time() > $feed->getFinishedDate() + $minDelay * 3600)) {
                $feeds[] = $feed;
            }
        }

        return $feeds;
    }

    /**
     * Get repository for the Product Feed model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\ProductFeed');
    }
}
