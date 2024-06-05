<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFixes\View\AccountBlocks\MembershipsAndSubscriptions;

use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\MembershipsAndSubscriptions\MembershipsAndSubscriptions;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\Request;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;
use XLite\Model\Profile;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"Qualiteam\SkinActYourAccountPage"})
 * @Extender\After ("XPay\XPaymentsCloud")
 */
class MembershipsAndSubscriptionsSubscriptions extends MembershipsAndSubscriptions
{
    /**
     * User profile object
     *
     * @var \XLite\Model\Profile
     */
    protected $profile;

    public function getProfile()
    {
        if ($this->profile === null) {
            $profileId = Request::getInstance()->profile_id;

            $this->profile = $profileId === null
                ? Auth::getInstance()->getProfile()
                : Database::getRepo(Profile::class)->find($profileId);
        }

        return $this->profile;
    }

    protected static function isXpaymentsEnabled()
    {
        return XPaymentsHelper::getPaymentMethod()
            && XPaymentsHelper::getPaymentMethod()->isEnabled();
    }

    protected function getBlockLinks(): array
    {
        $links = parent::getBlockLinks();

        foreach ($links as &$link) {
            if (isset($link['url']) && str_contains($link['url'], '?target=x_payments_subscription')) {
                $preserveOldLink = $this->getProfile()
                    && $this->getProfile()->hasOldXpaymentsSubscriptions();

                if (!$preserveOldLink) {
                    $link['url'] = $this->buildURL('xpayments_subscriptions');
                }
            }

            if (isset($link['url']) && str_contains($link['url'], '?target=saved_cards')) {
                $link['url'] = $this->buildURL('xpayments_cards');
            }
        }

        return $links;
    }
}
