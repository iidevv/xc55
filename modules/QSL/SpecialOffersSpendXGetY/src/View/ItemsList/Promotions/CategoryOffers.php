<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersSpendXGetY\View\ItemsList\Promotions;

use XCart\Extender\Mapping\Extender;

/**
 * Special offers promoted on category pages.
 * @Extender\Mixin
 */
class CategoryOffers extends \QSL\SpecialOffersBase\View\ItemsList\Promotions\CategoryOffers
{
    /**
     * Dependent modules should enable this flag to get the widget displayed.
     *
     * @return boolean
     */
    protected function isWidgetEnabled()
    {
        return true;
    }

    /**
     * Returns parameters to filter the list of available booking options.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();
        $cnd->{\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_VISIBLE_SXGY_CATEGORY} = true;
        $cnd->{\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_ENABLED_SXGY_CATEGORY} = intval($this->getCategory()->getCategoryId());

        return $cnd;
    }
}
