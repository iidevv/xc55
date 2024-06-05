<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Form\ItemsList\ProductFeed;

/**
 * Form class for the list of comparison shopping sites.
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product_feeds';
    }

    /**
     * Return default value for the "action" parameter.
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'generate';
    }
}
