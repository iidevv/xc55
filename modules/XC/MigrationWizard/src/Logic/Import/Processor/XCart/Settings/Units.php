<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

/**
 * Units store settings
 */
class Units extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
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
            'weight_symbol'     => [
                static::CONFIG_FIELD_NAME => 'weight_symbol',
            ],
            'weight_symbol alias weight_unit'     => [
                static::CONFIG_FIELD_NAME => 'weight_unit',
                static::CONFIG_FIELD_IS_VIRTUAL => true,
                static::CONFIG_FIELD_NORMALIZATOR_SKIP_ESCAPE => true,
            ],
            'dimensions_symbol' => [
                static::CONFIG_FIELD_NAME => 'dim_symbol',
            ],
            'dimensions_symbol alias dim_unit'     => [
                static::CONFIG_FIELD_NAME => 'dim_unit',
                static::CONFIG_FIELD_IS_VIRTUAL => true,
                static::CONFIG_FIELD_NORMALIZATOR_SKIP_ESCAPE => true,
            ],
        ];
    }
    // }}} </editor-fold>

    // {{{ Initializers <editor-fold desc="Initializers" defaultstate="collapsed">

    public static function initFieldWeightUnitValue()
    {
        $prefix  = self::getTablePrefix();

        return "(SELECT value FROM {$prefix}config WHERE name='weight_symbol')";
    }

    public static function initFieldDimUnitValue()
    {
        $prefix  = self::getTablePrefix();

        return "(SELECT value FROM {$prefix}config WHERE name='dimensions_symbol')";
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldWeightUnitValue($value)
    {
        static $possible_map = [];

        if (empty($possible_map)) {
            $possible_map = [
                'кг' => 'kg',
                'килограмм' => 'kg',
                'грамм' => 'г',
                'lbs' => 'lbs',
                'lb' => 'lbs',
                'oz' => 'oz',
                'g' => 'g',
                (string) static::t('LB') => 'lbs',
                (string) static::t('OZ') => 'oz',
                (string) static::t('KG') => 'kg',
                (string) static::t('G') => 'g',
            ];
        }

        return $possible_map[strtolower($value)] ?? $value;
    }

    public function normalizeFieldDimUnitValue($value)
    {
        static $_possible_map = [];

        if (empty($_possible_map)) {
            $_possible_map = [
                'centimeter' => 'cm',
                'centimeters' => 'cm',
                'centimetre' => 'cm',
                'centimetres' => 'cm',
                'cm (centimeters)' => 'cm',
                'cm' => 'cm',
                'decimeter' => 'cm',
                'decimeters' => 'cm',
                'decimetre' => 'cm',
                'decimetres' => 'cm',
                'in (inches)' => 'in',
                'in' => 'in',
                'inch' => 'in',
                'inches' => 'in',
                'meter' => 'm',
                'meters' => 'm',
                'metre' => 'm',
                'metres' => 'm',
                'millimeter' => 'cm',
                'millimeters' => 'cm',
                'millimetre' => 'cm',
                'millimetres' => 'cm',
                'дециметр' => 'dm',
                'дециметры' => 'dm',
                'дюйм' => 'in',
                'дюймы' => 'in',
                'метр' => 'm',
                'метры' => 'm',
                'миллиметр' => 'mm',
                'миллиметры' => 'mm',
                'сантиметр' => 'cm',
                'сантиметры' => 'cm',
                'дм' => 'dm',
                'м' => 'm',
                'мм' => 'mm',
                'см' => 'cm',
                (string) static::t('MM') => 'mm',
                (string) static::t('DM') => 'dm',
                (string) static::t('CM') => 'cm',
                (string) static::t('IN') => 'in',
            ];
        }

        return $_possible_map[strtolower($value)] ?? $value;
    }

    // }}} </editor-fold>
}
