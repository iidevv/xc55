<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout controller
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * @inheritdoc
     */
    protected function doActionUpdateProfile()
    {
        $old = null;
        $valid = \QSL\Segment\Core\Mediator::getInstance()->isValid();
        if ($valid && $this->getCart()->getProfile()) {
            $old = \QSL\Segment\Core\Mediator::getInstance()
                ->getProfileFingerprint($this->getCart()->getProfile());
        }

        parent::doActionUpdateProfile();

        if ($valid && $this->getCart()->getProfile()) {
            $new = \QSL\Segment\Core\Mediator::getInstance()
                ->getProfileFingerprint($this->getCart()->getProfile());
            if ($old != $new) {
                \QSL\Segment\Core\Mediator::getInstance()->doUpdateProfile($this->getCart()->getProfile());
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function doActionShipping()
    {
        $shippingId = $this->getCart()->getShippingId();

        parent::doActionShipping();

        if ($this->valid && $this->getCart()->getShippingId() != $shippingId) {
            \QSL\Segment\Core\Mediator::getInstance()
                ->doChangeShipping($this->getCart()->getShippingId(), $shippingId);
        }
    }

    /**
     * @inheritdoc
     */
    protected function doActionPayment()
    {
        $id = $this->getCart()->getFirstOpenPaymentTransaction()
            ? $this->getCart()->getFirstOpenPaymentTransaction()->getPaymentMethod()->getMethodId()
            : null;

        parent::doActionPayment();

        $newid = $this->getCart()->getFirstOpenPaymentTransaction()
            ? $this->getCart()->getFirstOpenPaymentTransaction()->getPaymentMethod()->getMethodId()
            : null;

        if ($this->valid && $newid != $id) {
            \QSL\Segment\Core\Mediator::getInstance()
                ->doChangePayment($newid, $id);
        }
    }
}
