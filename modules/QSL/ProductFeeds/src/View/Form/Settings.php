<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Form;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Settings form widget.
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Form\Settings
{
    /**
     * Required form parameters.
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        if (\XLite\Core\Request::getInstance()->target === 'product_feed') {
            $list['feed_id'] = \XLite\Core\Request::getInstance()->feed_id;
        }

        return $list;
    }
}
