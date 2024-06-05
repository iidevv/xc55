<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Customer;

/**
 * PaymentReturn
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn
{
    public function setReturnURL($url)
    {
        $txn = $this->detectTransaction();

        if ($txn
            && $txn->getOrder()
            && (bool)$txn->getOrder()->getApiCartUniqueId()
        ) {
            $this->doHTMLRedirect($url);

        } else {
            parent::setReturnURL($url);
        }
    }
}
