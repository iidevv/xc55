<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Controller\Customer;

/**
 * Individual special offers page.
 */
class SpecialOffers extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->checkAccess() ? static::t('Our special offers') : '';
    }

    /**
     * Common method to determine current location.
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Special offers');
    }
}
