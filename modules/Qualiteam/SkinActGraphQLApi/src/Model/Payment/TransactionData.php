<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Payment;

use Qualiteam\SkinActGraphQLApi\Controller\Customer\GraphqlApiCheckout;

/**
 * Class TransactionData
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Model\Payment
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * 

 */

class TransactionData extends \XLite\Model\Payment\TransactionData
{
    public static $isApiMode = false;

    public function isAvailable()
    {
        return parent::isAvailable()
            || \XLite::getController() instanceof GraphqlApiCheckout
            || static::$isApiMode;
    }
}
