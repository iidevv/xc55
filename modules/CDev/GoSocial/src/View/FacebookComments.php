<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Facebook comments
 *
 * @ListChild (list="product.details.page.tab.comments")
 */
class FacebookComments extends \XLite\View\AView
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/GoSocial/button/js/facebook_like.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/fb.comments.twig';
    }

    /**
     * Get button attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'href'          => $this->getCanonicalURL() ?: \XLite::getInstance()->getShopURL($this->getURL()),
            'width'         => 500,
            'numposts'      => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_comments_num_posts,
            'colorscheme'   => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_comments_colorscheme,
        ];
    }
}
