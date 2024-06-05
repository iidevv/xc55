<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\FormField\Select;

/**
 * QuoteType selector
 *
 */
class QuoteType extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            \XC\CanadaPost\Core\API::QUOTE_TYPE_NON_CONTRACTED => static::t('Counter - will return the regular price paid by retail consumers'),
            \XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED     => static::t('Commercial - will return the contracted price between Canada Post and the contract holder'),
        ];
    }
}
