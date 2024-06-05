<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Settings processor
 */
class Settings extends \XLite\Logic\Import\Processor\AProcessor
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define sub processors
     *
     * @return array
     */
    public static function defineSubProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\CleanURL',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\Company',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\Email',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\General',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\Shipping',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\Units',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\GoogleAnalytics',
        ];
    }

    public static function definePostProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\SeoHome',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_IS_KEY => true,
            ],
            'category' => [
                static::COLUMN_IS_KEY => true,
            ],
            'value' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config');
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Settings migrated');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeNameValue($value)
    {
        $context = $this->subprocessor ?: $this;

        if (method_exists($context, __FUNCTION__)) {
            call_user_func([$context, __FUNCTION__], $value);
        }

        return $value;
    }

    /**
     * Normalize 'category' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeCategoryValue($value)
    {
        $context = $this->subprocessor ?: $this;

        if (method_exists($context, __FUNCTION__)) {
            call_user_func([$context, __FUNCTION__], $value);
        }

        return $value;
    }

    /**
     * Normalize 'value' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeValueValue($value)
    {
        $context = $this->subprocessor ?: $this;

        if (method_exists($context, __FUNCTION__)) {
            call_user_func([$context, __FUNCTION__], $value);
        }

        return $value;
    }

    // }}} </editor-fold>
}
