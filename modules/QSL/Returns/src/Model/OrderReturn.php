<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order return
 *
 * @ORM\Entity (repositoryClass="\QSL\Returns\Model\Repo\OrderReturn")
 * @ORM\Table (name="order_returns")
 */
class OrderReturn extends \XLite\Model\AEntity
{
    /**
     * Return status codes
     */
    public const STATUS_ISSUED    = 'I';
    public const STATUS_COMPLETED = 'C';
    public const STATUS_DECLINED  = 'D';

    /*
     * Temporary stored field values (posted data)
     */
    public const POSTED_DATA_REASON_ID = 'reasonId';
    public const POSTED_DATA_ACTION_ID = 'actionId';
    public const POSTED_DATA_COMMENT   = 'comment';

    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Comment
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $comment = '';

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = self::STATUS_ISSUED;

    /**
     * Creation date (UNIX timestamp)
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date = 0;

    /**
     * Order
     *
     * @var \XLite\Model\Order
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Order", inversedBy="orderReturn")
     * @ORM\JoinColumn (name="orderId", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Relation to a reason entity
     *
     * @var \QSL\Returns\Model\ReturnReason
     *
     * @ORM\ManyToOne   (targetEntity="QSL\Returns\Model\ReturnReason")
     * @ORM\JoinColumn (name="returnReasonId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reason;

    /**
     * Relation to a action entity
     *
     * @var \QSL\Returns\Model\ReturnAction
     *
     * @ORM\ManyToOne   (targetEntity="QSL\Returns\Model\ReturnAction")
     * @ORM\JoinColumn (name="returnActionId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $action;

    /**
     * Order return items
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\Returns\Model\ReturnItem", mappedBy="orderReturn", cascade={"all"})
     */
    protected $items;

    /**
     * @var bool
     */
    private $vendorOrder;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add an item
     *
     * @param \QSL\Returns\Model\ReturnItem $newItem Item object
     *
     * @return void
     */
    public function addItem(\QSL\Returns\Model\ReturnItem $newItem)
    {
        $newItem->setReturn($this);

        $this->addItems($newItem);
    }

    public static function getStatusName($status)
    {
        switch ($status) {
            case static::STATUS_ISSUED:
                $statusName = static::t('Issued');
                break;

            case static::STATUS_COMPLETED:
                $statusName = static::t('Completed');
                break;

            case static::STATUS_DECLINED:
                $statusName = static::t('Declined');
                break;

            default:
                $statusName = static::t('n/a');
                break;
        }

        return $statusName;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return OrderReturn
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return OrderReturn
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return OrderReturn
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     *
     * @return OrderReturn
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set reason
     *
     * @param \QSL\Returns\Model\ReturnReason $reason
     *
     * @return OrderReturn
     */
    public function setReason(\QSL\Returns\Model\ReturnReason $reason = null)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return \QSL\Returns\Model\ReturnReason
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set return action
     *
     * @param \QSL\Returns\Model\ReturnAction $action
     *
     * @return OrderReturn
     */
    public function setAction(\QSL\Returns\Model\ReturnAction $action = null)
    {
        // Update only if actions is enabled. This helps to preserve references,
        // already collected before the functionality was disabled
        if (\XLite\Core\Config::getInstance()->QSL->Returns->enable_actions) {
            $this->action = $action;
        }

        return $this;
    }

    /**
     * Get return action
     *
     * @return \QSL\Returns\Model\ReturnAction
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Add items
     *
     * @param \QSL\Returns\Model\ReturnItem $items
     *
     * @return OrderReturn
     */
    public function addItems(\QSL\Returns\Model\ReturnItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Tells if this is full return
     *
     * @return boolean
     */
    public function isFullReturn()
    {
        $orderItems  = $this->getOrder()->getItems();
        $returnItems = $this->getItems();

        $totalAmountInOrder  = 0;
        $totalAmountInReturn = 0;

        foreach ($orderItems as $orderItem) {
            $totalAmountInOrder += $orderItem->getAmount();
        }

        foreach ($returnItems as $orderItem) {
            $totalAmountInReturn += $orderItem->getAmount();
        }

        return $totalAmountInReturn >= $totalAmountInOrder;
    }

    /**
     * Tells if this is partial return
     *
     * @return boolean
     */
    public function isPartialReturn()
    {
        return !$this->isFullReturn();
    }

    public function postprocessReturnCompleted()
    {
        if ($this->isFullReturn()) {
            $this->postprocessFullReturnCompleted();
        } else {
            $this->postprocessPartialReturnCompleted();
        }
    }

    public function postprocessFullReturnCompleted()
    {
        $this->makeFullOrderRefund();
    }

    public function makeFullOrderRefund()
    {
        $order = $this->getOrder();
        $order->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_REFUNDED);
        $order->setShippingStatus(\XLite\Model\Order\Status\Shipping::STATUS_RETURNED);
    }

    public function postprocessPartialReturnCompleted()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->partial_order_status === 'refund') {
            $this->makeFullOrderRefund();

            return;
        }

