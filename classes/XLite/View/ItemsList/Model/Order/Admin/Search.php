<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Order\Admin;

class Search extends \XLite\View\ItemsList\Model\Order\Admin\AAdmin
{
    /**
     * Widget param names
     */
    public const PARAM_ORDER_ID        = 'orderNumber';
    public const PARAM_LOGIN           = 'login';
    public const PARAM_PAYMENT_STATUS  = 'paymentStatus';
    public const PARAM_SHIPPING_STATUS = 'shippingStatus';
    public const PARAM_DATE            = 'date';
    public const PARAM_SUBSTRING       = 'substring';
    public const PARAM_DATE_RANGE      = 'dateRange';
    public const PARAM_PROFILE_ID      = 'profileId';
    public const PARAM_ACCESS_LEVEL    = 'accessLevel';
    public const PARAM_ZIPCODE         = 'zipcode';
    public const PARAM_CUSTOMER_NAME   = 'customerName';
    public const PARAM_TRANS_ID        = 'transactionID';
    public const PARAM_RECENT          = 'recent';
    public const PARAM_SKU             = 'sku';
    public const PARAM_COUNTRY         = 'country';
    public const PARAM_STATE           = 'state';
    public const PARAM_CUSTOM_STATE    = 'customState';
    public const PARAM_CITY            = 'city';
    public const PARAM_TYPE_ADDRESS    = 'address';

    /**
     * Allowed sort criteria
     */
    public const SORT_BY_MODE_ID               = 'o.orderNumber';
    public const SORT_BY_MODE_DATE             = 'o.date';
    public const SORT_BY_MODE_CUSTOMER         = 'p.login';
    public const SORT_BY_MODE_PAYMENT_STATUS   = 'o.paymentStatus';
    public const SORT_BY_MODE_SHIPPING_STATUS  = 'o.shippingStatus';
    public const SORT_BY_MODE_TOTAL            = 'o.total';

    /**
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_ID              => 'Order ID',
            static::SORT_BY_MODE_DATE            => 'Date',
            static::SORT_BY_MODE_CUSTOMER        => 'Customer',
            static::SORT_BY_MODE_PAYMENT_STATUS  => 'Payment status',
            static::SORT_BY_MODE_SHIPPING_STATUS => 'Shipping status',
            static::SORT_BY_MODE_TOTAL           => 'Amount',
        ];

        parent::__construct($params);
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/order/style.less';

        return $list;
    }

    /**
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'order_list';
    }

    /**
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(parent::getFormParams(), [
                'statusToSet' => '',
            ]);
    }

    /**
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.order.search.blank');
    }

    /**
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        if (!empty($params[static::PARAM_DATE]) && is_array($params[static::PARAM_DATE])) {
            foreach ($params[static::PARAM_DATE] as $i => $date) {
                if (is_string($date) && strtotime($date) !== false) {
                    $params[static::PARAM_DATE][$i] = strtotime($date);
                }
            }
        }

        parent::setWidgetParams($params);
    }

    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'orderNumber' => [
                static::COLUMN_NAME     => static::t('Order #'),
                static::COLUMN_LINK     => 'order',
                static::COLUMN_SORT     => static::SORT_BY_MODE_ID,
                static::COLUMN_ORDERBY  => 100,
            ],
            'date' => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/order/cell.date.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_DATE,
                static::COLUMN_ORDERBY  => 200,
            ],
            'profile' => [
                static::COLUMN_NAME     => static::t('Customer'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/order/cell.profile.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_CUSTOMER,
                static::COLUMN_ORDERBY  => 300,
            ],
            'paymentStatus' => [
                static::COLUMN_NAME     => static::t('Payment status'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Select\OrderStatus\Payment',
                static::COLUMN_SORT     => static::SORT_BY_MODE_PAYMENT_STATUS,
                static::COLUMN_ORDERBY  => 400,
            ],
            'shippingStatus' => [
                static::COLUMN_NAME     => static::t('Shipping status'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Select\OrderStatus\Shipping',
                static::COLUMN_SORT     => static::SORT_BY_MODE_SHIPPING_STATUS,
                static::COLUMN_ORDERBY  => 500,
            ],
            'total' => [
                static::COLUMN_NAME     => static::t('Amount'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/order/cell.total.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_TOTAL,
                static::COLUMN_ORDERBY  => 600,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array_merge(parent::getListNameSuffixes(), ['search']);
    }

    /**
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Order\Admin\Search';
    }

    /**
     * @param \XLite\Model\Profile $profile Profile
     * @param array                $column  Column data
     * @param \XLite\Model\Order   $entity  Order
     *
     * @return string
     */
    protected function preprocessProfile(\XLite\Model\Profile $profile, array $column, \XLite\Model\Order $entity)
    {
        $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        return $address ? $address->getName() : $profile->getLogin();
    }

