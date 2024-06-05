<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\TopCategories;

use XCart\Extender\Mapping\ListChild;
use CDev\Sale\View\FormField\Select\ShowLinksInCategoryMenu;

/**
 * List of discount links
 *
 * @ListChild (list="topCategories.linksUnder", zone="customer", weight="100")
 */
class AdditionalLinksUnder extends \CDev\Sale\View\TopCategories\AAdditionalLinks
{
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->CDev->Sale->show_links_in_category_menu === ShowLinksInCategoryMenu::TYPE_UNDER_CATEGORIES;
    }
}
