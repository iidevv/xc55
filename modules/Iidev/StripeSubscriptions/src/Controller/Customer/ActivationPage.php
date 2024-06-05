<?php

namespace Iidev\StripeSubscriptions\Controller\Customer;

class ActivationPage extends \XLite\Controller\Customer\ACustomer
{
    /**
     * @return bool
     */
    public function isCartEmpty(): bool
    {
        return $this->getCart()->isEmpty();
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
}