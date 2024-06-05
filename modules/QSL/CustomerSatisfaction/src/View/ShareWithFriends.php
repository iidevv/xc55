<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Share with friends widget
 *
 * @ListChild (list="center", zone="customer")
 */
class ShareWithFriends extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['share_with_friends']);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/style.css';

        return $list;
    }

    /**
     * Gets locale (ru_RU, en_US and etc)
     * @return mixed
     */
    public function getLocale()
    {
        $langCode = \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        $localeWithEncoding = \XLite\Core\Converter::detectLocaleByCode($langCode);
        $locale = explode('.', $localeWithEncoding);

        return $locale[0];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/share_with_friends/body.twig';
    }
}
