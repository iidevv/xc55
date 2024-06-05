<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Product
 *
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Subscription plan page key
     */
    const PAGE_XPAYMENTS_SUBSCRIPTION_PLAN = 'xpayments_subscription_plan';

    /**
     * Add non secure action (get plan description)
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['update_plan_field_view']);
    }

    /**
     * Add tab for subscription plan
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (
            XPaymentsHelper::isSubscriptionManagementEnabled()
            && !$this->isNew()
        ) {
            $list[static::PAGE_XPAYMENTS_SUBSCRIPTION_PLAN] = static::t('Subscription plan');
        }

        return $list;
    }

    /**
     * Add tab template for subscription plan
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list[static::PAGE_XPAYMENTS_SUBSCRIPTION_PLAN] = 'modules/XPay/XPaymentsCloud/product/body.twig';
        }

        return $list;
    }

    /**
     * Get subscription plan form model
     *
     * @return \XPay\XPaymentsCloud\View\Model\SubscriptionPlan
     */
    protected function getXpaymentsSubscriptionPlanFormModel()
    {
        return new \XPay\XPaymentsCloud\View\Model\SubscriptionPlan();
    }

    /**
     * Save subscription plan
     *
     * @return void
     */
    protected function doActionSaveXpaymentsSubscriptionPlan()
    {
        $this->getXpaymentsSubscriptionPlanFormModel()->updateSubscriptionPlan();
    }

    /**
     * Return description for posted subscription plan
     *
     * @return void
     */
    protected function doActionUpdatePlanFieldView()
    {
        $subscriptionPlan = new \XPay\XPaymentsCloud\Model\Subscription\Plan();
        $plan = \XLite\Core\Request::getInstance()->plan;

        $subscriptionPlan->setPlan($plan);

        $planField = new \XPay\XPaymentsCloud\View\FormField\Plan();
        $planField->setValue($subscriptionPlan->getPlan());

        print($planField->getWidget()->getContent());

        exit(0);
    }

}
