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
 * X-Payments Saved Cards tab
 *
 * @Extender\Mixin
 */
abstract class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{
    const PAYMENT_CARDS_TARGET = 'xpayments_cards';
    const SUBSCRIPTIONS_TARGET = 'xpayments_subscriptions';

    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
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
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if (
            $this->getProfile()
            && static::isXpaymentsEnabled()
        ) {
            $tabs[static::PAYMENT_CARDS_TARGET] = array(
                 'weight'   => 1200,
                 'title'    => static::t('Saved cards'),
                 'template' => 'modules/XPay/XPaymentsCloud/account/xpayments_cards.twig',
            );

            if (
                XPaymentsHelper::isSubscriptionManagementEnabled()
                && $this->getProfile()->hasXpaymentsSubscriptions()
            ) {
                $tabs[static::SUBSCRIPTIONS_TARGET] = array(
                    'weight'   => 1500,
                    'title'    => static::t('My Subscriptions'),
                    'template' => 'modules/XPay/XPaymentsCloud/subscriptions.twig',
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
