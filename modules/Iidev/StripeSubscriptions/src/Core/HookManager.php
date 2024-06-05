<?php

namespace Iidev\StripeSubscriptions\Core;

use XLite\InjectLoggerTrait;
use Iidev\StripeSubscriptions\Model\StripeSubscriptions;
use XLite\Core\Database;
use XLite\Model\Profile;

class HookManager
{
    use InjectLoggerTrait;

    public function handleSubscription(string $event, array $data)
    {
        $this->getLogger("StripeSubscriptions")->error('Event: ' . $event . '. Data: ' . json_encode($data));

        switch ($event) {
            case 'customer.subscription.created':
                // ??
                break;

            case 'customer.subscription.deleted':
                $this->updateSubscription($data, 'Inactive');
                break;

            case 'invoice.payment_succeeded':
                $this->updateSubscription($data, 'Active');
                break;

            case 'invoice.payment_failed':
                // notify administrator
                break;

            default:
        }
    }

    private function updateSubscription(array $data, string $status): void
    {

        $data['expiration_date'] = $data['lines']['data'][0]['period']['end'] ?? null;

        $subscription = Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'stripeSubscriptionId' => $data['subscription'] ?? $data['id']
        ]);

        if (!$subscription && !empty($data['billing_reason']) && $data['billing_reason'] === 'subscription_create') {
            $subscription = $this->createSubscription($data);
        }

        if (!$subscription) {
            $this->getLogger("StripeSubscriptions")->error('Error: Subscription not defined. Data: ' . json_encode($data));
            return;
        }

        $profileId = $subscription->getCustomerId();
        $profile = Database::getRepo('XLite\Model\Profile')->find($profileId);

        if ($profile && $profile->isMembershipMigrationProfile()) {
            $profile->setMembershipMigrationProfileComplete();
            Database::getEM()->persist($profile);
            Database::getEM()->flush();
        }

        if ($profile) {
            $this->setProMembership($profile, $status);
        } else {
            $this->getLogger("StripeSubscriptions")->error('Error: Profile not defined. Data: ' . json_encode($data));
        }

        $subscription->setExpirationDate($data['expiration_date'] ?? $data['canceled_at']);
        $subscription->setStatus($status);
        $subscription->setPeriods($subscription->getPeriods() + 1);
        Database::getEM()->persist($subscription);
        Database::getEM()->flush();
    }
    private function createSubscription(array $data): StripeSubscriptions|null
    {
        $profile = Database::getRepo('XLite\Model\Profile')->findByLogin($data['customer_email']);

        if (!$profile)
            return null;

        $subscription = Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'customerId' => $profile->getProfileId()
        ]);

        if (!$subscription) {
            $subscription = new StripeSubscriptions();
            $subscription->setPeriods(0);
        } else {
            $subscription->setPeriods($subscription->getPeriods() + 1);
        }

        $subscription->setCustomerId($profile->getProfileId());
        $subscription->setStripeCustomerId($data['customer']);
        $subscription->setStripeSubscriptionId($data['subscription']);
        $subscription->setExpirationDate($data['expiration_date']);
        $subscription->setStatus('Created');

        Database::getEM()->persist($subscription);
        Database::getEM()->flush();

        return $subscription;
    }

    private function setProMembership(Profile $profile, string $status): void
    {
        $membership = null;

        if ($status === 'Active') {
            /** @var \XLite\Model\Membership $membership */
            $membership = Database::getRepo(\XLite\Model\Membership::class)
                ->find(9);

            if (!$membership) {
                $this->getLogger("StripeSubscriptions")->error('Membership not found.');
                return;
            }

            $membership->getLabelId();
        }

        $profile->setMembership($membership);

        Database::getEM()->persist($profile);
        Database::getEM()->flush();
    }

}