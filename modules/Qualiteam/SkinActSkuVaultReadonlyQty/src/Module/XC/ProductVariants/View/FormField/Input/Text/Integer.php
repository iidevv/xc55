<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\ProductVariants\View\FormField\Input\Text;

use XC\ProductVariants\Model\ProductVariant;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class Integer extends \XC\ProductVariants\View\FormField\Input\Text\Integer
{
    protected function getCommonAttributes()
    {
        $params = parent::getCommonAttributes();

        $product = $this->getVariant() ? $this->getVariant()->getProduct() : null;
        if ($product && !$product->isSkippedFromSync()) {
            $params['disabled'] = true;
        }

        return $params;
    }

    /**
     * @return \XLite\Model\AEntity|null
     */
    protected function getVariant()
    {
        $fieldName = $this->getParam(self::PARAM_NAME);
        $variantId = $this->parseVariantId($fieldName);
        $variant = !is_null($variantId) ? Database::getRepo(ProductVariant::class)->find($variantId) : null;

        return $variant ?? null;
    }

    /**
     * @param string $fieldName
     * @return mixed|null
     */
    protected function parseVariantId(string $fieldName)
    {
        $regex = '/data\[(\d+)\]\[amount\]/m';
        preg_match($regex, $fieldName, $matches);

        return $matches[1] ?? null;
    }

}
