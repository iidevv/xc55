<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Account\Customer;

/**
 * Widget displaying a section on the Reward Points tab in My Account.
 */
abstract class ARewardsWidget extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    public const PARAM_PROFILE = 'profile';

    /**
     * Get the currency for the sums displayed in the widget.
     *
     * @return mixed
     */
    public function getCurrency()
    {
        return \XLite::getInstance()->getCurrency();
    }

    /**
     * Define widget parameters.
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PROFILE => new \XLite\Model\WidgetParam\TypeObject(
                'Profile',
                null,
                false,
                '\XLite\Model\Profile'
            ),
        ];
    }

    /**
     * Get profile.
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getParam(static::PARAM_PROFILE);
    }
}
