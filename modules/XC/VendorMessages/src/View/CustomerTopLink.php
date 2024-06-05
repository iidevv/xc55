<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Top link
 *
 * @ListChild (list="layout.header.bar.links.logged", zone="customer", weight="100")
 */
class CustomerTopLink extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XC/VendorMessages/top_link.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/VendorMessages/top_link.twig';
    }

    /**
     * Get vendor interface link (admin area)
     *
     * @return string
     */
    protected function getMessagesUrl()
    {
        return \XLite\Core\Converter::buildURL(
            'messages',
            '',
            [],
            \XLite\Core\Auth::getInstance()->isAdmin()
                ? \XLite::getAdminScript()
                : \XLite::getCustomerScript()
        );
    }

    /**
     * Count messages
     *
     * @return integer
     */
    protected function countMessages()
    {
        return \XLite\Core\Auth::getInstance()->getProfile()->countOwnUnreadMessages();
    }
}
