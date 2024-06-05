<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Model\Shipping;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\ShippingMethod;

/**
 * Payment method
 * @Extender\Mixin
 */
abstract class Method extends \XLite\Model\Shipping\Method
{
    /**
     * @param boolean $value
     */
    public function setAdded($value)
    {
        $changed = $this->getAdded() !== (bool) $value;

        parent::setAdded($value);

        if ($this->isPersistent() && $changed && ($this->getModuleName() || $this->getProcessor() === 'offline')) {
            Mediator::getInstance()->addMessage(
                new ShippingMethod(
                    $value ? 'Add Shipping Method' : 'Remove Shipping Method',
                    $this
                )
            );
        }
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Method
     */
    public function setEnabled($enabled)
    {
        $changed = $this->getEnabled() !== (bool) $enabled;

        parent::setEnabled($enabled);

        if ($this->isPersistent() && $changed && $this->getAdded() && ($this->getModuleName() || $this->getProcessor() === 'offline')) {
            Mediator::getInstance()->addMessage(
                new ShippingMethod(
                    $enabled ? 'Enable Shipping Method' : 'Disable Shipping Method',
                    $this
                )
            );
        }
    }
}
