<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XC\GoogleFeed\Logic\Feed\Generator;

/**
 * Google feed generation & settings
 */
class GoogleFeed extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Feeds');
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return bool
     */
    public function isFeedGenerationNotFinished()
    {
        $eventName = Generator::getEventName();
        $state     = Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                [EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS],
                true
            )
            && !Database::getRepo('XLite\Model\TmpVar')->getVar($this->getGenerationCancelFlagVarName());
    }

    /**
     * Check - generation process is finished or not
     *
     * @return bool
     */
    public function isGenerationFinished()
    {
        return !$this->isFeedGenerationNotFinished();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getGenerationCancelFlagVarName()
    {
        return Generator::getCancelFlagVarName();
    }

    /**
     * Manually generate sitemap
     */
    protected function doActionGenerate()
    {
        if ($this->isGenerationFinished()) {
            Generator::run([]);
        }

        $this->setReturnURL(
            $this->buildURL('google_feed')
        );
    }

    /**
     * Update module settings
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');

        \XC\GoogleFeed\Core\Task\FeedUpdater::setRenewalPeriod(
            \XLite\Core\Config::getInstance()->XC->GoogleFeed->renewal_frequency
        );
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    /**
     * Cancel
     */
    protected function doActionFeedGenerationCancel()
    {
        Generator::cancel();
        TopMessage::addWarning('Feed generation has been stopped.');

        $this->setReturnURL(
            $this->buildURL('google_feed')
        );
    }

    /**
     * Preprocessor for no-action run
     */
    protected function doNoAction()
    {
        $request = Request::getInstance();

        if ($request->generation_completed) {
            TopMessage::addInfo('Feed generation has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('google_feed')
            );
        } elseif ($request->generation_failed) {
            TopMessage::addError('Feed generation has been stopped.');

            $this->setReturnURL(
                $this->buildURL('google_feed')
            );
        }
    }

    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return Database::getRepo('XLite\Model\Config')
                ->findByCategoryAndVisible($this->getOptionsCategory());
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'XC\GoogleFeed';
    }
}
