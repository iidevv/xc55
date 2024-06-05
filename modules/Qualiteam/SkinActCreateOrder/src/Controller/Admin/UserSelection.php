<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;


use XLite\Core\Database;
use XLite\Core\OrderHistory;
use XLite\Core\Request;
use XLite\Core\TopMessage;

class UserSelection extends \XLite\Controller\Admin\Order
{
    use \Qualiteam\SkinActCreateOrder\Controller\Admin\Features\OrderCustomer;

    /**
     * Cache of order
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        if (null === $this->order) {
            $order = null;
            if (\XLite\Core\Request::getInstance()->order_id) {
                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')
                    ->find((int)\XLite\Core\Request::getInstance()->order_id);

            } elseif (\XLite\Core\Request::getInstance()->order_number) {
                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')
                    ->findOneByOrderNumber(\XLite\Core\Request::getInstance()->order_number);
            }

            $this->order = $order instanceof \XLite\Model\Cart
                ? null
                : $order;
        }

        return $this->order;
    }

    public function getOrderId()
    {
        return $this->getOrder() ? $this->getOrder()->getOrderId() : 0;
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['search', 'copy_profile_for_order']);
    }

    /**
     * Constructor
     *
     * @param array $params Constructor parameters
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $cellName = $this->getSessionCellName();
        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . '_' . $this->getOrderId() . md5(microtime());
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Select customer from the list');
    }

    // {{{ Search

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass()
            ?: '\Qualiteam\SkinActCreateOrder\View\ItemsList\Model\ProfileSelect';
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $cellName = $this->getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $select = [];

        foreach ((array)\XLite\Core\Request::getInstance()->select as $key => $value) {
            $select[] = 'select[]=' . $key;
        }

        $this->setHardRedirect();

        $this->setReturnURL(
            \XLite\Core\Request::getInstance()->{\XLite\View\Button\PopupProductSelector::PARAM_REDIRECT_URL}
            . '&' . implode('&', $select)
            . '&' . \XLite::FORM_ID . '=' . \XLite::getFormId()
        );
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        $className = $this->getItemsListClass();
        foreach ($className::getSearchParams() as $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = $this->getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }

    /**
     * @return void
     */
    protected function doActionSelect()
    {
        if ($this->updateOrderCustomer()) {
            \XLite\Core\TopMessage::addInfo('SkinActCreateOrder The customer for the order has been changed');

            OrderHistory::getInstance()
                ->registerEvent($this->getOrder()->getOrderId(), 'ocm', static::t('SkinActCreateOrder The customer for the order has been changed'));

            $this->setReturnURL($this->buildURL('order', false, ['order_number' => $this->getOrder()->getOrderNumber()]));
            $this->setHardRedirect();
        } else {
            \XLite\Core\TopMessage::addWarning('SkinActCreateOrder Something is wrong');
        }
    }

    protected function doActionCopyProfileForOrder()
    {
        $profileId = (int)Request::getInstance()->profile_id;
        $orderId = (int)Request::getInstance()->order_id;

        $result = false;

        if ($profileId > 0 && $orderId > 0) {

            $order = Database::getRepo(\XLite\Model\Order::class)->find($orderId);
            $profile = Database::getRepo(\XLite\Model\Profile::class)->find($profileId);

            if ($order && $profile) {
                $result = $this->updateOrderProfile($order, $profile, false);

                if ($result) {

                    $info = new \XLite\View\Order\Details\Admin\Info(['template' => 'order/page/parts/payment.address.twig']);
                    $billingContent = $info->getContent();

                    $info = new \XLite\View\Order\Details\Admin\Info(['template' => 'order/page/parts/shipping.address.twig']);
                    $shippingContent = $info->getContent();

                    $addressContent = [];

                    $addressContent['billingContent'] = $billingContent;
                    $addressContent['shippingContent'] = $shippingContent;

                    $addressContent['exists'] = 1;

                    exit(\json_encode(['addressContent' => $addressContent]));
                }
            }
        }

        if (!$result) {
            TopMessage::addInfo('Error while selecting profile for order');
        }

        exit(\json_encode(['addressContent' => ['exists' => 0]]));

    }


}
