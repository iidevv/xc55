<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Attribute extends \XLite\View\Model\Attribute
{
    /**
     * @param array $params
     * @param array $sections
     */
    public function __construct(array $params = [], array $sections = [])
    {
        if (\XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')->isAvailable()) {
            $this->schemaDefault['show_selector'] = [
                static::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel',
                static::SCHEMA_LABEL => 'Show selector',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'type'        => \XLite\Model\Attribute::TYPE_SELECT,
                        'displayMode' => \XLite\Model\Attribute::COLOR_SWATCHES_MODE
                    ]
                ]
            ];
        }

        parent::__construct($params, $sections);
    }
}
