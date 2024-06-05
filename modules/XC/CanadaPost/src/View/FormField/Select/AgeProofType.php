<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\FormField\Select;

/**
 * Proof of age type selector
 */
class AgeProofType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''     => static::t('Not specified'),
            'PA18' => static::t('18 years'),
            'PA19' => static::t('19 years'),
        ];
    }
}
