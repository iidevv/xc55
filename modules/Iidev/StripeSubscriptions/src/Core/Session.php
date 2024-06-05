<?php

namespace Iidev\StripeSubscriptions\Core;

use \XLite\Core\Config;
use \Stripe\Stripe;
use \Stripe\Checkout\Session as StripeSession;
use \Stripe\BillingPortal\Session as StripeAccountSession;
use XLite\Core\Database;

use XLite\InjectLoggerTrait;

class Session
{
    use InjectLoggerTrait;
    protected $profile;
    protected $returnUrl;
    protected $successUrl;

    public function __construct(\XLite\Model\Profile $profile, string $returnUrl = '', $successUrl = '')
    {
        $this->profile = $profile;
        $this->returnUrl = $returnUrl;
        $this->successUrl = $successUrl;
    }
    protected function getApiKey()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->secret_key;
    }

    protected function setApiKey()
    {
        Stripe::setApiKey($this->getApiKey());
    }


    /**
     * @param string $customerId The Stripe Customer ID.
     * @return \Stripe\Customer|null Returns the customer object or null if not found.
     */
    public function retrieveCustomer($customerId)
    {
        try {
            return \Stripe\Customer::retrieve($customerId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $this->getLogger("StripeSubscriptions")->error('Stripe\Customer: ' . $e->getMessage());
            return null;
        }
    }
    public function createSession()
    {
        $this->setApiKey();

        $subscription = Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'customerId' => $this->profile->getProfileId()
        ]);
        $returnUrl = $this->returnUrl;
        $successUrl = $this->successUrl;

        $data = [
            'success_url' => $successUrl,
            'cancel_url' => $returnUrl,
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => 'price_1PC7iKRp9qylIqdZAFDLddbH',
                    'quantity' => 1,
                ]
            ]
        ];

        if ($this->profile->isMembershipMigrationProfile()) {
            $data['subscription_data'] = [
                'trial_end' => $this->profile->getMembershipMigrationProfileExpirationDate() + 5256000
            ];
        }
        
        $stripeCustomer = ($subscription && $subscription->getStripeCustomerId()) ? $this->retrieveCustomer($subscription->getStripeCustomerId()) : null;

        if ($subscription && $subscription->getStripeCustomerId() && $stripeCustomer && !$stripeCustomer['deleted']) {
            $data['customer'] = $subscription->getStripeCustomerId();
        } else {
            $data['customer_email'] = $this->profile->getLogin();
        }

        return StripeSession::create($data);
    }

    public function createAccountSession()
    {
        $this->setApiKey();
        $returnUrl = $this->returnUrl;

        $profileId = $this->profile->getProfileId();

        /** @var \Iidev\StripeSubscriptions\Model\StripeSubscriptions $subscription */
        $subscription = Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'customerId' => $profileId
        ]);

        return StripeAccountSession::create([
            'customer' => $subscription->getStripeCustomerId(),
            'return_url' => $returnUrl,
        ]);
    }
}