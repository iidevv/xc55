<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Admin;

use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AddressBook extends \XLite\Controller\Admin\AddressBook
{
    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function doNoAction()
    {
        XPaymentsClient::getInstance()->fixSavedCardMethod();

        parent::doNoAction();
    }
}
