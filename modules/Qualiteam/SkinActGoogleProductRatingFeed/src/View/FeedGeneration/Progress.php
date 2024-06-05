<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\FeedGeneration;

use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Generator;
use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;
use XLite\View\EventTaskProgressProviderTrait;

/**
 * Google Feed generation Progress
 */
class Progress extends \XLite\View\AView
{
    use EventTaskProgressProviderTrait;
    use SkinActGoogleProductRatingFeedTrait;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/admin/progress_controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/admin/progress.twig';
    }

    /**
     * Returns processing unit
     *
     * @return mixed
     */
    protected function getProcessor()
    {
        return Generator::getInstance();
    }
}
