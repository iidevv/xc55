<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\View\FormField\Search\NewsMessage;

/**
 * Name search widget
 */
class Name extends \XLite\View\FormField\Search\ASearch
{
    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        return [
            [
                static::FIELD_NAME  => 'name',
                static::FIELD_CLASS => 'XLite\View\FormField\Input\Text',
            ],
        ];
    }
}
