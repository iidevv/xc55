<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\ProductFeedGeneration;

class Progress extends \XLite\View\AView
{
    use \XLite\View\EventTaskProgressProviderTrait;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/FacebookMarketing/product_feed_generation/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FacebookMarketing/product_feed_generation/progress.twig';
    }

    /**
     * Returns processing unit
     *
     * @return mixed
     */
    protected function getProcessor()
    {
        return \XC\FacebookMarketing\Logic\ProductFeed\Generator::getInstance();
    }
}
