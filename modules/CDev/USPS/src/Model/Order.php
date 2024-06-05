<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * USPS Shipments
     *
     * @var \Doctrine\Common\Collections\Collection|\CDev\USPS\Model\Shipment[]
     *
     * @ORM\OneToMany (targetEntity="CDev\USPS\Model\Shipment", mappedBy="order", cascade={"all"})
     */
    protected $uspsShipment;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->uspsShipment = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Shipment[]
     */
    public function getUspsShipment()
    {
        return $this->uspsShipment;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|Shipment[] $uspsShipment
     */
    public function setUspsShipment($uspsShipment)
    {
        $this->uspsShipment = $uspsShipment;
    }
}