    /**
     * @param integer              $orderNumber Order number
     * @param array                $column      Column data
     * @param \XLite\Model\Order   $entity      Order
     *
     * @return string
     */
    protected function preprocessOrderNumber($orderNumber, array $column, \XLite\Model\Order $entity)
    {
        return $entity->getPrintableOrderNumber();
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Order::P_ORDER_NUMBER       => static::PARAM_ORDER_ID,
            \XLite\Model\Repo\Order::P_EMAIL              => static::PARAM_LOGIN,
            \XLite\Model\Repo\Order::P_PAYMENT_STATUS     => static::PARAM_PAYMENT_STATUS,
            \XLite\Model\Repo\Order::P_SHIPPING_STATUS    => static::PARAM_SHIPPING_STATUS,
            \XLite\Model\Repo\Order::P_DATE               => static::PARAM_DATE,
            \XLite\Model\Repo\Order::SEARCH_DATE_RANGE    => static::PARAM_DATE_RANGE,
            \XLite\Model\Repo\Order::SEARCH_SUBSTRING     => static::PARAM_SUBSTRING,
            \XLite\Model\Repo\Order::P_PROFILE_ID         => static::PARAM_PROFILE_ID,
            \XLite\Model\Repo\Order::SEARCH_ACCESS_LEVEL  => static::PARAM_ACCESS_LEVEL,
            \XLite\Model\Repo\Order::SEARCH_ZIPCODE       => static::PARAM_ZIPCODE,
            \XLite\Model\Repo\Order::SEARCH_CUSTOMER_NAME => static::PARAM_CUSTOMER_NAME,
            \XLite\Model\Repo\Order::SEARCH_TRANS_ID      => static::PARAM_TRANS_ID,
            \XLite\Model\Repo\Order::P_RECENT             => static::PARAM_RECENT,
            \XLite\Model\Repo\Order::SEARCH_SKU           => static::PARAM_SKU,
            \XLite\Model\Repo\Order::SEARCH_COUNTRY       => static::PARAM_COUNTRY,
            \XLite\Model\Repo\Order::SEARCH_STATE         => static::PARAM_STATE,
            \XLite\Model\Repo\Order::SEARCH_CUSTOM_STATE  => static::PARAM_CUSTOM_STATE,
            \XLite\Model\Repo\Order::SEARCH_CITY          => static::PARAM_CITY,
            \XLite\Model\Repo\Order::SEARCH_TYPE_ADDRESS  => static::PARAM_TYPE_ADDRESS,
        ];
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER_ID        => new \XLite\Model\WidgetParam\TypeString('Order ID', ''),
            static::PARAM_LOGIN           => new \XLite\Model\WidgetParam\TypeString('Email', ''),
            static::PARAM_PAYMENT_STATUS  => new \XLite\Model\WidgetParam\TypeCollection('Payment status', []),
            static::PARAM_SHIPPING_STATUS => new \XLite\Model\WidgetParam\TypeCollection('ShippingShipping status', []),
            static::PARAM_DATE            => new \XLite\Model\WidgetParam\TypeCollection('Date', [null, null]),
            static::PARAM_DATE_RANGE      => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
            static::PARAM_SUBSTRING       => new \XLite\Model\WidgetParam\TypeString('Substring', ''),
            static::PARAM_PROFILE_ID      => new \XLite\Model\WidgetParam\TypeInt('Profile ID', 0),
            static::PARAM_ACCESS_LEVEL    => new \XLite\Model\WidgetParam\TypeString('Customer access level', ''),
            static::PARAM_ZIPCODE         => new \XLite\Model\WidgetParam\TypeString('Customer zip/postal code', ''),
            static::PARAM_CUSTOMER_NAME   => new \XLite\Model\WidgetParam\TypeString('Customer name', ''),
            static::PARAM_TRANS_ID        => new \XLite\Model\WidgetParam\TypeString('Payment transaction ID', ''),
            static::PARAM_RECENT          => new \XLite\Model\WidgetParam\TypeBool('Recent', false),
            static::PARAM_SKU             => new \XLite\Model\WidgetParam\TypeString('SKU', ''),
            \XLite\Controller\Admin\AAdmin::PARAM_SEARCH_FILTER_ID => new \XLite\Model\WidgetParam\TypeString('Search filter ID', null),
            static::PARAM_COUNTRY         => new \XLite\Model\WidgetParam\TypeString('Country', ''),
            static::PARAM_STATE           => new \XLite\Model\WidgetParam\TypeInt('State', null),
            static::PARAM_CUSTOM_STATE    => new \XLite\Model\WidgetParam\TypeString('State name (custom)', ''),
            static::PARAM_CITY            => new \XLite\Model\WidgetParam\TypeString('City', ''),
            static::PARAM_TYPE_ADDRESS    => new \XLite\Model\WidgetParam\TypeString('Search by address', ''),
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

