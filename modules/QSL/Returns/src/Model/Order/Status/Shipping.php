<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model\Order\Status;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Decorated Shipping status model
 * @Extender\Mixin
 */
abstract class Shipping extends \XLite\Model\Order\Status\Shipping
{
    /**
     * Whether customers can request a return for their orders having this status.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true, options={ "default": NULL })
     */
    protected $isReturnRequestAllowed = null;

    /**
     * Updates the flag that determines if customers can request a return for
     * their orders having this status.
     *
     * @param boolean $value New value
     *
     * @return $this
     */
    public function setIsReturnRequestAllowed($value)
    {
        $this->isReturnRequestAllowed = $value;

        return $this;
    }

    /**
     * Check if the status allows customers to request a return.
     *
     * @return boolean
     */
    public function getIsReturnRequestAllowed()
    {
        $result = $this->isReturnRequestAllowed;

        // Fallback to the default logic if store administors didn't configure statuses
        if (!isset($result) || is_null($result)) {
            $result = $this->getDefaultIsRequestAllowed();
        }

        return $result;
    }

    /**
     * Check if the status allows customers to request a return.
     *
     * @return boolean
     */
    public function isReturnRequestAllowed()
    {
        return $this->getDefaultIsRequestAllowed();
    }

    /**
     * A fallback logic that checks if the status allows customers to request a return.
     *
     * @return boolean
     */
    protected function getDefaultIsRequestAllowed()
    {
        $code = $this->getCode();

        return ($code === \XLite\Model\Order\Status\Shipping::STATUS_SHIPPED)
            || ($code === \XLite\Model\Order\Status\Shipping::STATUS_DELIVERED);
    }
}
