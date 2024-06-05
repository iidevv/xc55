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
class CustomerProfiles extends \XLite\Controller\Admin\CustomerProfiles
{
    public function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }

    protected function doNoAction()
    {
        if (Core::isProfileHasStaffAccess()) {
            $this->redirect(
                $this->buildURL('memberships', [], ['section' => 'Communications'])
            );
        }

        parent::doNoAction();
    }
}