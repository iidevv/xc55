<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\CDev\Wholesale\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Wholesale")
 */
abstract class SaleDiscount extends \CDev\Sale\View\Model\SaleDiscount
{
    /**
     * Add apply to wholesale field right after SKU
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $applyToWholesale = [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL    => 'Apply sale discount to wholesale prices',
        ];

        $schema = [];
        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;
            if ($name === 'value') {
                $schema['applyToWholesale'] = $applyToWholesale;
            }
        }

        if (!isset($schema['applyToWholesale'])) {
            $schema['applyToWholesale'] = $applyToWholesale;
        }

        $this->schemaDefault = $schema;
    }
}
