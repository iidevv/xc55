<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use XC\GDPR\Core\Activity;

/**
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    protected function postprocessSuccessAction()
    {
        parent::postprocessSuccessAction();

        if (($profile = $this->getModelObject()) instanceof \XLite\Model\Profile) {
            Activity\Admin::update($profile);
        }
    }
}
