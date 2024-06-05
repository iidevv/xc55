<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\ItemsList\Promotions;

use XCart\Extender\Mapping\ListChild;

/**
 * Special offers promoted on the home page.
 *
 * @ListChild (list="center.bottom", zone="customer", weight="105")
 */
class HomeOffers extends \QSL\SpecialOffersBase\View\ItemsList\Promotions\APromotedOffers
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'main';

        return $list;
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return parent::getFingerprint() . '-home';
    }

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
        $cnd->{\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_VISIBLE_HOME} = true;

        return $cnd;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-promoted-offers-home';
    }
}
