<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\Menu\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Your Account menu item
 *
 * @ListChild (list="layout.header.bar.links.logged", weight="-300", zone="customer")
 * @ListChild (list="slidebar.navbar.account", weight="10", zone="customer")
 */
class YourAccountLink extends \XLite\View\AView
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
        return static::t('SkinActYourAccountPage your account');
    }

    /**
     * @return string
     */
    protected function getYourAccountUrl()
    {
        return $this->buildURL('your_account');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/your_account_link.twig';
    }
}