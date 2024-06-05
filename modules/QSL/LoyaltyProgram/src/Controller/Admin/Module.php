<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    /**
     * Cached LoyaltyProgram module settings with Reviews options removed (if needed).
     *
     * @var array
     */
    protected $loyaltyProgramOptions;

    /**
     * Return current module options.
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->getModuleId() !== 'QSL-LoyaltyProgram') {
            $this->loyaltyProgramOptions = parent::getOptions();
        } else {
            $registry       = \Includes\Utils\Module\Manager::getRegistry();
            $reviewsEnabled = $registry->isModuleEnabled('XC', 'Reviews');
            $couponsEnabled = $registry->isModuleEnabled('CDev', 'Coupons');

            if (!isset($this->loyaltyProgramOptions)) {
                $this->loyaltyProgramOptions = [];
                foreach (parent::getOptions() as $option) {
                    $reviewSetting = (strpos($option->getName(), 'reward_points_reviews') === 0);
                    $couponSetting = ($option->getName() === 'reward_points_with_coupon');
                    if ((!$reviewSetting || $reviewsEnabled) && (!$couponSetting || $couponsEnabled)) {
                        $this->loyaltyProgramOptions[] = $option;
                    }
                }
            }
        }

        return $this->loyaltyProgramOptions;
    }
}
