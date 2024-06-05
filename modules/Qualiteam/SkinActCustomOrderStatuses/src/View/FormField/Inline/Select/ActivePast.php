<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomOrderStatuses\View\FormField\Inline\Select;

use Qualiteam\SkinActCustomOrderStatuses\View\FormField\Select\ActivePast as ActivePastSelect;
use XLite\View\FormField\Inline\Base\Single;

class ActivePast extends Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass(): string
    {
        return ActivePastSelect::class;
    }

    /**
     * Get view value
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getViewValue(array $field)
    {
        return parent::getViewValue($field) === 'A'
            ? static::t('SkinActCustomOrderStatuses active')
            : static::t('SkinActCustomOrderStatuses past');
    }
}