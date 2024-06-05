<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SurchargeInfo;

/**
 * ASurchargeInfo
 */
abstract class ASurchargeInfo extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    public const PARAM_SURCHARGE = 'surcharge';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SURCHARGE => new \XLite\Model\WidgetParam\TypeObject('Surcharge', null),
        ];
    }

    /**
     * Get surcharge object
     *
     * @return \XLite\Model\Base\Surcharge
     */
    protected function getSurcharge()
    {
        $surchargeParam = $this->getParam(static::PARAM_SURCHARGE);

        return $surchargeParam['object'] ?? null;
    }
}
