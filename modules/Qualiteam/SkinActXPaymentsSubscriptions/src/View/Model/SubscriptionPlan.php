<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Model;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Plan;
use XLite\Core\Database;
use XLite\Model\AEntity;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;
use XLite\View\FormField\Input\Checkbox\OnOff;
use XLite\View\FormField\Input\Text\Base\Numeric;
use XLite\View\FormField\Input\Text\Integer;
use XLite\View\FormField\Input\Text\Price;
use XLite\View\Model\AModel;

/**
 * XPaymentsSubscriptions Subscription Plan form model
 */
class SubscriptionPlan extends AModel
{
    /**
     * schemeDefault
     *
     * @var array
     */
    protected $schemaDefault = [
        'subscription'       => [
            self::SCHEMA_CLASS     => OnOff::class,
            self::SCHEMA_LABEL     => 'This is subscription product',
            OnOff::PARAM_ON_LABEL  => 'Yes',
            OnOff::PARAM_OFF_LABEL => 'No',
        ],
        'setup_fee'          => [
            self::SCHEMA_CLASS => Price::class,
            self::SCHEMA_LABEL => 'Setup fee',
            self::SCHEMA_HELP  => 'The product\'s selling price will be calculated as Setup Fee + Subscription Fee. If you do not intend to charge any subscription setup fee to the buyer, set the Setup fee to "0" (zero); in this case, the product\'s selling price will equal the Subscription fee.',
        ],
        'fee'                => [
            self::SCHEMA_CLASS    => Price::class,
            self::SCHEMA_LABEL    => 'Subscription fee',
            self::SCHEMA_HELP     => 'Amount the buyer will need to pay for each period for the duration of the subscription. If subscription fee is set to "0" (zero), buyers will not be charged when creating recurring orders.',
            self::SCHEMA_REQUIRED => true,
            Numeric::PARAM_MIN    => ASubscriptionPlan::MIN_FEE_VALUE,
        ],
        'plan'               => [
            self::SCHEMA_CLASS => Plan::class,
            self::SCHEMA_LABEL => 'Plan',
        ],
        'periods'            => [
            self::SCHEMA_CLASS => Integer::class,
            self::SCHEMA_LABEL => 'Re-bill periods',
            self::SCHEMA_HELP  => 'Number of times that the buyer should be re-billed for this product after the initial payment. Set to "0" (zero) if you need an infinite subscription.',
        ],
        'calculate_shipping' => [
            self::SCHEMA_CLASS     => OnOff::class,
            self::SCHEMA_LABEL     => 'Calculate shipping for recurring orders',
            OnOff::PARAM_ON_LABEL  => 'Yes',
            OnOff::PARAM_OFF_LABEL => 'No',
        ],
    ];

    /**
     * Update subscription plan
     *
     * @return void
     */
    public function updateSubscriptionPlan()
    {
        $product = $this->getProduct();
        $subscriptionPlan = $product->getSubscriptionPlan();

        $repo = Database::getRepo(\Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan::class);
        $data = $this->getRequestData();

        if (ASubscriptionPlan::MIN_FEE_VALUE > floatval($data['fee'])) {
            $data['subscription'] = false;
        }

        if (empty($data['plan']['reverse'])) {
            $data['plan']['reverse'] = false;
        }

        if (is_null($subscriptionPlan)) {
            $data['product'] = $product;

            $repo->insert($data);

        } else {
            $repo->update($subscriptionPlan, $data);
        }
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new Submit(
            [
                AButton::PARAM_LABEL => 'Save',
                AButton::PARAM_STYLE => 'action',
            ]
        );

        return $result;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return AEntity
     */
    protected function getDefaultModelObject()
    {
        $product = $this->getProduct();

        $subscriptionPlan = is_null($product)
            ? null
            : $product->getSubscriptionPlan();

        if (!is_null($product) && is_null($subscriptionPlan)) {
            $subscriptionPlan = new \Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan();
            $subscriptionPlan->setSetupFee($product->getPrice());
            $subscriptionPlan->setCalculateShipping(true);
        }

        return $subscriptionPlan;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return \Qualiteam\SkinActXPaymentsSubscriptions\View\Form\SubscriptionPlan::class;
    }
}
