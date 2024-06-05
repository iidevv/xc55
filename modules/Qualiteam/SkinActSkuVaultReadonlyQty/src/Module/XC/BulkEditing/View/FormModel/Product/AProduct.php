<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\BulkEditing\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\BulkEditing")
 */
class AProduct extends \XC\BulkEditing\View\FormModel\Product\AProduct
{
    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVaultReadonlyQty/form_model/bulk_edit_theme.twig';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVaultReadonlyQty/bulk_edit/style.css';

        return $list;
    }
}
