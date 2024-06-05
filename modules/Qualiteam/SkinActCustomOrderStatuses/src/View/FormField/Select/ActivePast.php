<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomOrderStatuses\View\FormField\Select;

use XLite\View\FormField\Select\Regular;

class ActivePast extends Regular
{
    protected function getDefaultOptions()
    {
        return [
            'A' => static::t('SkinActCustomOrderStatuses active'),
            'P' => static::t('SkinActCustomOrderStatuses past'),
        ];
    }
}