<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Controller\Customer;

class SaleProducts extends \XLite\Controller\Customer\ACustomer
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Sale');
    }

    /**
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }
}
