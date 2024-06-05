<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select;

use XLite\View\FormField\Select\CheckboxList\ACheckboxList;

class OperationTypes extends ACheckboxList
{
    const TYPE_CREATE_PRODUCT      = 'create_product';
    const TYPE_UPDATE_PRODUCT      = 'update_product';
    const TYPE_DELETE_PRODUCT_SYNC = 'delete_product_sync';
    const TYPE_ADD_INVENTORY       = 'add_inventory';
    const TYPE_SYNC_INVENTORY      = 'sync_inventory';
    const TYPE_ADD_SALE            = 'add_sale';
    const TYPE_SYNC_SALE           = 'sync_sale';

    const TYPES = [
        self::TYPE_CREATE_PRODUCT      => 'Create product',
        self::TYPE_UPDATE_PRODUCT      => 'Update product',
        self::TYPE_DELETE_PRODUCT_SYNC => 'Delete product sync',
        self::TYPE_ADD_INVENTORY       => 'Add inventory',
        self::TYPE_SYNC_INVENTORY      => 'Sync inventory',
        self::TYPE_ADD_SALE            => 'Add sale',
        self::TYPE_SYNC_SALE           => 'Sync sale',
    ];

    /**
     * @inheritDoc
     */
    protected function getDefaultOptions()
    {
        return self::TYPES;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All operation types');

        return $list;
    }
}
