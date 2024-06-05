<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

abstract class Alert extends \XLite\View\AView
{
    abstract protected function getAlertContent();

    protected function getClass()
    {
        return 'alert';
    }

    protected function getDefaultTemplate()
    {
        return 'alert.twig';
    }
}
