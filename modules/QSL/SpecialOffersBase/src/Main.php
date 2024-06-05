<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Updates offer types and disable those that have no enabled modules.
     *
     * @return void
     */
    public static function updateOfferTypes()
    {
        foreach (\XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\OfferType')->findAll() as $type) {
            $enabled = $type->hasAllRequiredClasses();
            $type->setEnabled($enabled);
            if (!$enabled) {
                foreach ($type->getSpecialOffers() as $offer) {
                    $offer->setEnabled(false);
                }
            }
        }
    }

    /**
     * Adds the Special Offers entry to the primary storefront menu if it is not there yet.
     *
     * @return void
     */
    public static function addSimpleCMSMenuLink()
    {
        $repo = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu');
        $repoLang = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\MenuTranslation');

        $link = \QSL\SpecialOffersBase\Model\Menu::DEFAULT_OFFERS_PAGE;

        /** @var \CDev\SimpleCMS\Model\Menu $item */
        $item = $repo->findOneByLink('?target=special_offers');
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
                        'name' => 'Special offers',
                    ]
                );
                $en->setOwner($item);
                $item->addTranslations($en);
                $repoLang->insert($en);

                $ru = new \CDev\SimpleCMS\Model\MenuTranslation(
                    [
                        'code' => 'ru',
                        'name' => 'Акции',
                    ]
                );
                $ru->setOwner($item);
                $item->addTranslations($ru);
                $repoLang->insert($ru);
            }
        }
    }
}
