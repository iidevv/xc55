<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

/**
 * Wishlist products send mail form
 */
class SendMailForm extends \XLite\View\AView
{
    /**
     * Define the specific JS file for widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/MyWishlist/send_mail_form.js';

        return $list;
    }

    /**
     * The send mail form is not visible if there is no visible products in the wishlist
     *
     * @return boolean
     */
    public function isVisible()
    {
        $auth = \XLite\Core\Auth::getInstance();
        $list = $this->getWishlist();

        return parent::isVisible()
            && !\XLite\Core\Request::getInstance()->list_hash
            && $auth->isWishlistAvailable()
            && $list->hasAccessToManage($auth->getProfile())
            && $list->hasVisibleProducts();
    }

    /**
     * Define the main template for the widget
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/send_mail_form.twig';
    }
}