        $this->requestParams = array_merge(
            $this->requestParams,
            static::getSearchParams(),
            [\XLite\Controller\Admin\AAdmin::PARAM_SEARCH_FILTER_ID]
        );
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    public function getConditionForNexPrevious()
    {
        return $this->getSearchCondition();
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{\XLite\Model\Repo\Order::P_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $value = $this->getParam($requestParam);
            if ($requestParam === static::PARAM_DATE && is_array($value)) {
                foreach ($value as $i => $date) {
                    if (is_string($date) && strtotime($date) !== false) {
                        $value[$i] = strtotime($date);
                    }
                }
            } elseif (is_string($value)) {
                $value = $this->widgetParams[$requestParam] instanceof \XLite\Model\WidgetParam\TypeCollection
                    ? explode(',', $value)
                    : trim($value);
            }

            $result->$modelParam = $value;
        }

        if ($result->{\XLite\Model\Repo\Order::SEARCH_COUNTRY}) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find(
                $result->{\XLite\Model\Repo\Order::SEARCH_COUNTRY}
            );
            if (!$country || !$country->hasStates()) {
                $result->{\XLite\Model\Repo\Order::SEARCH_STATE} = null;
            }
            if (!$country || $country->hasStates()) {
                $result->{\XLite\Model\Repo\Order::SEARCH_CUSTOM_STATE} = null;
            }
        }

        $result = \XLite\Core\Database::getRepo('XLite\Model\Order')->correctSearchConditions($result);

        return $result;
    }

    /**
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Order')->search($cnd, $countOnly);
    }

    /**
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
    }

    /**
     * @param \XLite\Model\Order $order Order
     *
     * @return integer
     */
    protected function getItemsQuantity(\XLite\Model\Order $order)
    {
        return $order->countQuantity();
    }

    /**
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isProfileRemoved(\XLite\Model\Order $order)
    {
        return !$order->getOrigProfile() || $order->getOrigProfile()->getOrder();
    }

    /**
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }
}
