<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Customer;


class Careers extends \XLite\Controller\Customer\ACustomer
{

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            static::t('SkinActCareers Careers'),
            $this->buildURL('careers')
        );

    }

    public function getTitle()
    {
        return static::t('SkinActCareers Careers');
    }

    protected function getLocation()
    {
        return $this->getTitle();
    }

}
