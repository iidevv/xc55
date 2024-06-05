<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

/**
 *
 */
class WishlistLink extends \XLite\View\Button\SimpleLink
{
    public const PARAM_HAS_LABEL = 'hasLabel';


    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_HAS_LABEL => new \XLite\Model\WidgetParam\TypeBool('Is label displayed', false),
        ];
    }

    /**
     * Return true if there's a label in the wishlist link
     *
     * @return boolean
     */
    protected function hasLabel()
    {
        return $this->getParam(static::PARAM_HAS_LABEL);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/button/link.twig';
    }

    /**
     * Label of wishlist link
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Wishlist');
    }

    /**
     * CSS class of wishlist link
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
            . ' wishlist-link'
            . (\XLite\Core\Auth::getInstance()->isLogged() ? '' : ' log-in');
    }

    protected function getLinkAttributes()
    {
        $list = parent::getLinkAttributes();

        $list['data-return-url'] = $this->buildURL('wishlist');

        return $list;
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return $this->buildURL('wishlist');
    }
}
