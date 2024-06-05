<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * Special offers page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class SpecialOffers extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['special_offers']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/SpecialOffersBase/special_offers/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer')->count();
    }

    /**
     * Check if there are offer types defined.
     *
     * @return boolean
     */
    protected function hasActiveOfferTypes()
    {
        return 0 < \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\OfferType')->findActiveOfferTypes(true);
    }
}
