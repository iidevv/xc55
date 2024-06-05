<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

class QuickbooksSettings extends Quickbooks
{
    const OPTIONS = [
        'qbc_enable_sync',
        'qbc_products_sep',
        'qbc_products_add',
        'qbc_products_update_prices',
        'qbc_products_unlink_empty_fname',
        'qbc_products_income_acc',
        'qbc_products_cogs_acc',
        'qbc_products_asset_acc',
        'qbc_orders_sep',
        'qbc_orders_start_id',
        'qbc_orders_class_ref',
        'qbc_orders_template_ref',
        'qbc_orders_discount_ref',
        'qbc_orders_shipping_ref',
        'qbc_orders_import_errors_email',
    ];
}