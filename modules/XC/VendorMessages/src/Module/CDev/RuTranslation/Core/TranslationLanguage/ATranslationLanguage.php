<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Module\CDev\RuTranslation\Core\TranslationLanguage;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\RuTranslation")
 * @Extender\After("XC\VendorMessages")
 */
abstract class ATranslationLanguage extends \XLite\Core\TranslationLanguage\ATranslationLanguage
{
    /**
     * Translate label 'X unread messages'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelXUnreadMessages(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'X unread message',
                'X unread messages(2)',
                'X unread messages',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }
}
