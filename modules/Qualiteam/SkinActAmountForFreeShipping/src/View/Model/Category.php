<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAmountForFreeShipping\View\Model;

use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\Input\Text\Price;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $schema = [];

        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;

            if ($name === 'memberships') {
                $schema[$this->getAmountShippingSchemaName()] = [
                    self::SCHEMA_CLASS    => $this->getAmountShippingSchemaClass(),
                    self::SCHEMA_LABEL    => $this->getAmountShippingSchemaLabel(),
                    self::SCHEMA_REQUIRED => $this->getAmountShippingSchemaRequired(),
                ];
            }
        }

        $this->schemaDefault = $schema;
    }

    /**
     * Get schema name
     *
     * @return string
     */
    protected function getAmountShippingSchemaName(): string
    {
        return 'amount_shipping';
    }

    /**
     * Get schema class
     *
     * @return string
     */
    protected function getAmountShippingSchemaClass(): string
    {
        return Price::class;
    }

    /**
     * Get schema label
     *
     * @return string
     */
    protected function getAmountShippingSchemaLabel(): string
    {
        return static::t('SkinActAmountForFreeShipping amount for free shipping');
    }

    /**
     * Get schema required
     *
     * @return bool
     */
    protected function getAmountShippingSchemaRequired(): bool
    {
        return false;
    }
}
