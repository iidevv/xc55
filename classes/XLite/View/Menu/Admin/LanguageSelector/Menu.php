<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LanguageSelector;

use XLite\Core\Request;

/**
 * Quick menu widget
 */
class Menu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'menu/language_selector';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return 'XLite\View\Menu\Admin\LanguageSelector\Node';
    }

    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = [];

        $activeLanguages = $this->getActiveLanguages();
        $currentLanguageCode = $this->getCurrentLanguage()->getCode();

        if (count($activeLanguages) > 1) {
            $weight = 1;
            foreach ($activeLanguages as $lng) {
                $items[$lng->getCode()] = [
                    static::ITEM_TITLE         => strtoupper($lng->getCode()),
                    static::ITEM_LINK          => $this->getChangeLanguageLink($lng),
                    static::ITEM_WEIGHT        => $weight++,
                    static::ITEM_ICON_IMG      => \XLite\Core\Layout::getInstance()->getResourceWebPath(
                        $lng->getFlagFile(),
                        null,
                        \XLite::INTERFACE_WEB,
                        \XLite::ZONE_COMMON
                    ),
                    static::ITEM_PUBLIC_ACCESS => true,
                ];

                if ($lng->getCode() === $currentLanguageCode) {
                    $items[$lng->getCode()][static::ITEM_CLASS] = 'selected';
                }
            }
        }

        return $items;
    }

    /**
     * Get active languages
     *
     * @return \XLite\Model\Language[]
     */
    protected function getActiveLanguages()
    {
        $list = [];
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
            $list[] = $language;
        }

        return $list;
    }

    /**
     * Get link to change language
     *
     * @param \XLite\Model\Language $language Language object
     *
     * @return string
     */
    protected function getChangeLanguageLink(\XLite\Model\Language $language)
    {
        return $this->buildURL(
            $this->getTarget(),
            'change_language',
            [
                'language' => $language->getCode(),
            ] + Request::getInstance()->getGetData(),
            false
        );
    }
}
