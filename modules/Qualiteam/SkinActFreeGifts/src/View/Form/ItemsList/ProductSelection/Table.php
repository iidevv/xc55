<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\Form\ItemsList\ProductSelection;

/**
 * Product selections list table form
 */
class Table extends \XLite\View\Form\ItemsList\ProductSelection\Table
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'gift_tier_product_selections';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list[\Qualiteam\SkinActFreeGifts\View\ItemsList\Model\GiftTier::PARAM_GIFT_TIER_ID]
            = \XLite\Core\Request::getInstance()->gift_tier_id;

        return $list;
    }
}
