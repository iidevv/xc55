<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Controller\Customer;

class TestErrorPage extends \XLite\Controller\Customer\ACustomer
{
    public function doNoAction()
    {
        throw new \Exception('Test error page');
        exit;
    }
}