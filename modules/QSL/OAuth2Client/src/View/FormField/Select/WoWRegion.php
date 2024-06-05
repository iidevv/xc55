<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\FormField\Select;

/**
 * WoW region
 */
class WoWRegion extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return [
            'us' => 'United States',
            'eu' => 'EU',
            'kr' => 'South Korea',
            'tw' => 'Taiwan',
        ];
    }
}
