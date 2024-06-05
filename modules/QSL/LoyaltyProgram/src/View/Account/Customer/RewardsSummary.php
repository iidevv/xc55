<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Account\Customer;

use XCart\Extender\Mapping\ListChild;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * Widget displaying the summary on rewards earned by a customer.
 *
 * @ListChild (list="customer.account.rewards", weight="10")
 */
class RewardsSummary extends \QSL\LoyaltyProgram\View\Account\Customer\ARewardsWidget
{
    public static function formatPriceLP($price, $currency = null, $strictFormat = false)
    {
        return \QSL\LoyaltyProgram\Core\Price::longFormat($price, $currency, $strictFormat);
    }

    /**
     * Return the number of reward points the shopper has.
     *
     * @return integer
     */
    public function getRewardPoints()
    {
        return $this->getProfile()->getRewardPoints();
    }

    /**
     * Check whether the customer has the positive points balance.
     *
     * @return boolean
     */
    public function hasPositivePointsBalance()
    {
        return 0 < $this->getRewardPoints();
    }

    /**
     * Check whether the customer has the negative points balance.
     *
     * @return boolean
     */
    public function hasNegativePointsBalance()
    {
        return 0 > $this->getRewardPoints();
    }

    /**
     * Return the money equivalent for the number of reward points the shopper has.
     *
     * @return integer
     */
    public function getRewardPointsSum()
    {
        return LoyaltyProgram::getInstance()->calculateRedeemDiscount($this->getProfile()->getRewardPoints());
    }

    /**
     * Return the number of reward points for the "exchange rate" section.
     *
     * @return integer
     */
    public function getRatePoints()
    {
        return 1;
    }

    /**
     * Return the money equivalent for the "exchange rate" section.
     *
     * @return integer
     */
    public function getRatePointsSum()
    {
        return LoyaltyProgram::getInstance()->calculateRedeemDiscount($this->getRatePoints());
    }

    /**
     * Get a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/account/reward_points_summary.css';

        return $list;
    }

    /**
     * Return the default template for the widget.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/account/reward_points_summary.twig';
    }
}
