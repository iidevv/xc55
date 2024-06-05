<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Adds the Brands entry to the primary storefront menu if it is not there yet.
     */
    public static function addSimpleCMSMenuLink()
    {
        $repo     = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu');
        $repoLang = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\MenuTranslation');

        $link = \QSL\ShopByBrand\Model\Menu::DEFAULT_BRANDS_PAGE;

        $item = $repo->findOneByLink('?target=brands');
        if ($item) {
            $item->setLink($link);
        } else {
            $item = $repo->findOneByLink($link);
            if (!$item) {
                $item = new \CDev\SimpleCMS\Model\Menu(
                    [
                        'enabled'  => false,
                        'link'     => $link,
                        'type'     => \CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY,
                        'position' => 150,
                    ]
                );
                $repo->insert($item);

                $en = new \CDev\SimpleCMS\Model\MenuTranslation(
                    [
                        'code' => 'en',
                        'name' => 'Brands',
                    ]
                );
                $en->setOwner($item);
                $item->addTranslations($en);
                $repoLang->insert($en);

                $ru = new \CDev\SimpleCMS\Model\MenuTranslation(
                    [
                        'code' => 'ru',
                        'name' => 'Бренды',
                    ]
                );
                $ru->setOwner($item);
                $item->addTranslations($ru);
                $repoLang->insert($ru);
            }
        }
    }
}
