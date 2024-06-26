<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Core\Track;

use XLite\Model\Shipping\Method;
use XC\Concierge\Core\ATrack;

class ShippingMethod extends ATrack
{
    /**
     * @var Method
     */
    protected $method;

    /**
     * PaymentMethod constructor.
     *
     * @param string $event
     * @param Method $method
     */
    public function __construct($event, $method)
    {
        $this->event  = $event;
        $this->method = $method;
    }

    /**
     * @return Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param Method $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $method = $this->getMethod();

        return [
            'Shipping Name' => $method->getName(),
            'Module Name'   => $method->getModuleName(),
        ];
    }
}
