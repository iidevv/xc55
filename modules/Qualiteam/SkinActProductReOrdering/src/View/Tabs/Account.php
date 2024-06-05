<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 're_order';

        return $list;
    }
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        $tabs['re_order'] = [
            'title'    => static::t('SkinActProductReOrdering re-order'),
            'template' => 'modules/Qualiteam/SkinActProductReOrdering/page/reorder.twig',
            'weight'   => 450,
        ];

        return $tabs;
    }
}