        $originalVendorTotal = $this->getVendorOrderTotal();

        if ($request->partial_update_order_items) {
            $this->updateOrderItems();
        }

        if ($request->partial_update_order_items) {
            $this->createVendorTransaction($originalVendorTotal);
        }
    }

    protected function updateOrderItems()
    {
        $order = $this->getOrder();

        $returnItems = $this->getItems();
        $orderItems  = $order->getItems();

        // prepare request
        $request                                 = \XLite\Core\Request::getInstance();
        $request->target                         = 'order';
        $request->action                         = 'update';
        $request->doNotSendNotification          = '1';
        $request->xcart_form_id                  = \XLite::getFormId(true);
        $request->is_initiated_by_partial_return = true;

        $request->order_id = $order->getOrderId();

        $returnedItemsMap = [];
        foreach ($returnItems as $returnItem) {
            $returnedItemsMap[$returnItem->getOrderItem()->getItemId()] = $returnItem->getAmount();
        }

        $orderItemsData   = [];
        $deleteOrderItems = [];

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            $oiid      = $orderItem->getItemId();
            $newAmount = $orderItem->getAmount();

            if (isset($returnedItemsMap[$oiid])) {
                $newAmount -= $returnedItemsMap[$oiid];
            }

            if ($newAmount === 0) {
                $deleteOrderItems[$oiid] = 1;
                // {{{ explicitly break relationship
                $returnItems = $orderItem->getReturnItems();
                foreach ($returnItems as $returnItem) {
                    $returnItem->setOrderItem(null);
                }

                $orderItem->deleteReturnItems();
                // }}}
            } else {
                $orderItemsData[$oiid]['amount'] = $newAmount;
            }
        }

        if (!empty($orderItemsData)) {
            $request->order_items = $orderItemsData;
        }
        if (!empty($deleteOrderItems)) {
            $request->delete_order_items = $deleteOrderItems;
        }

        \XLite\Core\Database::getEM()->flush();

        // call "Update Order" action:
        $controllerName = \XLite\Core\Converter::getControllerClass('order');
        $controller     = new $controllerName();
        $controller->handleRequest(); // <<< тут отрабатывается как я того ожидаю

        $request->is_initiated_by_partial_return = false;
    }

    protected function createVendorTransaction($originalTotal)
    {
        if (!$this->isVendorOrder()) {
            return;
        }

        $order = $this->getOrder();
        $order->renew();

        $newTotal = $this->getVendorOrderTotal();
        $value    = $newTotal - $originalTotal;

        $description = static::t('Partial return for order #X', [
            'orderNumber' => $order->getOrderNumber(),
        ]);

        $order->createProfileTransaction(
            -1 * $value,
            $description
        );
    }

    /**
     * @return float
     */
    protected function getVendorOrderTotal()
    {
        $order = $this->getOrder();

        if (!$this->isVendorOrder()) {
            return $order->getTotal();
        }

        /** @var \XC\MultiVendor\Model\Commission $commission */
        $commission = $order->getCommission();

        return $commission ? $commission->getValue() : $order->getTotal();
    }

    /**
     * @return bool
     */
    protected function isVendorOrder(): bool
    {
        if ($this->vendorOrder === null) {
            $order = $this->getOrder();

            $this->vendorOrder = method_exists($order, 'getVendor') && $order->getVendor();
        }

        return $this->vendorOrder;
    }
}
