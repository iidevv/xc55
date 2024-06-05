<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\Model\Shipping;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Shipping\Method
{
    /**
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $instructions;

    /**
     * @return string
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * @param string $instructions
     */
    public function setInstructions($instructions)
    {
        $this->instructions = (string)$instructions;
    }

}