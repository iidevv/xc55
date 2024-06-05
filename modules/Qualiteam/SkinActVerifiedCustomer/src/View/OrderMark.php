<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\View;

use XLite\View\AView;


class OrderMark extends AView
{

    protected function isVisible()
    {
        return $this->isVerified() && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    protected function getOrderId()
    {
        return $this->entity->getOrderId();
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/OrderMark.less';
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
        return 'modules/Qualiteam/SkinActVerifiedCustomer/OrderMark.twig';
    }
}