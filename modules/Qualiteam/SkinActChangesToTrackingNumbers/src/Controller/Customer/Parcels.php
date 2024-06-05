<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\Controller\Customer;

/**
 * Messages
 */
class Parcels extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target'];

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
        && \XLite\Core\Auth::getInstance()->isLogged()
        && $this->checkProfile();
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget_title ?: static::t('SkinActChangesToTrackingNumbers parcels');
    }

    /**
     * @inheritdoc
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * @inheritdoc
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }
}
