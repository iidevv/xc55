<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Model\Shipping;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shipping method model
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Shipping\Method
{
    /**
     * Special code values for free ship and fixed fee methods
     */
    public const METHOD_TYPE_FREE_SHIP = 'FREESHIP';
    public const METHOD_TYPE_FIXED_FEE = 'FIXEDFEE';

    /**
     * Whether the method is free or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $free = false;

    /**
     * Set free
     *
     * @param boolean $free
     * @return Method
     */
    public function setFree($free)
    {
        $this->free = $free;
        return $this;
    }

    /**
     * Get free
     *
     * @return boolean
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * Return true if method is 'Freight fixed fee'
     *
     * @return boolean
     */
    public function isFixedFee()
    {
        return $this->getCode() === self::METHOD_TYPE_FIXED_FEE
            && $this->getProcessor() === 'offline';
    }

    public function canBeEstimated(): bool
    {
        return parent::canBeEstimated()
           && !in_array(
               $this->getCode(),
               [static::METHOD_TYPE_FIXED_FEE, static::METHOD_TYPE_FREE_SHIP]
           );
    }
}
