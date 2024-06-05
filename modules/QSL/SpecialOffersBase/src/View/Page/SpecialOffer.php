<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * Special offer page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class SpecialOffer extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['special_offer']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/SpecialOffersBase/special_offer/body.twig';
    }
}
