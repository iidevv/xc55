<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Relation to a klarna order
     *
     * @var \Qualiteam\SkinActKlarna\Model\OrderKlarna
     *
     * @ORM\OneToOne   (targetEntity="Qualiteam\SkinActKlarna\Model\OrderKlarna")
     * @ORM\JoinColumn (name="order_klarna_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $order_klarna;

    /**
     * @var string
     */
    protected string $auth = '';

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAuth(string $value): void
    {
        $this->auth = $value;
    }

    /**
     * @return string
     */
    public function getAuth(): string
    {
        return $this->auth;
    }

    /**
     * @return \Qualiteam\SkinActKlarna\Model\OrderKlarna|null
     */
    public function getOrderKlarna(): ?OrderKlarna
    {
        return $this->order_klarna;
    }

    /**
     * @param \Qualiteam\SkinActKlarna\Model\OrderKlarna $order_klarna
     *
     * @return void
     */
    public function setOrderKlarna(OrderKlarna $order_klarna): void
    {
        $this->order_klarna = $order_klarna;
    }
}