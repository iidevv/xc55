<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Core\TranslationLanguage;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ATranslationLanguage extends \XLite\Core\TranslationLanguage\ATranslationLanguage
{
    /**
     * @return array
     */
    protected function defineLabelHandlers()
    {
        return parent::defineLabelHandlers()
            + [
                'X unread messages' => 'translateLabelXUnreadMessages',
            ];
    }

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
                'X unread messages',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }
}
