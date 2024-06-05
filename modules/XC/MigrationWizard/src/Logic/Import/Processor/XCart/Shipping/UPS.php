<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * UPS
 */
class UPS extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AShipping
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define shipping processor
     *
     * @return string
     */
    public static function defineProcessor()
    {
        return 'XC\UPS\Model\Shipping\Processor\UPS';
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'XC\UPS';
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'UPS_username'       => [
                static::MODULE_FIELD_NAME        => 'userID',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'UPS_password'       => [
                static::MODULE_FIELD_NAME        => 'password',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'UPS_accesskey' => [
                static::MODULE_FIELD_NAME        => 'accessKey',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'UPS_shipper_number'    => [
                static::MODULE_FIELD_NAME        => 'shipper_number',
                static::MODULE_FIELD_IS_VIRTUAL => true,
            ],
            'UPS_packaging_type'    => [ static::MODULE_FIELD_NAME        => 'packaging_type', static::MODULE_FIELD_IS_VIRTUAL => true, ],//selector
            'UPS_pickup_type'    => [ static::MODULE_FIELD_NAME        => 'pickup_type', static::MODULE_FIELD_IS_VIRTUAL => true, ],//selector
            'UPS_dimensions'    => [ static::MODULE_FIELD_NAME        => 'dimensions', static::MODULE_FIELD_IS_VIRTUAL => true, ],//unseriali separated fields
            'UPS_max_weight'    => [ static::MODULE_FIELD_NAME        => 'max_weight', static::MODULE_FIELD_IS_VIRTUAL => true, ],
            'UPS_additional_handling'    => [ static::MODULE_FIELD_NAME        => 'additional_handling', static::MODULE_FIELD_IS_VIRTUAL => true, ],
            'UPS_saturday_pickup'    => [ static::MODULE_FIELD_NAME        => 'saturday_pickup', static::MODULE_FIELD_IS_VIRTUAL => true, ],
            'UPS_saturday_delivery'    => [ static::MODULE_FIELD_NAME        => 'saturday_delivery', static::MODULE_FIELD_IS_VIRTUAL => true, ],
            'UPS_delivery_conf'    => [ static::MODULE_FIELD_NAME        => 'delivery_conf', static::MODULE_FIELD_IS_VIRTUAL => true, ],//selector
            'UPS_negotiated_rates'    => [ static::MODULE_FIELD_NAME        => 'negotiated_rates', static::MODULE_FIELD_IS_VIRTUAL => true, ],
            'UPS_currency_code'    => [ static::MODULE_FIELD_NAME        => 'currency_code', static::MODULE_FIELD_IS_VIRTUAL => true, ],
            'UPS_conversion_rate'    => [ static::MODULE_FIELD_NAME        => 'currency_rate', static::MODULE_FIELD_IS_VIRTUAL => true, ],
        ];
    }


    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * Return TRUE if source data exists
     *
     * @return boolean
     */
    public static function hasTransferableData()
    {
        return Configuration::isModuleEnabled(Configuration::MODULE_UPS_SHIPPING);
    }

    // }}} </editor-fold>

    // {{{ Initializers <editor-fold desc="Initializers" defaultstate="collapsed">

    public static function initFieldShipperNumberValue()
    {
        return '';
    }

    public static function initFieldPickupTypeValue()
    {
        return '01';
    }

    public static function initFieldPackagingTypeValue()
    {
        return '00';
    }

    public static function initFieldDimensionsValue()
    {
        // see normalizeFieldDimensionsValue for order
        return serialize([10, 10, 10]);
    }

    public static function initFieldMaxWeightValue()
    {
        return 150;
    }

    public static function initFieldAdditionalHandlingValue()
    {
        return '';
    }

    public static function initFieldSaturdayPickupValue()
    {
        return '';
    }

    public static function initFieldSaturdayDeliveryValue()
    {
        return '';
    }

    public static function initFieldDeliveryConfValue()
    {
        return '0';
    }

    public static function initFieldNegotiatedRatesValue()
    {
        return '';
    }

    public static function initFieldCurrencyCodeValue()
    {
        return '';
    }

    public static function initFieldCurrencyRateValue()
    {
        return 1;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">


    protected function getCachedDecryptedValue($value, $_function)
    {
        return $this->executeCachedRuntime(static function () use ($value) {
            if (!empty($value)) {
                $value = Configuration::textDecrypt($value);
            }
            return $value;
        }, [$_function, $value]);
    }

    public function normalizeFieldUserIDValue($value)
    {
        return $this->getCachedDecryptedValue($value, __METHOD__);
    }

    public function normalizeFieldPasswordValue($value)
    {
        return $this->getCachedDecryptedValue($value, __METHOD__);
    }

    public function normalizeFieldAccessKeyValue($value)
    {
        return $this->getCachedDecryptedValue($value, __METHOD__);
    }

    public function getUnserializedXC4param00()
    {
        return $this->executeCachedRuntime(static function () {
            $prefix = self::getTablePrefix();
            $_shipper_settings = static::getCellData("SELECT param00 FROM {$prefix}shipping_options WHERE carrier = 'UPS'");
            if (!empty($_shipper_settings)) {
                $_shipper_settings = unserialize($_shipper_settings) ?: [];
            }
            return $_shipper_settings ?:
                [
                    'shipper_number' => static::initFieldShipperNumberValue(),
                    'pickup_type' => static::initFieldPickupTypeValue(),
                    'packaging_type' => static::initFieldPackagingTypeValue(),
                    'upsoptions' => [],
                    'length' => unserialize(static::initFieldDimensionsValue())[0],
                    'width' => unserialize(static::initFieldDimensionsValue())[1],
                    'height' => unserialize(static::initFieldDimensionsValue())[2],
                    'weight' => static::initFieldMaxWeightValue(),
                    'additional_handling' => static::initFieldAdditionalHandlingValue(),
                    'saturday_pickup' => static::initFieldSaturdayPickupValue(),
                    'saturday_delivery' => static::initFieldSaturdayDeliveryValue(),
                    'delivery_conf' => static::initFieldDeliveryConfValue(),
                    'negotiated_rates' => static::initFieldNegotiatedRatesValue(),
                    'currency_code' => static::initFieldCurrencyCodeValue(),
                    'conversion_rate' => static::initFieldCurrencyRateValue(),
                ];
        }, ['GetUnserializedXC4param00']);
    }

    public function normalizeFieldShipperNumberValue($value)
    {
        return $this->getUnserializedXC4param00()['shipper_number'];
    }

    public function normalizeFieldPackagingTypeValue($value)
    {
        return $this->getUnserializedXC4param00()['packaging_type'];
    }

    public function normalizeFieldPickupTypeValue($value)
    {
        return $this->getUnserializedXC4param00()['pickup_type'];
    }

    public function normalizeFieldDimensionsValue($value)
    {
        $res = $this->getUnserializedXC4param00();
        $l = $res['length'] ?: 10;
        $w = $res['width'] ?: 10;
        $h = $res['height'] ?: 10;

        return serialize([$l, $w, $h]);
    }

    public function normalizeFieldMaxWeightValue($value)
    {
        return $this->getUnserializedXC4param00()['weight'];
    }

    public function normalizeFieldAdditionalHandlingValue($value)
    {
        $res = $this->getUnserializedXC4param00()['upsoptions'];
        $res = explode('|', $res) ?: [];
        return in_array('AH', $res);
    }

    public function normalizeFieldSaturdayPickupValue($value)
    {
        $res = $this->getUnserializedXC4param00()['upsoptions'];
        $res = explode('|', $res) ?: [];
        return in_array('SP', $res);
    }

    public function normalizeFieldSaturdayDeliveryValue($value)
    {
        $res = $this->getUnserializedXC4param00()['upsoptions'];
        $res = explode('|', $res) ?: [];
        return in_array('SD', $res);
    }

    public function normalizeFieldDeliveryConfValue($value)
    {
        return $this->getUnserializedXC4param00()['delivery_conf'];
    }

    public function normalizeFieldNegotiatedRatesValue($value)
    {
        return $this->getUnserializedXC4param00()['negotiated_rates'] == 'Y';
    }

    public function normalizeFieldCurrencyCodeValue($value)
    {
        return $this->getUnserializedXC4param00()['currency_code'] ?: static::initFieldCurrencyCodeValue();
    }

    public function normalizeFieldCurrencyRateValue($value)
    {
        return $this->getUnserializedXC4param00()['conversion_rate'];
    }
    // }}} </editor-fold>
}
