<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Tabs;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo\Subscription as SubscriptionRepo;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;

/**
 * Tabs related to user profile section
 *
 * @Extender\Mixin
 */
abstract class Account extends \XLite\View\Tabs\Account
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'x_payments_subscription';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if ($this->isLogged() && $this->getProfile()->hasOldXpaymentsSubscriptions()) {
            $tabs['x_payments_subscription'] = [
                'weight'   => 900,
                'title'    => static::t('Subscriptions'),
                'template' => 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscriptions.twig',
            ];
        }

        return $tabs;
    }
}
