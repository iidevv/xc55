<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\View\Menu\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Orders list menu item
 *
 * @ListChild (list="layout.header.bar.links.logged", weight="310", zone="customer")
 * @ListChild (list="slidebar.navbar.account", weight="11", zone="customer")
 */
class OrdersList extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_CAPTION = 'caption';

    /**
     * @return string
     */
    protected function getCaption()
    {
        return $this->getParam(static::PARAM_CAPTION);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CAPTION => new \XLite\Model\WidgetParam\TypeString('Link caption', $this->getDefaultCaption()),
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultCaption()
    {
        return static::t('SkinActChangesToTrackingNumbers parcels');
    }

    /**
     * @return string
     */
    protected function getOrdersListUrl()
    {
        return $this->buildURL('parcels');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/orders.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isLogged();
    }
}
