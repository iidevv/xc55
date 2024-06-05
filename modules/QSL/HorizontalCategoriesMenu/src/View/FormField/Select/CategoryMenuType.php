<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View\FormField\Select;

class CategoryMenuType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Set value.
     *
     * @param mixed $value Value to set
     */
    public function setValue($value)
    {
        $options = $this->getDefaultOptions();
        if (!isset($options[$value])) {
            $value = key($options);
        }

        parent::setValue($value);
    }

    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'catalog'         => static::t('Catalog one-root menu name'),
            'root_categories' => static::t('Root categories'),
        ];
    }
}
