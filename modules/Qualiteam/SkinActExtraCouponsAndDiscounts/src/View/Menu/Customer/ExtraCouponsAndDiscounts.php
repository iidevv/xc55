<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\Menu\Customer;

use Qualiteam\SkinActProMembership\Helpers\Profile;
use XCart\Extender\Mapping\ListChild;

/**
 * Extra coupons menu item
 *
 * @ListChild (list="layout.header.bar.links.logged", weight="496", zone="customer")
 */
class ExtraCouponsAndDiscounts extends \XLite\View\AView
{
    public const PARAM_CAPTION = 'caption';

    protected function getCaption()
    {
        return $this->getParam(static::PARAM_CAPTION);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/layout/header/extra_coupons_and_discounts.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CAPTION => new \XLite\Model\WidgetParam\TypeString('Link caption', $this->getDefaultCaption()),
        ];
    }

    protected function getDefaultCaption()
    {
        return static::t('SkinActExtraCouponsAndDiscounts extra coupons and discounts');
    }

    protected function getExtraCouponsAndDiscountsUrl()
    {
        return $this->buildURL('extra_coupons_and_discounts');
    }

    protected function isVisible()
    {
        return parent::isVisible() && (new Profile)->isProfileProMembership();
    }
}