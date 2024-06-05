<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\FormField\Select\LanguageSelector;

use XLite\Core\Cache\ExecuteCached;

class CustomerCountry extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $currentCountry = \XLite::getController()->getCurrentCountry();

        return $currentCountry ? $currentCountry->getCode() : '';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Country');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return self::getCountries();
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = [];

        foreach (self::getCountries() as $country) {
            $options[$country->getCode()] = $country->getCountry();
        }

        return $options;
    }

    /**
     * @return mixed
     */
    protected static function getCountries(): array
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled();
        }, [__METHOD__]);
    }

    /**
     * Get commented widget data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array_merge(
            parent::getCommentedData(),
            ['languagesByCountry' => $this->getCountriesByLanguage()]
        );
    }

    protected function getCountriesByLanguage()
    {
        $result = [];

        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();
        foreach ($languages as $language) {
            foreach ($language->getCountries() as $country) {
                $result[$country->getCode()] = $language->getCode();
            }
        }

        return $result;
    }
}
