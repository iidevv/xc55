<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Request;
use QSL\Returns\Model\OrderReturn;

/**
 * Order page controller
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $request = Request::getInstance();
        $returnId = $this->getReturnId();

        if ($request->page === 'create_return' && $request->order_number && !$returnId) {
            return static::t('Creating return for order #{{order_number}}', [
                'order_number' => $request->order_number
            ]);
        }

        if ($request->page === 'modify_return' && $request->order_number && $returnId) {
            return static::t('Order #{{order_number}}', [
                'order_number' => $request->order_number
            ]);
        }

        return parent::getTitle();
    }

    public function getReturnId()
    {
        $returnId = 0;
        $request = Request::getInstance();

        if ($request->return_id) {
            $returnId = $request->return_id;
        } else {
            // Find returnId by order
            $order = $this->getOrder();

            if ($order) {
                $orderReturn = \XLite\Core\Database::getRepo('QSL\Returns\Model\OrderReturn')
                    ->findOneBy(['order' => $order]);

                if ($orderReturn) {
                    $returnId = $orderReturn->getId();
                }
            }
        }

        return $returnId;
    }

    /**
     * doActionCreateOrderReturn
     *
     * @return void
     */
    protected function doActionCreateOrderReturn()
    {
        \XLite\Core\Session::getInstance()->postedData = null;

        $order = $this->getOrder();

        if ($this->getReturnId()) {
            // Return is already created
            \XLite\Core\TopMessage::addWarning('Return for this order has already been created');

            $this->setReturnUrl(
                $this->buildUrl(
                    'order',
                    '',
                    ['order_number' => $order->getOrderNumber(), 'page' => 'modify_return']
                )
            );
        } else {
            $items = $this->prepareReturnItems();

            if (!empty($items)) {
                // Create new order return model
                $orderReturn = new OrderReturn();

                \XLite\Core\Database::getEM()->persist($orderReturn);

                $orderReturn->setOrder($order);
                $orderReturn->setReason($this->getReturnReason());
                if ($this->isActionsEnabled()) {
                    $orderReturn->setAction($this->getReturnAction());
                }
                $orderReturn->setComment($this->getReturnComment());
                $orderReturn->setDate(\XLite\Core\Converter::time());

                foreach ($items as $itemId => $itemData) {
                    // Create new order return item model
                    $returnItem = new \QSL\Returns\Model\ReturnItem();

                    \XLite\Core\Database::getEM()->persist($returnItem);

                    $returnItem->setAmount($itemData['amount']);
                    $orderItem = $order->getOrderItemById($itemId);
                    $returnItem->setOrderItem($orderItem);
                    $returnItem->setName($orderItem->getName());

                    $orderReturn->addItem($returnItem);
                }

                $order->setOrderReturn($orderReturn);

                \XLite\Core\Database::getEM()->flush();

                \XLite\Core\Mailer::sendOrderReturnCreatedCustomer($order);

                \XLite\Core\TopMessage::addInfo('The order return has been registered.');

                $this->setReturnUrl(
                    $this->buildUrl(
                        'order',
                        '',
                        ['order_number' => $order->getOrderNumber(), 'page' => 'modify_return']
                    )
                );
            } else {
                // Save prefilled values to the session
                $postedData = [
                    OrderReturn::POSTED_DATA_REASON_ID => $this->getReturnReasonId(),
                    OrderReturn::POSTED_DATA_COMMENT => $this->getReturnComment(),
                ];
                if ($this->isActionsEnabled()) {
                    $postedData[OrderReturn::POSTED_DATA_ACTION_ID] = $this->getReturnActionId();
                }

                \XLite\Core\Session::getInstance()->postedData = $postedData;

                \XLite\Core\TopMessage::addWarning('No items have been selected for return.');
            }
        }
    }

    /**
     * doActionModifyOrderReturn
     *
     * @return void
     */
    protected function doActionModifyOrderReturn()
    {
        $order = $this->getOrder();

        if (!$this->getReturnId()) {
            // Return is not created yet
            \XLite\Core\TopMessage::addWarning('Return for this order has not been created yet');

            $this->setReturnUrl(
                $this->buildUrl(
                    'order',
                    '',
                    ['order_number' => $order->getOrderNumber(), 'page' => 'create_return']
                )
            );
        } else {
            $orderReturn = $order->getOrderReturn();
            $orderReturn->setReason($this->getReturnReason());

            if ($this->isActionsEnabled()) {
                $orderReturn->setAction($this->getReturnAction());
            }

            $orderReturn->setComment($this->getReturnComment());

            $request = Request::getInstance();
            $em = \XLite\Core\Database::getEM();

            $items = $request->items ?? [];
            $delete = $request->delete ?? [];

            foreach ($orderReturn->getItems() as $item) {
                if (isset($delete[$item->getId()])) {
                    $em->remove($item);
                } elseif (isset($items[$item->getId()])) {
                    $item->setAmount($items[$item->getId()]['amount']);
                }
            }

            $em->flush();

            \XLite\Core\TopMessage::addInfo('The order return has been updated.');
        }
    }

    /**
     * doActionCompleteReturn
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionCompleteReturn()
    {
        /** @var \QSL\Returns\Model\Order $order */
        $order = $this->getOrder();
        $orderReturn = $order->getOrderReturn();

        if ($orderReturn) {
            $orderReturn->setStatus(OrderReturn::STATUS_COMPLETED);
            $orderReturn->postprocessReturnCompleted();
        }

        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Mailer::sendOrderReturnCompleted($order);

        \XLite\Core\TopMessage::addInfo('The order return has been completed.');
    }

    /**
     * doActionDeclineReturn
     *
     * @return void
     */
    protected function doActionDeclineReturn()
    {
        $order = $this->getOrder();

        if ($order->getOrderReturn()) {
            $order->getOrderReturn()->setStatus(OrderReturn::STATUS_DECLINED);

            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\Mailer::sendOrderReturnDeclined($order);

            \XLite\Core\TopMessage::addInfo('The order return has been declined.');
        }
    }

    /**
     * doActionCompleteReturn
     *
     * @return void
     */
    protected function doActionDeleteReturn()
    {
        $order = $this->getOrder();

        if ($order->getOrderReturn()) {
            \XLite\Core\Database::getEM()->remove($order->getOrderReturn());
            $order->setOrderReturn(null);

            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo('The order return has been deleted.');

            $this->setReturnUrl(
                $this->buildUrl(
                    'order',
                    '',
                    ['order_number' => $order->getOrderNumber(), 'page' => 'create_return']
                )
            );
        }
    }

    /**
     * Prepare return items data
     *
     * @return array
     */
    protected function prepareReturnItems()
    {
        $items = $this->getReturnItems();

        $result = [];

        if (!empty($items) && is_array($items)) {
            foreach ($items as $itemId => $itemData) {
                $itemId = (int)$itemId;
                $itemData['amount'] = (int)$itemData['amount'];

                if ($itemData['amount'] > 0 && $this->getOrder()->getOrderItemById($itemId)) {
                    $result[$itemId] = [
                        'amount' => $itemData['amount'],
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get return items list
     *
     * @return array
     */
    protected function getReturnItems()
    {
        $newItems = [];
        $request = Request::getInstance();

        $select = $request->select ?? [];
        $items = $request->items ?? [];

        if ($select && is_array($select) && $items && is_array($items)) {
            foreach ($items as $itemId => $itemData) {
                if (array_key_exists($itemId, $select)) {
                    $newItems[$itemId] = $itemData;
                }
            }
        }

        return $newItems;
    }

    // {{{ Create return

    /**
     * Get return reason ID
     *
     * @return string
     */
    protected function getReturnReasonId()
    {
        return (int)Request::getInstance()->reason_id ?: 0;
    }

    /**
     * Get return reason
     *
     * @return string
     */
    protected function getReturnReason()
    {
        $reason = null;

        $reasonId = $this->getReturnReasonId();

        if ($reasonId) {
            $reason = \XLite\Core\Database::getRepo('QSL\Returns\Model\ReturnReason')
                ->find($reasonId);
        }

        return $reason;
    }

    /**
     * Get return action ID
     *
     * @return string
     */
    protected function getReturnActionId()
    {
        return (int)Request::getInstance()->action_id ?: 0;
    }

    /**
     * Get return action
     *
     * @return string
     */
    protected function getReturnAction()
    {
        $action = null;

        $actionId = $this->getReturnActionId();

        if ($actionId) {
            $action = \XLite\Core\Database::getRepo('QSL\Returns\Model\ReturnAction')
                ->find($actionId);
        }

        return $action;
    }

    /**
     * Get return comment
     *
     * @return string
     */
    protected function getReturnComment()
    {
        return (string)Request::getInstance()->comment ?: '';
    }

    protected function isOrderReturnCreated()
    {
        $order = $this->getOrder();

        if ($order) {
            return (bool)\XLite\Core\Database::getRepo('QSL\Returns\Model\OrderReturn')
                ->findOneBy(['order' => $order]);
        }

        return false;
    }

    // }}}

    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        $isReturnAdded = false;

        $newList = [];

        // Insert 'Return' tab after 'Default' one
        if ($list) {
            foreach ($list as $k => $v) {
                $newList[$k] = $v;

                if ($k === 'default') {
                    $newList = array_merge($newList, $this->defineReturnPages());

                    $isReturnAdded = true;
                }
            }
        }

        if (!$isReturnAdded) {
            // Insert 'Return' tab at the end
            $newList = array_merge($newList, $this->defineReturnPages());
        }

        return $newList;
    }

    protected function defineReturnPages()
    {
        $list = [];

        if (Auth::getInstance()->isPermissionAllowed('manage orders')) {
            if ($this->isOrderReturnCreated()) {
                $list['modify_return'] = [
                    'title'        => static::t('Order return'),
                    'linkTemplate' => 'modules/QSL/Returns/order/page/order_return.twig',
                ];
            } else {
                $list['create_return'] = static::t('Create return');
            }
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if ($this->isOrderReturnCreated()) {
            $list['modify_return'] = 'modules/QSL/Returns/order/page/modify_return.twig';
        } else {
            $list['create_return'] = 'modules/QSL/Returns/order/page/create_return.twig';
        }

        return $list;
    }

    public function isOrderReturnIssued()
    {
        $order = $this->getOrder();

        return $order
            && $order->getOrderReturn()
            && $order->getOrderReturn()->getStatus() == OrderReturn::STATUS_ISSUED;
    }

    // }}}

    // {{ Show return info

    /*
     * Get return reason Id (for 'modify return' page)
     */
    public function getReasonIdValue()
    {
        $session = \XLite\Core\Session::getInstance();

        if (isset($session->postedData[OrderReturn::POSTED_DATA_REASON_ID])) {
            // Fetch prefilled values from the session
            return $session->postedData[OrderReturn::POSTED_DATA_REASON_ID];
        }

        if ($this->getReturnId()) {
            /** @var \QSL\Returns\Model\ReturnReason $reason */
            $reason = $this->getOrder()
                ->getOrderReturn()
                ->getReason();

            return $reason ? $reason->getId() : 0;
        }

        return 0;
    }

    /*
     * Get return action Id (for 'modify return' page)
     */
    public function getActionIdValue()
    {
        $session = \XLite\Core\Session::getInstance();

        if (isset($session->postedData[OrderReturn::POSTED_DATA_ACTION_ID])) {
            // Fetch prefilled values from the session
            return $session->postedData[OrderReturn::POSTED_DATA_ACTION_ID];
        }

        if ($this->getReturnId()) {
            /** @var \QSL\Returns\Model\ReturnAction $action */
            $action = $this->getOrder()->getOrderReturn()->getAction();

            return $action ? $action->getId() : 0;
        }

        return 0;
    }

    public function isActionsEnabled()
    {
        return (bool)\XLite\Core\Config::getInstance()->QSL->Returns->enable_actions;
    }

    /*
     * Get return comment (for 'modify return' page)
     */
    public function getCommentValue()
    {
        $session = \XLite\Core\Session::getInstance();

        if (isset($session->postedData[OrderReturn::POSTED_DATA_COMMENT])) {
            // Fetch prefilled values from the session
            return $session->postedData[OrderReturn::POSTED_DATA_COMMENT];
        }

        if ($this->getReturnId()) {
            return $this->getOrder()->getOrderReturn()->getComment();
        }

        return '';
    }

    /**
     * Is redirect needed
     *
     * @return boolean
     */
    public function isRedirectNeeded()
    {
        $request = Request::getInstance();
        return (
            !empty($request->is_initiated_by_partial_return)
            && $request->action === 'update'
        )
            ? false
            : parent::isRedirectNeeded();
    }

    /*
     * Get submit button text (for 'create/modify return' pages)
     */
    public function getSubmitButtonLabel()
    {
        return $this->getReturnId()
            ? static::t('Apply changes')
            : static::t('Submit return');
    }

    public function isPartialReturn()
    {
        return $this->getOrder()->getOrderReturn()
            ? $this->getOrder()->getOrderReturn()->isPartialReturn()
            : false;
    }

    public function getPartialReturnOrderStatusOptions()
    {
        return [
            '' => static::t('Leave unchanged'),
            'refund' => static::t('Refund the order'),
        ];
    }

    public function isPersonalTransactionAvailable()
    {
        return method_exists($this->getOrder(), 'getVendor')
            && $this->getOrder()->getVendor();
    }

    public function getPersonalTransactionComment()
    {
        $vendorName = $this->getOrder()->getVendor()->getName();

        return static::t('Order subtotal difference will be reversed for vendor: V', [
            'vendorName' => $vendorName,
        ]);
    }
    // }}}
}
