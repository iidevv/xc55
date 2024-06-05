<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Logic\Order\SpecialOffer;

/**
 * Abstract special offer logic.
 */
abstract class ASpecialOffer extends \XLite\Base
{
    /**
     * Epsilon constant used when comparing float values.
     */
    public const EPS = 0.000000001;

    /**
     * Applies the special offer to the order being processed by the special offer modifier.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer                 $offer    Special offer model.
     * @param \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier Order modifier.
     *
     * @return void
     */
    abstract public function applyOffer(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
    );

    /**
     * Checks if the offer has correct settings and can be applied on the order.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer                 $offer    Special offer model.
     * @param \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier Order modifier.
     *
     * @return boolean
     */
    public function canApplyOffer(
        \QSL\SpecialOffersBase\Model\SpecialOffer $offer,
        \QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers $modifier
    ) {
        return true;
    }
}
