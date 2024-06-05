<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Module\XC\MultiVendor\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use XC\Stripe\Main;

/**
 * Administrator profile model widget. This widget is used in the admin interface
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class FinancialInfo extends \XC\MultiVendor\View\Model\Profile\FinancialInfo
{
    public const SECTION_STRIPE_CONNECT_ACCOUNT = 'stripeConnectAccount';

    /**
     * @return array
     */
    protected function getFinancialInfoSections()
    {
        $sections = parent::getFinancialInfoSections();

        $stripeConnectMethod = Main::getStripeConnectMethod();
        if (
            $stripeConnectMethod->getAdded()
            && $stripeConnectMethod->getProcessor()
            && $stripeConnectMethod->getProcessor()->isConfigured($stripeConnectMethod)
        ) {
            $sections[static::SECTION_STRIPE_CONNECT_ACCOUNT] = static::t('Stripe Connect Account');
        }

        return $sections;
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionStripeConnectAccount()
    {
        $schema = $this->defineStripeConnectAccountSchema();

        return $this->getFieldsBySchema($schema);
    }

    /**
     * @return array
     */
    protected function defineStripeConnectAccountSchema()
    {
        $fields['stripeSellerAccountId'] = [
            static::SCHEMA_CLASS       => 'XC\Stripe\View\FormField\ConnectAccount',
            static::SCHEMA_LABEL       => 'Stripe Account ID',
            static::SCHEMA_REQUIRED    => false,
        ];

        return $fields;
    }
}
