<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\TranslationDriver;

/**
 * DB-based driver (without cache)
 */
class DbDirect extends \XLite\Core\TranslationDriver\ATranslationDriver
{
    public const TRANSLATION_DRIVER_NAME = 'DatabaseDirect';

    /**
     * @inheritdoc
     */
    public function translate($name, $code)
    {
        $label = $this->getRepo()->findOneByName($name);

        return $label && $label->getHardTranslation($code)
            ? $label->getHardTranslation($code)->getLabel()
            : null;
    }
}
