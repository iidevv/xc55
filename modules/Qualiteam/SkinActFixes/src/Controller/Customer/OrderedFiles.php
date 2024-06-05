<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFixes\Controller\Customer;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 * @Extender\Depend({"CDev\Egoods"})
 */
class OrderedFiles extends \CDev\Egoods\Controller\Customer\OrderedFiles
{
    protected function addBaseLocation()
    {
        $this->locationPath[] = new \XLite\View\Location\Node\Home();
    }

    public function getTitle()
    {
        return static::t('Ordered files');
    }

    public function isTitleVisible()
    {
        return true;
    }
}