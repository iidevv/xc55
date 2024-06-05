<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCouponSearchBar\View\SearchPanel;


class Coupons extends \XLite\View\SearchPanel\ASearchPanel
{
    public const PARAM_SUBSTRING = 'substr';

    protected function defineConditions()
    {
        return parent::defineConditions() + [
                static::PARAM_SUBSTRING => [
                    static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                    \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
                    \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Search keywords'),
                ],

            ];
    }

    protected function getFormClass()
    {
        return '\XLite\View\Form\ItemsList\AItemsListSearch';
    }


}