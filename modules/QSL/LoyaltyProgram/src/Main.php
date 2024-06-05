<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram;

use CDev\SimpleCMS\Model\Menu;
use CDev\SimpleCMS\Model\MenuTranslation;

/**
 * Main module class.
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Adds the Brands entry to the primary storefront menu if it is not there yet.
     */
    public static function addSimpleCMSMenuLink()
    {
        $repo     = \XLite\Core\Database::getRepo(Menu::class);
        $repoLang = \XLite\Core\Database::getRepo(MenuTranslation::class);

        $link = \QSL\LoyaltyProgram\Model\Menu::DEFAULT_LOYALTY_PROGRAM_PAGE;

        $item = $repo->findOneByLink('?target=loyalty_program_details');
        if ($item) {
            $item->setLink($link);
        } else {
            $item = $repo->findOneByLink($link);
            if (!$item) {
                $item = new Menu(
                    [
                        'enabled'  => false,
                        'link'     => $link,
                        'type'     => Menu::MENU_TYPE_PRIMARY,
                        'position' => 450,
                    ]
                );
                $repo->insert($item);

                $en = new MenuTranslation(
                    [
                        'code' => 'en',
                        'name' => 'Loyalty Program',
                    ]
                );
                $en->setOwner($item);
                $item->addTranslations($en);
                $repoLang->insert($en);

                $ru = new MenuTranslation(
                    [
                        'code' => 'ru',
                        'name' => 'Бонусы',
                    ]
                );
                $ru->setOwner($item);
                $item->addTranslations($ru);
                $repoLang->insert($ru);
            }
        }
    }
}
