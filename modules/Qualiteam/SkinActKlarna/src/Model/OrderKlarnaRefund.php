<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table  (name="order_klarna_refunds", indexes={
 *     @ORM\Index (name="klarna_id", columns={"klarna_id"})
 *  })
 */
class OrderKlarnaRefund extends \XLite\Model\AEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $id;

    /**
     * @var \Qualiteam\SkinActKlarna\Model\OrderKlarna
     *
     * @ORM\ManyToOne   (targetEntity="Qualiteam\SkinActKlarna\Model\OrderKlarna", inversedBy="refunds")
     * @ORM\JoinColumn (name="klarna_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $klarna;

    /**
     * @var int
     *
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $refund_id;

    /**
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $url;

    /**
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $amount = 0.0000;

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderKlarna(): OrderKlarna
    {
        return $this->klarna;
    }

    public function setOrderKlarna(OrderKlarna $klarna): void
    {
        $this->klarna = $klarna;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): void
    {
        $this->date = $date;
    }

    public function getRefundId(): string
    {
        return $this->refund_id;
    }

    public function setRefundId(string $refund_id): void
    {
        $this->refund_id = $refund_id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
}