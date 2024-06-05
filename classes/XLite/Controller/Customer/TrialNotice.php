<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

class TrialNotice extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Print widget used for iframe content
     */
    protected function doNoAction()
    {
        $widget = new \XLite\View\LicenseManager\TrialNotice();

        print $widget->getContent();

        $this->silent = true;
        $this->setSuppressOutput(true);
    }
}
