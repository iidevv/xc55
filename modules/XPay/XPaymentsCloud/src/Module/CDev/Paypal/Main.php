<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Module\CDev\Paypal;

use XCart\Extender\Mapping\Extender;

/**
 * This is a workaround to hide the Buy now buttons from PayPal for specific subscription product
 *
 * @Extender\Depend("CDev\Paypal")
 */
abstract class Main extends \XLite\Module\CDev\Paypal\Main implements \XLite\Base\IDecorator
{
    /**
     * Returns BuyNow button availability status
     *
     * @return boolean
     */
    public static function isBuyNowEnabled()
    {
        static $result;

        if (!isset($result)) {

            $result = parent::isBuyNowEnabled();

            if ($result) {

                $controller = \XLite::getController();

                if ($controller instanceof \XLite\Controller\Customer\Product) {
                    $product = $controller->getProduct();

                    if ($product) {
                        $result = !$product->hasXpaymentsSubscriptionPlan();
                    }
                }
            }
        }

        return $result;
    }
}
