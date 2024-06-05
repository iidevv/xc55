<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\ItemsList\Model;

use XCart\Domain\ModuleManagerDomain;
use XLite\Model\Repo\Cart as Repo;
use QSL\AbandonedCartReminder\View\SearchPanel\Admin\AbandonedCart as SearchPanel;

class AbandonedCart extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @var bool
     */
    protected $hightlightStep = true;

    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * Get a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/AbandonedCartReminder/abandoned_carts/style.css';

        return $list;
    }

    /**
     * Get container class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' abandoned_carts';
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return bool
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'abandoned_carts';
    }

    /**
     * Return wrapper form options
     *
     * @return array
     */
    protected function getFormOptions()
    {
        $options           = parent::getFormOptions();
        $options['action'] = 'remind';

        return $options;
    }

    /**
     * Define columns structure.
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'profile'  => [
                static::COLUMN_NAME     => static::t('Email'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/abandoned_cart/cell.profile.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => false,
                static::COLUMN_ORDERBY  => 100,
            ],
            'items'    => [
                static::COLUMN_NAME     => static::t('Products'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/abandoned_cart/cell.items.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 200,
            ],
            'subtotal' => [
                static::COLUMN_NAME     => static::t('Subtotal'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/abandoned_cart/cell.subtotal.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
        ];

        // Add an extra column if Coupons module is enabled
        if ($this->moduleManagerDomain->isEnabled('CDev-Coupons')) {
            $columns += [
                'coupons' => [
                    static::COLUMN_NAME     => static::t('Coupons'),
                    static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/abandoned_cart/cell.coupons.twig',
                    static::COLUMN_ORDERBY  => 350,
                ],
            ];
        }

        $columns += [
            'lastVisitDate' => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/abandoned_cart/cell.date.twig',
                static::COLUMN_ORDERBY  => 400,
            ],
            'notified'      => [
                static::COLUMN_NAME     => static::t('Reminder sent'),
                static::COLUMN_TEMPLATE => 'modules/QSL/AbandonedCartReminder/items_list/model/table/abandoned_cart/cell.notified.twig',
                static::COLUMN_ORDERBY  => 500,
            ],
        ];

        return $columns;
    }

    /**
     * Define repository name.
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Cart';
    }

    /**
     * Mark list as removable.
     *
     * @return bool
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Allow items to be selected.
     *
     * @return bool
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Get panel class.
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\StickyPanel\ItemsList\AbandonedCart';
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\SearchPanel\Admin\AbandonedCart';
    }

    /**
     * Default search conditions
     *
     * @param \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        return $this->getRepository()->addConditionSearchAbandoned(parent::postprocessSearchCase($searchCase));
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        // Extra options to pass to the form as hidden fields
        return [
        ];
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            Repo::SEARCH_SUBSTRING             => SearchPanel::PARAM_SUBSTRING,
            Repo::SEARCH_LAST_VISIT_DATE_RANGE => SearchPanel::PARAM_DATE_RANGE,
        ];
    }

    // {{{ Template methods }}}

    /**
     * Check whether a line item has multiple ordered units.
     *
     * @param \XLite\Model\OrderItem $item Order line item
     *
     * @return bool
     */
    protected function hasMultipleUnits(\XLite\Model\OrderItem $item)
    {
        return $item->getAmount() > 1;
    }

    /**
     * Check whether coupons were created for the abandoned cart.
     *
     * @param \XLite\Model\Cart $cart Cart
     *
     * @return bool
     */
    protected function hasCoupons(\XLite\Model\Cart $cart)
    {
        return $this->moduleManagerDomain->isEnabled('CDev-Coupons')
            && (0 < count($cart->getCreatedCoupons()));
    }

    /**
     * Get URL to the coupon page.
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return string
     */
    protected function getCouponURL(\CDev\Coupons\Model\Coupon $coupon)
    {
        return $this->moduleManagerDomain->isEnabled('CDev-Coupons')
            ? \XLite\Core\Converter::buildUrl('coupon', '', ['id' => $coupon->getId()])
            : false;
    }

    /**
     * Return CSS class for the coupon.
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return string
     */
    protected function getCouponClass(\CDev\Coupons\Model\Coupon $coupon)
    {
        return $coupon->isExpired() ? 'expired-coupon' : '';
    }

    /**
     * Return tooltip tile for the coupon.
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return string
     */
    protected function getCouponTitle(\CDev\Coupons\Model\Coupon $coupon)
    {
        return $coupon->isExpired() ? static::t('This coupon is expired') : static::t('Click to edit the coupon');
    }

    /**
     * Return user's name.
     *
     * @param \XLite\Model\Cart $entity Abandoned cart
     *
     * @return string
     */
    protected function getName($entity)
    {
        $profile = $entity->getProfile();

        return ($profile && ($profile->name !== static::t('n/a')))
            ? $profile->name
            : '';
    }
}
