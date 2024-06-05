<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\Form\ItemsList\GiftTier;

/**
 * Gift Tier list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'gift_tier';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'updateItemsList';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        $list['itemsList'] = '\Qualiteam\SkinActFreeGifts\View\ItemsList\Model\GiftTier';
        $list[\Qualiteam\SkinActFreeGifts\View\ItemsList\Model\GiftTier::PARAM_GIFT_TIER_ID]
            = \XLite\Core\Request::getInstance()->gift_tier_id;

        return $list;
    }
}
