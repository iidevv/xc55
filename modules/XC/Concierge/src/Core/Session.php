<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Session extends \XLite\Core\Session
{
    //protected function createSession()
    //{
    //    parent::createSession();
    //
    //    if (\XLite\Core\Config::getInstance()->XC->Concierge->write_key && !$this->useDumpSession()) {
    //        $this->sessionImmediateCreated = true;
    //    }
    //}
}
