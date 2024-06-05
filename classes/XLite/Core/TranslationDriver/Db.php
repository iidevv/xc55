<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\TranslationDriver;

/**
 * DB-based driver
 */
class Db extends \XLite\Core\TranslationDriver\ATranslationDriver
{
    public const TRANSLATION_DRIVER_NAME = 'Database';

    /**
     * Translations
     *
     * @var array
     */
    protected $translations = [];

    /**
     * @inheritdoc
     */
    public function translate($name, $code)
    {
        if (!isset($this->translations[$code])) {
            $this->translations[$code] = $this->getRepo()->findLabelsByCode($code);
        }

        return \Includes\Utils\ArrayManager::getIndex($this->translations[$code], $name);
    }

    /**
     * Reset language driver
     *
     * @return void
     */
    public function reset()
    {
        parent::reset();

        $this->translations = [];
        $this->getRepo()->cleanCache();
    }
}
