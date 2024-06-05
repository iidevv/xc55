<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Controller\Customer;

/**
 * Create return controller
 */
class CreateReturn extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Order (local cache)
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Creating return for order #{{id}}', [
            'id' => $this->getOrder()->getOrderNumber()
        ]);
    }

    /**
     * Add the base part of the location path
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Search for orders'), $this->buildURL('order_list'));
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Create return');
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        if ($this->order === null) {
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

    public function getReturnId()
    {
        $returnId = 0;

        if (\XLite\Core\Request::getInstance()->return_id) {
            $returnId = \XLite\Core\Request::getInstance()->return_id;
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
    public function doActionCreateOrderReturn()
    {
        $order = $this->getOrder();

        if ($this->getReturnId()) {
            // Return is already created
            \XLite\Core\TopMessage::addWarning('Return for this order has already been created');

            $this->setReturnUrl(
                $this->buildUrl(
                    'order',
                    '',
                    ['order_number' => $order->getOrderNumber()]
                )
            );
        } else {
            $items = $this->prepareReturnItems();

            if (!empty($items)) {
                // Create new order return model
                $orderReturn = new \QSL\Returns\Model\OrderReturn();

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
                \XLite\Core\Mailer::sendOrderReturnCreatedAdmin($order);

                \XLite\Core\TopMessage::addInfo('The order return has been registered.');

                $this->setReturnUrl(
                    $this->buildUrl(
                        'order',
                        '',
                        ['order_number' => $order->getOrderNumber()]
                    ) . '#return_details'
                );
            } else {
                // Save prefilled values to the session
                $postedData = [
                    \QSL\Returns\Model\OrderReturn::POSTED_DATA_REASON_ID => $this->getReturnReasonId(),
                    \QSL\Returns\Model\OrderReturn::POSTED_DATA_COMMENT => $this->getReturnComment(),
                ];
                if ($this->isActionsEnabled()) {
                    $postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_ACTION_ID] = $this->getReturnActionId();
                }

                \XLite\Core\Session::getInstance()->postedData = $postedData;

                \XLite\Core\TopMessage::addWarning('No items have been selected for return.');
            }
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

        if (
            !empty($items)
            && is_array($items)
        ) {
            foreach ($items as $itemId => $itemData) {
                $itemId = intval($itemId);
                $itemData['amount'] = intval($itemData['amount']);

                if (
                    0 < $itemData['amount']
                    && $this->getOrder()->getOrderItemById($itemId)
                ) {
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
        $select = [];

        $selectTmp = \XLite\Core\Request::getInstance()->select ?? [];

        $items = \XLite\Core\Request::getInstance()->items ?? [];

        if ($selectTmp) {
            foreach ($selectTmp as $k => $v) {
                if ($v) {
                    $select[$k] = $v;
                }
            }
        }

        if (
            $select
            && is_array($select)
            && $items
            && is_array($items)
        ) {
            foreach ($items as $itemId => $itemData) {
                if (array_key_exists($itemId, $select)) {
                    $newItems[$itemId] = $itemData;
                }
            }
        }

        return $newItems;
    }

    // {{{ Show return info


    /*
     * Get return reason Id (for 'modify return' page)
     */
    public function getReasonIdValue()
    {
        if (
            isset(\XLite\Core\Session::getInstance()->postedData)
            && isset(\XLite\Core\Session::getInstance()->postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_REASON_ID])
        ) {
            // Fetch prefilled values from the session
            $reasonId = \XLite\Core\Session::getInstance()->postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_REASON_ID];
        } elseif (
            $this->getReturnId()
            && $this->getOrder()->getOrderReturn()->getReason()
        ) {
            $reasonId = $this->getOrder()->getOrderReturn()->getReason()->getId();
        } else {
            $reasonId = 0;
        }

        return $reasonId;
    }

    /*
     * Get return action Id (for 'modify return' page)
     */
    public function getActionIdValue()
    {
        if (
            isset(\XLite\Core\Session::getInstance()->postedData)
            && isset(\XLite\Core\Session::getInstance()->postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_ACTION_ID])
        ) {
            // Fetch prefilled values from the session
            $actionId = \XLite\Core\Session::getInstance()->postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_ACTION_ID];
        } elseif (
            $this->getReturnId()
            && $this->getOrder()->getOrderReturn()->getAction()
        ) {
            $actionId = $this->getOrder()->getOrderReturn()->getAction()->getId();
        } else {
            $actionId = 0;
        }

        return $actionId;
    }

    /*
     * Get return comment (for 'modify return' page)
     */
    public function getCommentValue()
    {
        if (
            isset(\XLite\Core\Session::getInstance()->postedData)
            && isset(\XLite\Core\Session::getInstance()->postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_COMMENT])
        ) {
            // Fetch prefilled values from the session
            $comment = \XLite\Core\Session::getInstance()->postedData[\QSL\Returns\Model\OrderReturn::POSTED_DATA_COMMENT];
        } elseif ($this->getReturnId()) {
            $comment = $this->getOrder()->getOrderReturn()->getComment();
        } else {
            $comment = '';
        }

        return $comment;
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

    /*
     * Get return item amount (for 'modify return' page)
     */
    public function getReturnItemAmount($item)
    {
        $currentAmount = 0;

        if ($item) {
            if ($this->getOrder()->getOrderReturn()) {
                /*
                 * Return is already exists
                 */
                $returnItem = $item->getReturnItemByOrder($this->getOrder()->getOrderId());

                if ($returnItem) {
                    // Get amount from existed return
                    $currentAmount = $returnItem->getAmount();
                } else {
                    // This item has not been included to return
                    $currentAmount = 0;
                }
            } else {
                $currentAmount = $item->getAmount();
            }
        }

        return $currentAmount;
    }

    // }}}

    // {{{ Create return

    /**
     * Get return reason ID
     *
     * @return string
     */
    protected function getReturnReasonId()
    {
        return intval(\XLite\Core\Request::getInstance()->reason_id ?: 0);
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
            $reason = \XLite\Core\Database::getRepo('QSL\Returns\Model\ReturnReason')->find($reasonId);
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
        return intval(\XLite\Core\Request::getInstance()->action_id ?: 0);
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
            $action = \XLite\Core\Database::getRepo('QSL\Returns\Model\ReturnAction')->find($actionId);
        }

        return $action;
    }

    public function isActionsEnabled()
    {
        return (bool)\XLite\Core\Config::getInstance()->QSL->Returns->enable_actions;
    }

    /**
     * Get return comment
     *
     * @return string
     */
    protected function getReturnComment()
    {
        return strval(\XLite\Core\Request::getInstance()->comment ?: '');
    }

    // }}}

    // {{{ Check if user has access to current order

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && $this->getOrder()
            && ($this->checkOrderAccess() || $this->isLastAnonymousOrder() || $this->checkAccessControls());
    }

    /**
     * Check order access
     *
     * @return boolean
     */
    protected function checkOrderAccess()
    {
        return \XLite\Core\Auth::getInstance()->isLogged() && $this->checkOrderProfile();
    }

    /**
     * Check if order corresponds to current user
     *
     * @return boolean
     */
    protected function checkOrderProfile()
    {
        return $this->getOrder()
            && $this->getOrder()->getOrigProfile()
            && \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
                == $this->getOrder()->getOrigProfile()->getProfileId();
    }

    /**
     * Check if requested order has just been placed by the visitor
     *
     * @return boolean
     */
    protected function isLastAnonymousOrder()
    {
        return $this->getOrder()->getOrderId() == \XLite\Core\Session::getInstance()->last_order_id;
    }

    /**
     * Return Access control entities for controller as [key => entity]
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlEntities()
    {
        return [$this->getOrder()];
    }

    /**
     * Return Access control zones for controller
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlZones()
    {
        return ['order'];
    }

    // }}}
}
