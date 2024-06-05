<?php

namespace Iidev\StripeSubscriptions\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use \XLite\Core\Config;

/**
 * Extends customer base controller to show a message on all customer pages
 * @Extender\Mixin
 */
class StripeSubscription extends \XLite\Controller\Customer\ACustomer
{

    public function getSubscriptionSuccessUrl()
    {
        return $this->buildURL() . "subscription-activation";
    }

    public function getSubscriptionReturnUrl()
    {
        return \XLite::getController()->getURL();
    }

    /**
     * @return bool
     */
    public function isProMembership(): bool
    {
        if ($this->getProfile() && $this->getProfile()->getMembership()) {
            return true;
        }
        return false;
    }
    public function getImageUrl()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->image_url;
    }
}