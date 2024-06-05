<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

/**
 * Order packaging widget
 *
 */
class Packaging extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    public const PARAM_PACKAGES = 'packages';


    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/history/packaging.twig';
    }


    /**
     * Define widget parameters
     *
     * @return array
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PACKAGES => new \XLite\Model\WidgetParam\TypeCollection('Packages', []),
        ];
    }

    /**
     * Get array of packages
     *
     * @return array
     */
    protected function getPackages()
    {
        return $this->getParam(self::PARAM_PACKAGES);
    }
}
