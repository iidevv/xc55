<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Category view model
 *
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{
    /**
     * Schema for bottom description field
     *
     * @var array
     */
    protected $bottomDescriptionSchema = [
            'bottom_description' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Bottom description',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_TRUSTED_PERMISSION => true,
            \XLite\View\FormField\Textarea\Advanced::PARAM_STYLE => 'category-bottom-description',
            ]
        ];

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $this->schemaDefault = array_merge($this->schemaDefault, $this->bottomDescriptionSchema);
        return $this->getFieldsBySchema($this->schemaDefault);
    }
}
