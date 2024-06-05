<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use XLite\Model\Order;

/**
 * @ORM\Entity
 * @ORM\Table  (name="order_klarna_transaction", indexes={
 *     @ORM\Index (name="order_id", columns={"order_id"})
 *  })
 *
 */
class OrderKlarna extends \XLite\Model\AEntity
{
    /**
     * Order unique id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Order id
     *
     * @var Order
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Order", cascade={"all"})
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Order id in klarna service
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $klarna_order_id;

    /**
     * Fraud status in klarna service
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    protected $fraud_status;

    /**
     * Authorized payment method
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    protected $authorized_payment_method;

    /**
     * Relation to a klarna refund
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActKlarna\Model\OrderKlarnaRefund", mappedBy="klarna", cascade={"all"})
     */
    protected $refunds;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->refunds = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return void
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getKlarnaOrderId(): string
    {
        return $this->klarna_order_id;
    }

    /**
     * @param mixed $klarna_order_id
     *
     * @return void
     */
    public function setKlarnaOrderId(string $klarna_order_id): void
    {
        $this->klarna_order_id = $klarna_order_id;
    }

    /**
     * @return string
     */
    public function getFraudStatus(): string
    {
        return $this->fraud_status;
    }

    /**
     * @param string $fraud_status
     *
     * @return void
     */
    public function setFraudStatus(string $fraud_status): void
    {
        $this->fraud_status = $fraud_status;
    }

    /**
     * @return string
     */
    public function getAuthorizedPaymentMethod(): string
    {
        return $this->authorized_payment_method;
    }

    /**
     * @param string $authorized_payment_method
     *
     * @return void
     */
    public function setAuthorizedPaymentMethod(string $authorized_payment_method): void
    {
        $this->authorized_payment_method = $authorized_payment_method;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRefunds(): ArrayCollection
    {
        return $this->refunds;
    }

    /**
     * @param \Qualiteam\SkinActKlarna\Model\OrderKlarnaRefund $refund
     */
    public function addRefunds(OrderKlarnaRefund $refund): void
    {
        $this->refunds[] = $refund;
    }
}