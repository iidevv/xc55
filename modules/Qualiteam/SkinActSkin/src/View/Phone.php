<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Company phone number
 *
 * @ListChild (list="layout.header.bar", weight="0")
 */
class Phone extends \XLite\View\AView
{

    public function getPhone()
    {
        return \XLite\Core\Config::getInstance()->Company->company_phone;
    }

    protected function getDefaultTemplate()
    {
        return 'layout/header/phone.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getPhone();
    }

}
