<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Paypal\Service;

use Doctrine\ORM\ORMException;
use XcartGraphqlApi\Types\Enum\AddressTypeEnumType;
use XLite\Core\Database;
use XLite\Model\Address;
use CDev\Paypal\Model\Payment\Processor\ExpressCheckout;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Operation\UpdateAddress;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CartServiceException;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\InvalidAmountException;
use Qualiteam\SkinActGraphQLApi\Model\Cart;
use Qualiteam\SkinActGraphQLApi\Model\Order;
use Qualiteam\SkinActGraphQLApi\Model\Profile;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("CDev\Paypal")
 *
 */

class CartService extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService
{
    /**
     * @param \XLite\Model\Cart           $cart
     * @param \XLite\Model\Payment\Method $method
     *
     * @throws \Exception
     */
    public function changePaymentMethod(\XLite\Model\Cart $cart, \XLite\Model\Payment\Method $method)
    {
        \XLite\Core\Session::getInstance()->ec_type
            = ExpressCheckout::EC_TYPE_MARK;

        parent::changePaymentMethod($cart, $method);
    }
}
