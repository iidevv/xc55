<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Core\EventListener;

use QSL\ProductFeeds\Model\ProductFeed;

/**
 * Generate product feeds for comparison shopping websites.
 */
class GenerateFeeds extends \XLite\Core\EventListener\AEventListener
{
    /**
     * Event name.
     */
    public const EVENT_NAME = 'generateFeeds';

    /**
     * Feed Generator object instances.
     *
     * @var array
     */
    protected $generators = [];

    /**
     * Event data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Handle the event.
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public function handleEvent($name, array $arguments)
    {
        parent::handleEvent($name, $arguments);

        $this->errors = [];

        $this->init();

        $feed = $this->getCurrentFeed();
        if ($feed) {
            $this->preprocessFeed($feed);
            $this->processFeed($feed);
            $this->postprocessFeed($feed);
            $feed->update();
        }

        if ($this->hasMoreSteps()) {
            $this->scheduleNextStep();
        } else {
            $this->finish();
        }

        return !($feed && $this->hasErrors());
    }

    /**
     * Add error messages.
     *
     * @param array $errors Error messages.
     *
     * @return void
     */
    public function addErrors($errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return static::EVENT_NAME;
    }

    /**
     * Initialize the event data.
     *
     * @return void
     */
    protected function init()
    {
        $this->loadEventState();

        $this->setEventState(\XLite\Core\EventTask::STATE_IN_PROGRESS);

        if (!$this->hasEventLength()) {
            $this->initEventLength();
        }

        $this->saveEventState();
    }

    /**
     * Returns the product feed that should be processed on this step.
     *
     * @return \QSL\ProductFeeds\Model\ProductFeed
     */
    protected function getCurrentFeed()
    {
        return \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\ProductFeed')
            ->findOneNotFinishedFeed();
    }

    /**
     * Make necessary operations before processing another chunk of data.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return void
     */
    protected function preprocessFeed(ProductFeed $feed)
    {
        if ($feed->getPosition() == 0) {
            $this->initFeed($feed);
        }
    }

    /**
     * Make necessary operations before processing the first chunk of data.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return void
     */
    protected function initFeed(ProductFeed $feed)
    {
        $feed->resetErrors();

        $generator = $this->getGenerator($feed);
        $generator->initFeed();
    }

    /**
     * Process a chunk of data.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return void
     */
    protected function processFeed(ProductFeed $feed)
    {
        $generator = $this->getGenerator($feed);
        $generator->processChunk();

        $count = $generator->countProcessedItems();
        if ($count) {
            $feed->movePosition($count);

            $total = $generator->countFeedItems();
            if ($total) {
                $feed->setProgress(min(100, $feed->getPosition() / $total));
            }

            $this->saveEventState();
        }
    }

    /**
     * Make necessary operations after processing a chunk of data.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return void
     */
    protected function postprocessFeed(ProductFeed $feed)
    {
        $generator = $this->getGenerator($feed);

        if ($generator->hasErrors()) {
            $errors = $generator->getErrors();
            $feed->addErrors($errors);
            $this->addErrors($errors);
            // Skip the feed in the queue
            $this->moveEventPosition($generator->countFeedItems() - $feed->getPosition());
        }

        if (!($generator->countProcessedItems() && $generator->hasMoreChunks())) {
            $this->finishFeed($feed);
        }
    }

    /**
     * Make necessary operations before finishing processing the feed.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return void
     */
    protected function finishFeed(ProductFeed $feed)
    {
        $now = \XLite\Core\Converter::time();
        $feed->setFinishedDate($now);

        if (!$feed->hasErrors()) {
            $generator = $this->getGenerator($feed);
            $generator->replaceFeedFileWithTemp();

            if ($generator->hasErrors()) {
                $errors = $generator->getErrors();
                $feed->addErrors($errors);
                $this->addErrors($errors);
            } else {
                $feed->setDate($now);
            }
        }
    }

    /**
     * Check whether there were errors when generated the feed file.
     *
     * @return boolean
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Get the generator object for a product feed.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return \QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror
     */
    protected function getGenerator(ProductFeed $feed)
    {
        $id = $feed->getId();

        if (!isset($this->generators[$id])) {
            $this->generators[$id] = $this->factoryGenerator($feed);
        }

        return $this->generators[$id];
    }

    /**
     * Create the generator object for the specified product feed.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     *
     * @return \QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror
     */
    protected function factoryGenerator(ProductFeed $feed)
    {
        return $feed->getGenerator();
    }

    /**
     * Check whether there are more data to export.
     *
     * @return boolean
     */
    protected function hasMoreSteps()
    {
        return is_object($this->getCurrentFeed());
    }

    /**
     * Schedule the next step.
     *
     * @return void
     */
    protected function scheduleNextStep()
    {
        $this->setEventState(\XLite\Core\EventTask::STATE_STANDBY);
        $this->saveEventState();

        \XLite\Core\EventTask::generateFeeds($this->arguments);
    }

    /**
     * Finish the even.
     *
     * @return void
     */
    protected function finish()
    {
        $this->setEventState(
            $this->hasErrors() ? \XLite\Core\EventTask::STATE_ABORTED : \XLite\Core\EventTask::STATE_FINISHED
        );

        $this->saveEventState();
    }

    /**
     * Calculate the total number of items to process.
     *
     * @return void
     */
    protected function initEventLength()
    {
        $count = 0;

        $feeds = \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\ProductFeed')
            ->findNotFinishedFeeds();

        foreach ($feeds as $feed) {
            $count += $this->getGenerator($feed)->countFeedItems();
        }

        $this->data['length'] = $count;
    }

    /**
     * Check whether the total number of items to process is set.
     *
     * @return boolean
     */
    protected function hasEventLength()
    {
        return ($this->data['length'] > 0);
    }

    /**
     * Set the total number of items to be processed.
     *
     * @param integer $length Number of items to be processed.
     *
     * @return void
     */
    protected function setEventLength($length)
    {
        $this->data['length'] = (int) $length;
    }

    /**
     * Load event data.
     *
     * @return void
     */
    protected function loadEventState()
    {
        $this->data = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState(self::EVENT_NAME);
    }

    /**
     * Save event data.
     *
     * @return void
     */
    protected function saveEventState()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setEventState(self::EVENT_NAME, $this->data);
    }

    /**
     * Set the event state.
     *
     * @param integer $code Event state code.
     *
     * @return void
     */
    protected function setEventState($code)
    {
        $this->data['state'] = $code;
    }

    /**
     * Move the event position by the number of processed items.
     *
     * @param integer $count Number of items to move the position for.
     *
     * @return void
     */
    protected function moveEventPosition($count)
    {
        $this->data['position'] += (int) $count;
    }
}
