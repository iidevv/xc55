<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Profile dialog
 *
 * @Extender\Mixin
 */
abstract class AdminProfile extends \XLite\View\Tabs\AdminProfile implements \XLite\Base\IDecorator
{
    const PAYMENT_CARDS_TARGET = 'xpayments_cards';
    const SUBSCRIPTIONS_TARGET = 'xpayments_user_subscriptions';

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        if (static::isXpaymentsEnabled()) {
            $list[] = static::PAYMENT_CARDS_TARGET;
            $list[] = static::SUBSCRIPTIONS_TARGET;
        }

        return $list;
    }

    /**
     * Get customer profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        $profileId = \XLite\Core\Request::getInstance()->profile_id;
        if (empty($profileId)) {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->find(intval($profileId));
    }

    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if (
            !$this->getCustomerProfile()->getAnonymous()
            && static::isXpaymentsEnabled()
        ) {
            $tabs[static::PAYMENT_CARDS_TARGET] = array(
                'weight'   => 1100,
                'title'    => static::t('Saved cards'),
                'template' => 'modules/XPay/XPaymentsCloud/account/xpayments_cards.twig',
            );

            $profile = $this->getProfile();

            if (
                XPaymentsHelper::isSubscriptionManagementEnabled()
                && $profile
                && $profile->hasXpaymentsSubscriptions()
            ) {
                $tabs[static::SUBSCRIPTIONS_TARGET] = array(
                    'weight'   => 1400,
                    'title'    => static::t('X-Payments subscriptions'),
                    'template' => 'modules/XPay/XPaymentsCloud/profile/subscription.twig',
                );
            }
        }

        return $tabs;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (static::isXpaymentsEnabled()) {
            $list['css'][] = 'modules/XPay/XPaymentsCloud/account/cc_type_sprites.css';
            $list['css'][] = 'modules/XPay/XPaymentsCloud/account/xpayments_cards.less';
        }

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (static::isXpaymentsEnabled()) {
            $list[] = 'modules/XPay/XPaymentsCloud/account/xpayments_cards.admin.css';
        }

        return $list;
    }

    /**
     * Check if X-Payment Cloud payment method is enabled
     *
     * @return bool
     */
    protected static function isXpaymentsEnabled()
    {
        return XPaymentsHelper::getPaymentMethod()
            && XPaymentsHelper::getPaymentMethod()->isEnabled();
    }
}
