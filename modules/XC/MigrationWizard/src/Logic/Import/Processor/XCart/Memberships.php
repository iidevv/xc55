<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Memberships processor
 */
class Memberships extends \XLite\Logic\Import\Processor\AProcessor
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                self::COLUMN_IS_KEY          => true,
                self::COLUMN_IS_MULTILINGUAL => true,
                self::COLUMN_LENGTH          => 255,
            ],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Membership');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $languageFields = static::getMembershipsLanguageFieldsSQL();

        return 'm.membership AS `name`,'
            . $languageFields
            . 'm.orderby AS `position`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}memberships AS m";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        return 'm.area = "C"';
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            self::REGISTRY_SOURCE => 'name',
            self::REGISTRY_RESULT => 'membership_id',
        ];
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
        return static::t('Memberships migrated');
    }

    /**
     * Get memberships language fields SQL
     *
     * @return string
     */
    public static function getMembershipsLanguageFieldsSQL()
    {
        return static::getLanguageFieldsSQLfor(
            [
                'm.`membership`' => 'name',
            ],
            Configuration::getAvailableLanguages()
        );
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating memberships');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeNameValue($value)
    {
        $default = $this->normalizeValueAsString($this->getDefLangValue($value));

        return $this->getI18NValues($default, 'membership', $value);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getLngDataPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT ml.code code, ml.membership membership'
            . " FROM {$prefix}memberships_lng AS ml"
            . " INNER JOIN {$prefix}memberships AS m"
            . ' ON ml.membershipid = m.membershipid'
            . ' AND m.membership = ?'
        );
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Membership
     */
    protected function createModel(array $data)
    {
        $this->importer->getOptions()->commonData['calculateAllQuickData'] = true;

        return parent::createModel($data);
    }

    // }}} </editor-fold>
}
