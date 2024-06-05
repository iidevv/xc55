<?php /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Admin;

use Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Plan;
use Qualiteam\SkinActXPaymentsSubscriptions\View\Model\SubscriptionPlan;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * Product
 *
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Subscription plan page key
     */
    const PAGE_SUBSCRIPTION_PLAN = 'subscription_plan';

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

        if (!$this->isNew()) {
            $list[static::PAGE_SUBSCRIPTION_PLAN] = static::t('Subscription plan');
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
            $list[static::PAGE_SUBSCRIPTION_PLAN] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/product/body.twig';
        }

        return $list;
    }

    /**
     * Get subscription plan form model
     *
     * @return SubscriptionPlan
     */
    protected function getSubscriptionPlanFormModel()
    {
        return new SubscriptionPlan();
    }

    /**
     * Save subscription plan
     *
     * @return void
     */
    protected function doActionSaveSubscriptionPlan()
    {
        $this->getSubscriptionPlanFormModel()->updateSubscriptionPlan();
    }

    /**
     * Return description for posted subscription plan
     *
     * @return void
     */
    protected function doActionUpdatePlanFieldView()
    {
        $subscriptionPlan = new \Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan();
        $plan = Request::getInstance()->plan;

        $subscriptionPlan->setPlan($plan);

        $planField = new Plan();
        $planField->setValue($subscriptionPlan->getPlan());

        print ($planField->getWidget()->getContent());

        exit (0);
    }
}
