<?php

namespace Iidev\StripeSubscriptions\Controller\Customer;

use \XLite\Core\Config;
use XLite\Core\Database;

class SubscriptionPage extends \XLite\Controller\Customer\ACustomer
{
    public function getImageUrl()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->image_url;
    }
    public function getPageTitle()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->title;
    }

    public function getPrice()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->price;
    }

    public function getShortDescription()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->short_description;
    }

    public function getDescription()
    {
        return Config::getInstance()->Iidev->StripeSubscriptions->description;
    }

    public function getExpirationDate() {
        $profile = $this->getProfile();
        $subscription = Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'customerId' => $profile->getProfileId()
        ]);
        return date('F d, Y', $subscription->getExpirationDate());
    }
    public function getStatus() {
        $profile = $this->getProfile();
        $subscription = Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'customerId' => $profile->getProfileId()
        ]);
        return $subscription->getStatus();
    }

    public function isSubscriptionExist() {
        $profile = $this->getProfile();
        return Database::getRepo('Iidev\StripeSubscriptions\Model\StripeSubscriptions')->findOneBy([
            'customerId' => $profile->getProfileId()
        ]);
    }
    public function isLogged()
    {
        return \XLite\Core\Auth::getInstance()->isLogged();
    }

    public function displayScriptData()
    {
        $data = [
            "url_params" => [
                'target' => 'login',
                'widget' => '\XLite\View\Authorization',
                'fromURL' => \XLite::getController()->getURL(),
                'popup' => '1',
            ]
        ];

        echo ('<script type="text/x-cart-data">' . "\r\n" . json_encode($data) . "\r\n" . '</script>' . "\r\n");
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }
}