<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Controller\Admin;

use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Generator;
use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;
use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\TmpVar;

class GoogleProductRatingFeed extends \XLite\Controller\Admin\AAdmin
{
    use SkinActGoogleProductRatingFeedTrait;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActGoogleProductRatingFeed feeds');
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
            $this->buildURL(
                $this->getGoogleProductRatingFeedName()
            )
        );
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
     * Check - generation process is not-finished or not
     *
     * @return bool
     */
    public function isFeedGenerationNotFinished()
    {
        $eventName = Generator::getEventName();
        $state     = Database::getRepo(TmpVar::class)->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                [EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS],
                true
            )
            && !Database::getRepo(TmpVar::class)->getVar($this->getGenerationCancelFlagVarName());
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
     * Cancel
     */
    protected function doActionFeedGenerationCancel()
    {
        Generator::cancel();
        TopMessage::addWarning('SkinActGoogleProductRatingFeed feed generation has been stopped');

        $this->setReturnURL(
            $this->buildURL(
                $this->getGoogleProductRatingFeedName()
            )
        );
    }

    /**
     * Preprocessor for no-action run
     */
    protected function doNoAction()
    {
        $request = Request::getInstance();

        if ($request->generation_completed) {
            TopMessage::addInfo('SkinActGoogleProductRatingFeed feed generation has been completed successfully');

            $this->setReturnURL(
                $this->buildURL(
                    $this->getGoogleProductRatingFeedName()
                )
            );
        } elseif ($request->generation_failed) {
            TopMessage::addError('SkinActGoogleProductRatingFeed feed generation has been stopped');

            $this->setReturnURL(
                $this->buildURL(
                    $this->getGoogleProductRatingFeedName()
                )
            );
        }
    }
}
