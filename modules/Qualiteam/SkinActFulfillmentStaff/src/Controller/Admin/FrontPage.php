<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Controller\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class FrontPage extends \XLite\Controller\Admin\FrontPage
{
    protected function doNoAction()
    {
        if (Core::isProfileHasStaffAccess()) {
            $this->redirect(
                $this->buildURL('banner_rotation')
            );
        }

        parent::doNoAction();
    }
}