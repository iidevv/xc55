<?php

namespace Iidev\StripeSubscriptions\View;

use XCart\Extender\Mapping\ListChild;
/**
 * @ListChild (list="center", zone="customer")
 */
class SubscriptionPage extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {

        $list = parent::getAllowedTargets();
        $list[] = 'subscription_page';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Iidev/StripeSubscriptions/page/main.twig';
    }
}