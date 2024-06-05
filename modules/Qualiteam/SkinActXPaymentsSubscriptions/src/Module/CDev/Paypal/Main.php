<?php

namespace Qualiteam\SkinActXPaymentsSubscriptions\Module\CDev\Paypal;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\SubscriptionPlan;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;

/**
 * This is a workaround to hide the Buy now buttons from PayPal, if the subscriptions are configured in the store.
 * Because currently we cannot hide button for the specific product, and the subscriptions must be paid by X-Payments methods.
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Paypal")
 */
class Main extends \CDev\Paypal\Main
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

                $cnd                                    = new CommonCell();
                $cnd->{SubscriptionPlan::SEARCH_ACTIVE} = true;

                // Allow Buy now buttons only if there are no active subscription plans
                $result = !Database::getRepo(\Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan::class)
                    ->search($cnd, true);
            }
        }

        return $result;
    }
}
