<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\Controller\Customer;

/**
 * Re-order list
 */
class ReOrder extends \XLite\Controller\Customer\ACustomer
{
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget_title ?: static::t('My account');
    }

    public function isTitleVisible()
    {
        return true;
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        array_pop($this->locationPath);
    }

    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . 'reorder-item-list';
    }
}