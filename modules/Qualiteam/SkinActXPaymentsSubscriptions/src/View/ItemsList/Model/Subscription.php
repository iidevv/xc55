<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model;

use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription as SubscriptionModel;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Inline\Select\SubscriptionStatus;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Address;
use XLite\Model\AEntity;
use XLite\Model\Profile;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\ItemsList\Model\Table;

/**
 * Subscriptions items list
 */
class Subscription extends Table
{
    /**
     * Widget param names
     */
    const PARAM_ID              = 'id';
    const PARAM_PRODUCT_NAME    = 'productName';
    const PARAM_STATUS          = 'status';
    const PARAM_DATE_RANGE      = 'dateRange';
    const PARAM_NEXT_DATE_RANGE = 'nextDateRange';

    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_ID       = 's.id';
    const SORT_BY_MODE_DATE     = 's.startDate';
    const SORT_BY_MODE_PRODUCT  = 'initialOrderItem.name';
    const SORT_BY_MODE_CUSTOMER = 'profile.login';
    const SORT_BY_MODE_STATUS   = 's.status';
    const SORT_BY_MODE_FEE      = 's.fee';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_ID       => 'Subscription ID',
            static::SORT_BY_MODE_DATE     => 'Date',
            static::SORT_BY_MODE_PRODUCT  => 'Product',
            static::SORT_BY_MODE_CUSTOMER => 'Profile',
            static::SORT_BY_MODE_STATUS   => 'Status',
            static::SORT_BY_MODE_FEE      => 'Fee',
        ];

        parent::__construct($params);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'id'               => [
                static::COLUMN_NAME     => static::t('Subscription #'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.id.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_ID,
                static::COLUMN_ORDERBY  => 100,
            ],
            'startDate'        => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.date.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_DATE,
                static::COLUMN_ORDERBY  => 200,
            ],
            'product'          => [
                static::COLUMN_NAME     => static::t('Product'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.product.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_PRODUCT,
                static::COLUMN_ORDERBY  => 300,
            ],
            'profile'          => [
                static::COLUMN_NAME     => static::t('Customer'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.profile.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_CUSTOMER,
                static::COLUMN_ORDERBY  => 400,
            ],
            'fee'              => [
                static::COLUMN_NAME     => static::t('Fee'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.fee.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_FEE,
                static::COLUMN_ORDERBY  => 500,
            ],
            'status'           => [
                static::COLUMN_NAME    => static::t('Status'),
                static::COLUMN_CLASS   => SubscriptionStatus::class,
                static::COLUMN_SORT    => static::SORT_BY_MODE_STATUS,
                static::COLUMN_ORDERBY => 600,
            ],
            'shipping_address' => [
                static::COLUMN_NAME     => static::t('Shipping address'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.shipping_address.twig',
                static::COLUMN_ORDERBY  => 650,
            ],
            'card'             => [
                static::COLUMN_NAME     => static::t('Card for payments'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.card.twig',
                static::COLUMN_ORDERBY  => 700,
            ],
            'statistics'       => [
                static::COLUMN_NAME     => static::t('Statistics'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/parts/cell.statistics.twig',
                static::COLUMN_ORDERBY  => 800,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return SubscriptionModel::class;
    }


    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' subscription';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return \XLite\View\Pager\Admin\Model\Table::class;
    }

    /**
     * Define line class as list of names
     *
     * @param integer                                $index  Line index
     * @param AEntity|SubscriptionModel $entity Subscription
     *
     * @return array
     */
    protected function defineLineClass($index, AEntity $entity = null)
    {
        $list = parent::defineLineClass($index, $entity);

        if ($this->isLastPaymentFailed($entity)) {
            $list[] = 'last-payment-failed';
        }

        if ($this->isLastPaymentExpired($entity)) {
            $list[] = 'last-payment-expired';
        }

        return $list;
    }

    /**
     * Preprocess profile
     *
     * @param Profile $profile Profile
     * @param array                $column  Column data
     * @param mixed                $entity  Order
     *
     * @return string
     */
    protected function preprocessProfile(Profile $profile, array $column, $entity)
    {
        $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        return $address ? $address->getName() : $profile->getLogin();
    }

    /**
     * Check - order's profile removed or not
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isProfileRemoved($subscription)
    {
        $order = $subscription->getInitialOrder();

        return !$order || !$order->getOrigProfile() || $order->getOrigProfile()->getOrder();
    }

    /**
     * isLastPaymentFailed
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isLastPaymentFailed($subscription)
    {
        return ASubscriptionPlan::STATUS_FAILED !== $subscription->getStatus()
            && $subscription->getRealDate() > $subscription->getPlannedDate();
    }

    /**
     * isLastPaymentExpired
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isLastPaymentExpired($subscription)
    {
        return $subscription->getRealDate() < Converter::now();
    }

    /**
     * isNextDateVisible
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isNextDateVisible($subscription)
    {
        return ASubscriptionPlan::STATUS_NOT_STARTED !== $subscription->getStatus()
            && ASubscriptionPlan::STATUS_FINISHED !== $subscription->getStatus()
            && ASubscriptionPlan::STATUS_STOPPED !== $subscription->getStatus()
            && ASubscriptionPlan::STATUS_FAILED !== $subscription->getStatus();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            SubscriptionRepo::SEARCH_ID              => static::PARAM_ID,
            SubscriptionRepo::SEARCH_PRODUCT_NAME    => static::PARAM_PRODUCT_NAME,
            SubscriptionRepo::SEARCH_STATUS          => static::PARAM_STATUS,
            SubscriptionRepo::SEARCH_DATE_RANGE      => static::PARAM_DATE_RANGE,
            SubscriptionRepo::SEARCH_NEXT_DATE_RANGE => static::PARAM_NEXT_DATE_RANGE,
        ];
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
            static::PARAM_ID              => new TypeString('Subscription ID', ''),
            static::PARAM_PRODUCT_NAME    => new TypeString('Product', ''),
            static::PARAM_STATUS          => new TypeString('Status', ''),
            static::PARAM_DATE_RANGE      => new TypeString('Date range', ''),
            static::PARAM_NEXT_DATE_RANGE => new TypeString('Next date range', ''),
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{SubscriptionRepo::SEARCH_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->$modelParam = $this->getParam($requestParam);
        }

        return $result;
    }

    /**
     * Return products list
     *
     * @param CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        return Database::getRepo(SubscriptionModel::class)
            ->search($cnd, $countOnly);
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

     /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Disable remove button for active subscriptions 
     *
     * @param AEntity $entity Shipping method object
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(AEntity $entity)
    {
        /** @var SubscriptionModel $entity */
        return parent::isAllowEntityRemove($entity) && !$entity->isActive();
    }

    /**
     * Get formatted card name 
     *
     * @param array $card Card details
     *
     * @return array
     */
    protected function getCardName($card)
    {
        $type = $card['card_type'] . str_repeat('&nbsp;', 4 - strlen($card['card_type']));

        return $type . ' ' . $card['card_number'] . ' ' . $card['expire'];
    }

    /**
     * Get formatted address name
     *
     * @param Address $address Address
     *
     * @return string
     */
    protected function getAddressName(Address $address = null)
    {
        return (!is_null($address))
            ? $address->getStreet() . ', ' . $address->getCity() . ', ' . $address->getCountry()->getCode() . ', ' . $address->getZipcode()
            : '';
    }
}
