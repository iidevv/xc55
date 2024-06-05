<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

/**
 * SEO home page settings
 */
class SeoHome extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
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
            'home_page_title' => [
                static::CONFIG_FIELD_NAME => '1',
            ],
            'meta_descr' => [
                static::CONFIG_FIELD_NAME => '2',
            ],
            'meta_keywords' => [
                static::CONFIG_FIELD_NAME => '3',
            ],
            'site_title' => [
                static::CONFIG_FIELD_NAME => '4',
            ],
        ];
    }

    // }}} </editor-fold>

    protected function importData(array $data)
    {
        static $all_languages = null;

        $maps_names = [
            'site_title' => 'default-site-title',
            'meta_descr' => 'default-meta-description',
            'meta_keywords' => 'default-meta-keywords',
        ];

        if ($data['name'] == 'home_page_title') {
            $rootCategory = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();

            if ($rootCategory) {
                $translation = $rootCategory->getTranslation();

                if (!$rootCategory->hasTranslation($translation->getCode())) {
                    $translation->setName($rootCategory->getName()); // To Avoid #MW-86
                    $rootCategory->addTranslations($translation);
                }

                $rootCategory->setMetaTitle($data['value']);
            }
        } elseif (isset($maps_names[$data['name']])) {
            if (!$all_languages) {
                $_languages = \XLite\Core\Database::getRepo('\XLite\Model\Language')->findAddedLanguages();

                $all_languages = array_map(
                    static function ($lng) {
                        return $lng->getCode();
                    },
                    $_languages
                ) ?: [];
            }

            $toUpdate = [];

            foreach ($all_languages as $code) {
                $label = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findOneByName($maps_names[$data['name']]);
                if ($label) {
                    $label->setEditLanguage($code)->setLabel($data['value']);
                    $toUpdate[] = $label;
                }
            }

            if ($toUpdate) {
                \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->updateInBatch($toUpdate);
                \XLite\Core\Translation::getInstance()->reset();
            }
        }

        return true;
    }
}
