<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\Menu\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Re-order menu item
 *
 * @ListChild (list="layout.header.bar.links.logged", weight="400", zone="customer")
 */
class ReOrder extends \XLite\View\AView
{
    public const PARAM_CAPTION = 'caption';
    protected function getCaption()
    {
        return $this->getParam(static::PARAM_CAPTION);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProductReOrdering/layout/header/reorder.twig';
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
        return static::t('SkinActProductReOrdering re-order');
    }

    protected function getReOrderUrl()
    {
        return $this->buildURL('re_order');
    }
}