<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View;

use QSL\ProductFeeds\Core\EventListener\GenerateFeeds;

class ProductFeeds extends \XLite\View\AView
{
    /**
     * @var \XLite\Model\EventTask
     */
    protected $event;

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/ProductFeeds/product_feeds/listener.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductFeeds/product_feeds/style.css';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ProductFeeds/product_feeds/body.twig';
    }

    /**
     * @return bool
     */
    protected function isSearchVisible()
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function isFeedBeingGenerated()
    {
        return is_string($this->getGeneratorEvent());
    }

    /**
     * @return string
     */
    protected function getGeneratorEvent()
    {
        if (!isset($this->event)) {
            $tmpVars = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');

            $event = GenerateFeeds::EVENT_NAME;
            $state = $tmpVars->getEventState($event);

            $this->event = ($state && !$tmpVars->isFinishedEventState($event)) ? $event : false;
        }

        return $this->event;
    }
}
