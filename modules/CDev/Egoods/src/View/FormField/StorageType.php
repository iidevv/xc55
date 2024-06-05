<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\FormField;

/**
 * Storage Type selector for settings page
 */
class StorageType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'as3' => static::t('Amazon S3'),
            'dos' => static::t('Digital Ocean Space'),
        ];
    }
}
