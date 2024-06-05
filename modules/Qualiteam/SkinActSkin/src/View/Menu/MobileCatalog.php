<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu;

use Qualiteam\SkinActSkin\Main;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Database;
use XLite\Model\Category;
use XLite\View\AView;

/**
 * @ListChild (list="layout.header.mobile.menu.right", weight=100)
 */
class MobileCatalog extends AView
{
    public function getChildren(): array
    {
        $repo = Database::getRepo(Category::class);

        return $repo->findBy(
            [ 'parent' => $repo->getRootCategoryId() ],
            [ 'pos' => 'ASC' ]
        );
    }

    protected function getDefaultTemplate(): string
    {
        return 'layout/header/mobile_header_parts/categories.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && Main::isStoreHasCategories();
    }

    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            ['js/mobile-menu/catalog.js']
        );
    }
}
