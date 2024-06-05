<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

/**
 * General store settings
 */
class General extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'enable_anonymous_checkout'   => [
                static::CONFIG_FIELD_NAME => 'force_login_before_checkout',
            ],
            'buynow_with_options_enabled' => [
                static::CONFIG_FIELD_NAME => 'force_choose_product_options',
            ],
            'redirect_to_cart'            => [
                static::CONFIG_FIELD_NAME => 'redirect_to_cart',
            ],
            'membership_signup'           => [
                static::CONFIG_FIELD_NAME => 'allow_membership_request',
            ],

            /*'currency_symbol' => [
                static::CONFIG_FIELD_NAME => 'shop_currency',
            ],*/

            'minimal_order_amount' => [
                static::CONFIG_FIELD_NAME => 'minimal_order_amount',
            ],
            'maximum_order_amount' => [
                static::CONFIG_FIELD_NAME => 'maximal_order_amount',
            ],
            'maximum_order_items'  => [
                static::CONFIG_FIELD_NAME => 'default_purchase_limit',
            ],

            'show_outofstock_products'  => [
                static::CONFIG_FIELD_NAME => 'show_out_of_stock_products',
            ],

            'default_customer_language' => [
                static::CONFIG_FIELD_NAME => 'default_language',
            ],
            'default_admin_language'    => [
                static::CONFIG_FIELD_NAME => 'default_admin_language',
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldForceLoginBeforeCheckoutValue($value)
    {
        return $value === 'Y' ? 'N' : 'Y';
    }

    public function normalizeFieldShowOutOfStockProductsValue($value)
    {
        return $value === 'Y' ? 'everywhere' : 'directLink';
    }

    public function normalizeFieldShopCurrencyValue($value)
    {
        $code = 'USD';

        switch ($value) {
            case '$':
                $code = 'USD';
                break;
            case '£':
                $code = 'GBP';
                break;
            case '€':
                $code = 'EUR';
                break;
            case '¥':
                $code = 'JPY';
                break;
            case '₽':
            case 'р.':
            case 'руб.':
            case 'руб':
                $code = 'RUB';// Currently Disabled https://xcn.myjetbrains.com/youtrack/issue/MW-28#focus=streamItem-73-53904.0-0
                break;
        }

        $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->findOneBy(['code' => $code]);

        if ($currency) {
            return $currency->getCurrencyId();
        }

        return \XLite\Core\Config::getInstance()->General->shop_currency;
    }

    public function normalizeFieldMaximalOrderAmountValue($value)
    {
        $max = (int) $value;

        return $max === 0 ? 999999 : $max;
    }

    public function normalizeFieldDefaultPurchaseLimitValue($value)
    {
        $max = (int) $value;

        return $max === 0 ? 999999 : $max;
    }

    public function normalizeFieldDefaultAdminLanguageValue($value)
    {
        $value = strtolower($value);
        if ($value == 'us') {
            $value = 'en';
        }

        if (version_compare(static::getPlatformVersion(), '4.5.0') < 0) {
            $value = 'en';
        }

        return $value;
    }

    public function normalizeFieldDefaultLanguageValue($value)
    {
        $value = strtolower($value);
        if ($value == 'us') {
            $value = 'en';
        }

        if (version_compare(static::getPlatformVersion(), '4.5.0') < 0) {
            $value = 'en';
        }

        return $value;
    }

    // }}} </editor-fold>
}
