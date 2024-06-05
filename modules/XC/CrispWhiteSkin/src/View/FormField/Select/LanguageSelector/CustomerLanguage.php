<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\FormField\Select\LanguageSelector;

class CustomerLanguage extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite\Core\Session::getInstance()->getLanguage()->getCode();
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $return = [];

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Language')->findActiveLanguages() as $language) {
            $return[$language->getCode()] = $language->getName();
        }

        return $return;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Language');
    }
}
