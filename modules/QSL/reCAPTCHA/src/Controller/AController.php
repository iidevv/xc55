<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Controller;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract controller
 * @Extender\Mixin
 */
abstract class AController extends \XLite\Controller\AController
{
    protected $requiresActivation = false;

    public function setRequiresActivation($value = true)
    {
        $this->requiresActivation = $value;
    }

    public function getRequiresActivation()
    {
        return $this->requiresActivation;
    }
}
