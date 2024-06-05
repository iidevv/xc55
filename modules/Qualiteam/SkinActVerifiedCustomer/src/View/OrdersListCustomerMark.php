<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\View;

use XLite\View\AView;
use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="itemsList.orders.search.cell.profile", weight="999999", zone="admin")
 */
class OrdersListCustomerMark extends AView
{
    protected function getProfileId()
    {
        return $this->entity && $this->entity->getOrigProfile() ? $this->entity->getOrigProfile()->getProfileId() : -1;
    }

    protected function getVerifiedCheckboxVisible()
    {
        $shippingStatus = $this->entity ? $this->entity->getShippingStatus() : null;

        if ($shippingStatus
            && $shippingStatus->getId() === (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActVerifiedCustomer->order_verified_status_id
        ) {
            return '';
        }

        return 'hidden';
    }

    protected function getOrderId()
    {
        return $this->entity->getOrderId();
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/OrdersListCustomerMark.less';
        return $list;
    }

    protected function isVerified()
    {
        return $this->entity
            && $this->entity->getOrigProfile()
            && $this->entity->getOrigProfile()->isVerified();
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActVerifiedCustomer/OrdersListCustomerMark.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders')
            && \XLite::getController()->getTarget() === 'order_list';
    }
}