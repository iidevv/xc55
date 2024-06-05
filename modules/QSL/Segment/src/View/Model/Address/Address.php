<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\View\Model\Address;

use XCart\Extender\Mapping\Extender;

/**
 * Profile model widget
 * @Extender\Mixin
 */
class Address extends \XLite\View\Model\Address\Address
{
    /**
     * @inheritdoc
     */
    protected function postprocessSuccessAction()
    {
        parent::postprocessSuccessAction();

        if (in_array($this->currentAction, ['update'])) {
            // Update address
            $profile = $this->getModelObject()->getProfile();

            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();
            if ($address->getAddressId() == $this->getModelObject()->getAddressId()) {
                \QSL\Segment\Core\Mediator::getInstance()
                    ->doUpdateProfile($profile, $this->getModelObject());
            }
        }
    }
}
