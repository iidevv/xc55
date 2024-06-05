<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\CrispWhiteSkin\Module\QSL\reCAPTCHA\View\Authorization;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ReCAPTCHA extends \QSL\reCAPTCHA\View\Authorization\ReCAPTCHA
{
    protected function isStarColumnVisible()
    {
        return false;
    }

    protected function isLabelVisible(): bool
    {
        return false;
    }
}
