<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\Button;

/**
 * Product selection in popup
 */
class PopupProductSelector extends \XLite\View\Button\PopupProductSelector
{
    public const PARAM_GIFT_TIER_ID = 'giftTierId';

    /**
     * Defines the target of the product selector
     * The main reason is to get the title for the selector from the controller
     *
     * @return string
     */
    protected function getSelectorTarget()
    {
        return 'gift_tier_product_selections';
    }

    /**
     * Defines the class name of the widget which will display the product list dialog
     *
     * @return string
     */
    protected function getSelectorViewClass()
    {
        return 'Qualiteam\SkinActFreeGifts\View\ProductSelections';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array_merge(
            parent::prepareURLParams(),
            [
                'giftTierId' => $this->getParam(static::PARAM_GIFT_TIER_ID),
            ]
        );
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_GIFT_TIER_ID => new \XLite\Model\WidgetParam\TypeString('Gift tier id, if it is provided', ''),
        ];
    }
}
