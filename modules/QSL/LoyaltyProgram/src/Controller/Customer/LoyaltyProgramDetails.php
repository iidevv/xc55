<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Customer;

class LoyaltyProgramDetails extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Loyalty Program Details');
    }

    /**
     * @param array $classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes[] = 'target-page';

        return $classes;
    }

    /**
     * Define current location for breadcrumbs.
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Loyalty Program Details');
    }
}
