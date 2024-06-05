<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Customer;

class Brands extends \XLite\Controller\Customer\ACustomer
{
    public const PARAM_FIRST_LETTER = 'first_letter';
    public const PARAM_SUBSTRING    = 'substring';

    /**
     * Cached brands.
     *
     * @var array
     */
    protected $brands;

    /**
     * Return the page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible() ? static::t('All brands') : '';
    }

    /**
     * Common method to determine current location.
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('All brands');
    }

    /**
     * @return string
     */
    public function getFirstLetter()
    {
        return \XLite\Core\Request::getInstance()->{static::PARAM_FIRST_LETTER};
    }

    /**
     * @return string
     */
    public function getSubstring()
    {
        return \XLite\Core\Request::getInstance()->{static::PARAM_SUBSTRING};
    }
}
