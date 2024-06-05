<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Address book (at add new card page)
 *
 * @Extender\Mixin
 */
class AddressBook extends \XLite\Controller\Customer\AddressBook
{
    /**
     * Save address
     *
     * @return boolean
     */
    protected function doActionSave()
    {
        $result = parent::doActionSave();

        if (
            $result
            && $this->getModelForm()->getModelObject()
            && $this->getModelForm()->getModelObject()->getAddressId()
        ) {

            // New address is not yet saved in profile
            Database::getEM()->flush();

            $addresses = $this->getProfile()->getAddresses();

            foreach ($addresses as $address) {
                if ($this->getModelForm()->getModelObject()->getAddressId() == $address->getAddressId()) {
                    $address->setIsBilling(true);
                } else {
                    $address->setIsBilling(false);
                }
            }

            // For those, who doesn't understand from the first time
            $this->getModelForm()->getModelObject()->setIsBilling(true);

            Database::getEM()->flush();
        }

        return $result;
    }
}
