<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\ListChild;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * Widget that renders the "Loyalty Program Details" page.
 *
 * @ListChild (list="center")
 */
class LoyaltyProgramPage extends \XLite\View\AView
{
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
     * Constructor.
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return \QSL\LoyaltyProgram\View\LoyaltyProgramPage
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->initData();
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'loyalty_program_details';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/loyalty_program_page/body.css';

        return $list;
    }

    /**
     * Get a SimpleCMS page linked to the "Loyalty Program Details" page.
     *
     * @return \CDev\SimpleCMS\Model\Page
     */
    public function getLinkedPage()
    {
        $id = \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_details_page;

        $page = $id ? \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->find($id) : null;

        return ($id && $page) ? $page : null;
    }

    /**
     * Get the contents of the linked SimpleCMS page.
     *
     * @return string
     */
    public function getLinkedPageBody()
    {
        $page = $this->getLinkedPage();

        return $page ? $page->getBody() : '';
    }

    /**
     * Return the money equivalent for the "exchange rate" section.
     *
     * @return integer
     */
    public function getPointsRate()
    {
        return 1 / LoyaltyProgram::getInstance()->getEarnRate();
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
     * Check whether there is limit set on the maximum discount for redeemed points.
     *
     * @return boolean
     */
    public function discountLimitEnabled()
    {
        return ($this->maxDiscountType !== '%') || (round($this->maxDiscount, 2) < 100);
    }

    /**
     * Get the maximum discount for redeeming points.
     *
     * @return string
     */
    public function getMaxDiscount()
    {
        return ($this->maxDiscountType === '%')
            ? (preg_replace('/[^\d]0+$/', '', round($this->maxDiscount, 2)) . '%')
            : $this->formatPrice($this->maxDiscount, $this->getCurrency());
    }

    /**
     * Initialize the widget.
     */
    protected function initData()
    {
        // Init the information on the maximum discount

        $rate = \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_redeem_cap;

        if (preg_match('/^(\d+\.?\d*) *(%?)$/', trim($rate), $matches)) {
            $this->maxDiscountType = $matches[2];
            $this->maxDiscount     = floatval($matches[1]);
        } else {
            $this->maxDiscountType = '';
            $this->maxDiscount     = 0;
        }
    }

    /**
     * Return the default template for the widget.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/loyalty_program_page/body.twig';
    }

    /**
     * Check if the Join Loyalty Program section is visible, or not.
     *
     * @return boolean
     */
    protected function isJoinSectionVisible()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        return (!$profile || !$profile->isLoyaltyProgramEnabled());
    }

    /**
     * Returns CSS classes for the main section on the page.
     *
     * @return string
     */
    protected function getMainSectionClasses()
    {
        return 'loyalty-program-details'
            . ($this->isJoinSectionVisible() ? ' with-join-section' : '');
    }
}
