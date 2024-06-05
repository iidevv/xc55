<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core\TranslationLanguage;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ATranslationLanguage extends \XLite\Core\TranslationLanguage\ATranslationLanguage
{
    /**
     * @inheritdoc
     */
    protected function defineLabelHandlers()
    {
        return parent::defineLabelHandlers()
            + [
                'You and N other people are subscribed to back-in-stock and price-drop alert for this product'  => 'QSLBackInStockYouAndX',
                'N other people are subscribed to back-in-stock and price-drop alert for this product'          => 'QSLBackInStockXOther',
            ];
    }

    /**
     * Translate label 'You and N other people are subscribed to back-in-stock and price-drop alert for this product'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelQSLBackInStockYouAndX(array $arguments)
    {
        return $arguments['count'] == 1
            ? \XLite\Core\Translation::getInstance()->translateByString('You and 1 other person are subscribed to back-in-stock and price-drop alert for this product', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('You and N other people are subscribed to back-in-stock and price-drop alert for this product', $arguments);
    }

    /**
     * Translate label 'N other people are subscribed to back-in-stock and price-drop alert for this product'
     *
     * @param array $arguments Arguments
     *
     * @return string
     */
    public function translateLabelQSLBackInStockXOther(array $arguments)
    {
        return $arguments['count'] == 1
            ? \XLite\Core\Translation::getInstance()->translateByString('1 other person are subscribed to back-in-stock and price-drop alert for this product', $arguments)
            : \XLite\Core\Translation::getInstance()->translateByString('N other people are subscribed to back-in-stock and price-drop alert for this product', $arguments);
    }
}
