<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute options items list
 * @Extender\Mixin
 */
class AttributeOption extends \XLite\View\ItemsList\Model\AttributeOption
{
    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        $result = parent::defineColumns();

        if (\XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')->isAvailable()) {
            $result += [
                'swatch' => [
                    static::COLUMN_CLASS   => 'QSL\ColorSwatches\View\FormField\Inline\Select\Swatch',
                    static::COLUMN_ORDERBY => 150,
                ],
            ];
        }

        return $result;
    }
}
