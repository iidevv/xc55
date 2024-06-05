<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class method
 *
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Shipping\Method
{
    /**
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $aftership_couriers;

    /**
     * @return string
     */
    public function getAftershipCouriers(): ?string
    {
        return $this->aftership_couriers;
    }

    /**
     * @param string $aftershipCouriers
     */
    public function setAftershipCouriers(string $aftershipCouriers): void
    {
        $this->aftership_couriers = $aftershipCouriers;
    }
}
