<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Wishlist products list widget
 *
 * @ListChild (list="center")
 */
class WishlistCustomerPage extends \XLite\View\AView implements ProviderInterface
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'wishlist';

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Request::getInstance()->target === 'wishlist';
    }

    /**
     * Define the main template for the widget
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/wishlist.twig';
    }

    public function getPreloadedLanguageLabels(): array
    {
        return [
            'wishlist - X item'                                      => static::t('wishlist - X item'),
            'wishlist - X items'                                     => static::t('wishlist - X items'),
            'Private wishlist title'                                 => static::t('Private wishlist title'),
            'Feel free to add any product you like to your wishlist' =>
                static::t('Feel free to add any product you like to your wishlist'),
        ];
    }
}
