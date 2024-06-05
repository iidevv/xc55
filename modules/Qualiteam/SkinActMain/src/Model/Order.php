<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{

    /**
     * Order delivered timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $deliveredDate;

    public function processSkinactDelivered()
    {
        $this->setDeliveredDate(\XLite\Core\Converter::time());
    }

    /**
     * @return int
     */
    public function getDeliveredDate()
    {
        return $this->deliveredDate;
    }

    /**
     * @param int $deliveredDate
     */
    public function setDeliveredDate($deliveredDate)
    {
        $this->deliveredDate = $deliveredDate;
    }


}