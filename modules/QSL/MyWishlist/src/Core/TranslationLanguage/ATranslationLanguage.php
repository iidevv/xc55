<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core\TranslationLanguage;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract translation language
 * @Extender\Mixin
 */
abstract class ATranslationLanguage extends \XLite\Core\TranslationLanguage\ATranslationLanguage
{
    /**
     * Define label handlers
     *
     * @return array
     */
    protected function defineLabelHandlers()
    {
        return parent::defineLabelHandlers()
            + [
                'wishlist - X items' => 'translateLabelWishlistXItems',
            ];
    }

    /**
     * Translate label 'Your shopping bag - X items'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelWishlistXItems(array $arguments)
    {
        $label = $this->getLabelByRule(
            [
                'wishlist - X item',
                'wishlist - X items',
            ],
            $arguments['count']
        );

        return \XLite\Core\Translation::getInstance()->translateByString($label, $arguments);
    }
}
