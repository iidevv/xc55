<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Abandoned Carts center widget.
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Overview extends \XLite\View\AView
{
    /**
     * Cached ID of the Loaylty Program module.
     *
     * @var integer
     */
    protected $moduleId;

    /**
     * Cached redeem statistics (total redeemed points).
     *
     * @var integer
     */
    protected $totalRedeemedPoints;

    /**
     * Cached points statistics (number of customers with points).
     *
     * @var integer
     */
    protected $customerWithPoints;

    /**
     * Cached points statistics (number of unused points).
     *
     * @var integer
     */
    protected $unusedPoints;

    /**
     * Cached redeem statistics (total redeem discount).
     *
     * @var float
     */
    protected $totalRedeemDiscount;

    /**
     * Whether the maximum allowed discount is a percent discount ('%'), or a fixed one ('').
     *
     * @var string
     */
    protected $maxDiscountType;

    /**
     * The maximum allowed discount amount.
     *
     * @var float
     */
    protected $maxDiscount;

    /**
     * Return list of allowed targets.
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['loyalty_program']);
    }

    public static function formatPriceLP($price, $currency = null, $strictFormat = false)
    {
        return \QSL\LoyaltyProgram\Core\Price::longFormat($price, $currency, $strictFormat);
    }

    /**
     * Attempts to display widget using its template
     *
     * @param string $template Template file name OPTIONAL
     */
    public function display($template = null)
    {
        $this->initData();

        parent::display($template);
    }

    /**
     * Build URL to the Loyalty Program module settings page.
     *
     * @return string
     */
    public function buildSettingsUrl()
    {
        return $this->buildUrl('module', '', ['moduleId' => 'QSL-LoyaltyProgram']);
    }

    /**
     * Get the total number of reward points redeemed in the store.
     *
     * @return integer
     */
    public function getTotalRedeemedPoints()
    {
        return $this->totalRedeemedPoints;
    }

    /**
     * Get the total discount given to customers in the store for redeeming their reward points.
     *
     * @return float
     */
    public function getTotalRedeemDiscount()
    {
        return $this->totalRedeemDiscount;
    }

    /**
     * Get the number of customers with unused reward points.
     *
     * @return integer
     */
    public function getNumberOfCustomers()
    {
        return $this->customerWithPoints;
    }

    /**
     * Get the total number of unused reward points.
     *
     * @return integer
     */
    public function getUnusedPoints()
    {
        return $this->unusedPoints;
    }

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
     * Get the number of $ to be spent to earn 1 reward point.
     *
     * @return float
     */
    public function getEarnRate()
    {
        $rate = \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance()->getEarnRate();

        return $rate ? (1 / $rate) : 0;
    }

    /**
     * Return the discount a customer will get for 1 redeemed reward point.
     *
     * @return float
     */
    public function getRedeemRate()
    {
        return \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance()->getRedeemRate();
    }

    /**
     * Return the discount a customer will get for 1 redeemed reward point.
     *
     * @return float
     */
    public function getMaxPossibleDiscount()
    {
        return \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance()->getRedeemRate();
    }

    /**
     * Get the maximum allowed discount.
     *
     * @return string
     */
    public function getMaxAllowedDiscount()
    {
        return ($this->maxDiscountType === '%')
            ? (preg_replace('/[^\d]0+$/', '', round($this->maxDiscount, 2)) . '%')
            : $this->formatPrice($this->maxDiscount, $this->getCurrency());
    }

    /**
     * Get a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/overview/styles.css';

        return $list;
    }

    /**
     * Maximum discount which a shopper can get for an order by redeeming his reward points.
     *
     * @return string
     */
    public function getRewardPointsRedeemCap()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_redeem_cap === ''
            ? '100%'
            : \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_redeem_cap;
    }

    /**
     * Return default template.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/dialog.twig';
    }

    /**
     * Return the path to the template with the dialog contents.
     *
     * @return string
     */
    protected function getBody()
    {
        return 'modules/QSL/LoyaltyProgram/overview/body.twig';
    }

    /**
     * Initialize the widget.
     */
    protected function initData()
    {
        [$this->totalRedeemedPoints, $this->totalRedeemDiscount]
            = \XLite\Core\Database::getRepo('XLite\Model\Order')->getRedeemStatistics();

        [$this->customerWithPoints, $this->unusedPoints]
            = \XLite\Core\Database::getRepo('XLite\Model\Profile')->getRewardPointsStatistics();

        // Init the information on the maximum discount
        $rate = $this->getRewardPointsRedeemCap();

        if (preg_match('/^(\d+\.?\d*) *(%?)$/', trim($rate), $matches)) {
            $this->maxDiscountType = $matches[2];
            $this->maxDiscount     = floatval($matches[1]);
        } else {
            $this->maxDiscountType = '';
            $this->maxDiscount     = 0;
        }
    }
}
