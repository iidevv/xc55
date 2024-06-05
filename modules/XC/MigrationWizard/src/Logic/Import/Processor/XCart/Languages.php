<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Languages
 */
class Languages extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    protected static $languageModules = [
        'ru' => 'CDev\RuTranslation',
        'de' => 'CDev\DeTranslation',
        'fr' => 'CDev\FrTranslation',
        'nl' => 'XC\NlTranslation',
    ];

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'code'    => [
                self::COLUMN_IS_KEY => true,
            ],
            'added'   => [],
            'enabled' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Language');
    }

    /**
     * Define field set SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return 'code,'
            . 'TRUE added,'
            . 'TRUE enabled';
    }

    /**
     * Define data set SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $languages = Configuration::getAvailableLanguages();

        $dataset = '(SELECT \'' . array_shift($languages) . '\' as code';
        foreach ($languages as $langCode) {
            $dataset .= ' UNION SELECT \'' . $langCode . '\'';
        }
        $dataset .= ') ls';

        return $dataset;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        $languages = Configuration::getAvailableLanguages();

        return array_values(array_intersect_key(static::$languageModules, array_flip($languages)));
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        return Configuration::getAvailableLanguages() > 0;
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        if (version_compare(static::getPlatformVersion(), '4.3.0') < 0) {
            if (strtolower($data['code']) == 'us') {
                $data['code'] = 'en';
            }
        }

        parent::importData($data);
    }

    // }}} </editor-fold>
}
