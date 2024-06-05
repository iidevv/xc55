<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\FormField\Select;

/**
 * "Visible for" selector
 *
 */
class VisibleFor extends \XLite\View\FormField\Select\Regular
{
    /**
     * Texts for labels
     */
    public const ANY_VISITORS      = 'Any visitors';
    public const ANONYMOUS_ONLY    = 'Anonymous users only';
    public const LOGGED_IN_ONLY    = 'Logged in users only';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'AL' => static::t(static::ANY_VISITORS),
            'A'  => static::t(static::ANONYMOUS_ONLY),
            'L'  => static::t(static::LOGGED_IN_ONLY),
        ];
    }
}
