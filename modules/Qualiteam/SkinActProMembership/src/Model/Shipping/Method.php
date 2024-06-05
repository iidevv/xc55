<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Shipping\Method
{

    protected $freeByMembership = false;

    /**
     * @return bool
     */
    public function isFreeByMembership()
    {
        return $this->freeByMembership;
    }

    /**
     * @param bool $freeByMembership
     */
    public function setFreeByMembership($freeByMembership)
    {
        $this->freeByMembership = $freeByMembership;
    }

    public function getHandlingFeeValue()
    {
        if ($this->isFreeByMembership()) {
            return 0;
        }

        return parent::getHandlingFeeValue();
    }


}