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
 * @ListChild (list="center", zone="customer")
 */
class SpecialOffersPage extends \QSL\SpecialOffersBase\View\ItemsList\Promotions\APromotedOffers
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'special_offers';

        return $result;
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return parent::getFingerprint() . '-page';
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
        $cnd->{\QSL\SpecialOffersBase\Model\Repo\SpecialOffer::SEARCH_VISIBLE_OFFERS} = true;

        return $cnd;
    }

    /**
     * Returns block title.
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-promoted-offers-page';
    }
}
